<?php

namespace App\Http\Controllers\Api\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingProof;
use App\Models\SupervisorLocation;
use App\Models\OrderTracking;
use App\Services\FileService;
use App\Services\PricingEngine;
use App\Services\CommissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class SupervisorPortalController extends Controller
{
    protected $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    /**
     * Get bookings list assigned to the authenticated supervisor.
     */
    public function getBookings(Request $request)
    {
        $supervisor = $request->user();
        
        $statusFilter = $request->query('status'); // e.g. active, completed, cancelled

        $query = Booking::with(['customer:id,name,email,mobile'])
            ->where('supervisor_id', $supervisor->id);

        if ($statusFilter === 'active') {
            $query->whereIn('status', ['pending', 'confirmed', 'in_progress']);
        } elseif ($statusFilter === 'completed') {
            $query->where('status', 'completed');
        } elseif ($statusFilter === 'cancelled') {
            $query->where('status', 'cancelled');
        }

        $bookings = $query->orderBy('shifting_date', 'desc')->paginate(15);

        return response()->json([
            'success'  => true,
            'bookings' => $bookings
        ]);
    }

    /**
     * Get booking details.
     */
    public function getBookingDetail(Request $request, $id)
    {
        $supervisor = $request->user();

        $booking = Booking::with([
            'customer:id,name,email,mobile',
            'items',
            'addOns',
            'category:id,name',
            'vehicle:id,name,registration_number',
            'proofs'
        ])
        ->where('supervisor_id', $supervisor->id)
        ->where('id', $id)
        ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found or unauthorized.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'booking' => $booking
        ]);
    }

    /**
     * Start Trip.
     */
    public function startTrip(Request $request, $id)
    {
        $supervisor = $request->user();
        $booking = Booking::where('supervisor_id', $supervisor->id)->where('id', $id)->first();

        if (!$booking) {
            return response()->json(['success' => false, 'message' => 'Booking not found.'], 404);
        }

        if ($booking->supervisor_acceptance_status !== 'accepted') {
            return response()->json(['success' => false, 'message' => 'Please accept assignment before starting trip.'], 400);
        }

        $booking->update([
            'tracking_status' => 'trip_started'
        ]);

        OrderTracking::create([
            'booking_id' => $booking->id,
            'status'     => 'trip_started',
            'notes'      => 'Supervisor started the trip. Vehicle departed.',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Trip started successfully!',
            'tracking_status' => 'trip_started'
        ]);
    }

    /**
     * Start Shifting.
     */
    public function startShifting(Request $request, $id)
    {
        $supervisor = $request->user();
        $booking = Booking::where('supervisor_id', $supervisor->id)->where('id', $id)->first();

        if (!$booking) {
            return response()->json(['success' => false, 'message' => 'Booking not found.'], 404);
        }

        $booking->update([
            'status'          => 'in_progress',
            'tracking_status' => 'shifting_started'
        ]);

        OrderTracking::create([
            'booking_id' => $booking->id,
            'status'     => 'shifting_started',
            'notes'      => 'Supervisor started the shifting process.',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Shifting started successfully!',
            'status'  => 'in_progress',
            'tracking_status' => 'shifting_started'
        ]);
    }

    /**
     * Complete Shifting (validate remaining payment is Paid!).
     */
    public function completeShifting(Request $request, $id)
    {
        $supervisor = $request->user();
        $booking = Booking::where('supervisor_id', $supervisor->id)->where('id', $id)->first();

        if (!$booking) {
            return response()->json(['success' => false, 'message' => 'Booking not found.'], 404);
        }

        if ($booking->status !== 'in_progress') {
            return response()->json(['success' => false, 'message' => 'Shifting must be in progress before marking as completed.'], 400);
        }

        if ($booking->remaining_payment_status !== 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot complete shifting. Remaining payment of ₹' . number_format($booking->remaining_amount, 2) . ' is pending from customer.'
            ], 400);
        }

        // Deduct 20% commission from vendor wallet
        $commissionService = new CommissionService();
        $commissionService->deductCommission($booking);

        $booking->update([
            'status'          => 'completed',
            'tracking_status' => 'completed'
        ]);

        OrderTracking::create([
            'booking_id' => $booking->id,
            'status'     => 'completed',
            'notes'      => 'Shifting completed by supervisor. 20% commission deducted from vendor wallet.',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Shifting completed successfully!',
            'status'  => 'completed',
            'tracking_status' => 'completed'
        ]);
    }

    /**
     * Upload Photo Proofs (supports multiple photo files).
     */
    public function uploadProof(Request $request, $id)
    {
        $supervisor = $request->user();
        $booking = Booking::where('supervisor_id', $supervisor->id)->where('id', $id)->first();

        if (!$booking) {
            return response()->json(['success' => false, 'message' => 'Booking not found.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'images'   => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120', // Max 5MB per file
            'type'     => 'required|string|in:pickup,delivery,general',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $proofs = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $imageFile) {
                // Upload image using FileService
                $filePath = $this->fileService->upload($imageFile, 'uploads/booking_proofs', null, 'proof');
                
                // Create proof database record
                $proof = BookingProof::create([
                    'booking_id' => $booking->id,
                    'file_path'  => $filePath,
                    'type'       => $request->type
                ]);

                $proofs[] = $proof;
            }

            // Create tracking logs
            OrderTracking::create([
                'booking_id' => $booking->id,
                'status'     => $booking->tracking_status,
                'notes'      => 'Supervisor uploaded ' . count($proofs) . ' photo proof(s) (' . ucfirst($request->type) . ').',
            ]);
        }

        // Auto update tracking status to pickup_completed if type is pickup
        if ($request->type === 'pickup' && $booking->tracking_status !== 'pickup_completed') {
            $booking->update(['tracking_status' => 'pickup_completed']);
            OrderTracking::create([
                'booking_id' => $booking->id,
                'status'     => 'pickup_completed',
                'notes'      => 'Booking status advanced to Pickup Completed after proof upload.',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Photo proofs uploaded successfully!',
            'proofs'  => $proofs,
            'tracking_status' => $booking->fresh()->tracking_status
        ]);
    }

    /**
     * Update Live Location (Rapido tracker).
     */
    public function updateLocation(Request $request)
    {
        $supervisor = $request->user();

        $validator = Validator::make($request->all(), [
            'latitude'   => 'required|numeric|between:-90,90',
            'longitude'  => 'required|numeric|between:-180,180',
            'booking_id' => 'nullable|exists:bookings,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // 1. Update latest coordinates on User profile
        $supervisor->update([
            'latitude'  => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        // 2. Save historical trace record
        $location = SupervisorLocation::create([
            'supervisor_id' => $supervisor->id,
            'booking_id'    => $request->booking_id,
            'latitude'      => $request->latitude,
            'longitude'     => $request->longitude,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Live location updated successfully.',
            'location'=> $location
        ]);
    }

    /**
     * Update items and recalculate price (live price calculation).
     */
    public function updateItems(Request $request, $id)
    {
        $supervisor = $request->user();
        $booking = Booking::where('supervisor_id', $supervisor->id)->where('id', $id)->first();

        if (!$booking) {
            return response()->json(['success' => false, 'message' => 'Booking not found.'], 404);
        }

        if ($booking->status !== 'in_progress') {
            return response()->json(['success' => false, 'message' => 'Items can only be updated during active shifting.'], 400);
        }

        $validator = Validator::make($request->all(), [
            'items'            => 'nullable|array',
            'items.*.id'       => 'exists:items,id',
            'items.*.quantity' => 'integer|min:0|max:50',
            'addons'           => 'nullable|array',
            'addons.*'         => 'exists:add_ons,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $engine = new PricingEngine();
        $quote = $engine->calculateQuote([
            'items'           => array_filter($request->input('items', []), fn($i) => ($i['quantity'] ?? 0) > 0),
            'addons'          => $request->input('addons', []),
            'pickup_latitude' => $booking->pickup_latitude,
            'pickup_longitude'=> $booking->pickup_longitude,
            'drop_latitude'   => $booking->drop_latitude,
            'drop_longitude'  => $booking->drop_longitude,
            'shifting_date'   => $booking->shifting_date,
            'floors'          => $booking->floors ?? 0,
        ]);

        if ($quote['survey_required']) {
            return response()->json(['success' => false, 'message' => 'Updated items exceed automatic pricing threshold.'], 422);
        }

        DB::transaction(function () use ($booking, $quote) {
            $extraCharges = ($booking->loading_charge ?? 0)
                + ($booking->unloading_charge ?? 0)
                + ($booking->packing_charge ?? 0)
                + ($booking->labour_charge ?? 0);

            $grandTotal   = $quote['total_amount'] + $extraCharges;
            $advanceAmount = $booking->advance_amount;
            $remaining    = max(0, $grandTotal - $advanceAmount);

            $booking->update([
                'total_volume_score' => $quote['total_volume_score'],
                'category_id'        => $quote['category_id'],
                'vehicle_id'         => $quote['vehicle_id'],
                'total_distance'     => $quote['total_distance_km'],
                'base_fare'          => $quote['base_fare'],
                'point_based_fare'   => $quote['point_based_fare'] ?? 0,
                'distance_charges'   => $quote['distance_charges'],
                'addon_charges'      => $quote['addon_charges'],
                'floor_charges'      => $quote['floor_charges'],
                'weekend_charges'    => $quote['weekend_charges'],
                'month_end_charges'  => $quote['month_end_charges'],
                'amount'             => $grandTotal,
                'remaining_amount'   => $remaining,
            ]);

            // Sync items
            $itemsSync = [];
            foreach ($quote['items_breakdown'] as $item) {
                $itemsSync[$item['id']] = [
                    'quantity'                => $item['quantity'],
                    'calculated_volume_score' => $item['line_score'],
                ];
            }
            $booking->items()->sync($itemsSync);

            // Sync addons
            $addonsSync = [];
            foreach ($quote['addons_breakdown'] as $addon) {
                $addonsSync[$addon['id']] = ['price' => $addon['price']];
            }
            $booking->addOns()->sync($addonsSync);

            OrderTracking::create([
                'booking_id' => $booking->id,
                'status'     => 'items_updated',
                'notes'      => 'Items updated by supervisor API. New total: ₹' . number_format($grandTotal, 2),
            ]);
        });

        return response()->json([
            'success'   => true,
            'message'   => 'Booking items updated successfully!',
            'new_total' => $booking->fresh()->amount,
            'remaining' => $booking->fresh()->remaining_amount
        ]);
    }
}
