<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    /**
     * Display booking history of the authenticated user.
     */
    public function index(Request $request)
    {
        $bookings = $request->user()
            ->bookings()
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'bookings' => $bookings
        ]);
    }

    /**
     * Display a specific booking detail.
     */
    public function show(Request $request, $id)
    {
        $booking = $request->user()
            ->bookings()
            ->where('id', $id)
            ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'booking' => $booking
        ]);
    }

    /**
     * Cancel a booking by the customer.
     */
    public function cancel(Request $request, $id)
    {
        $booking = $request->user()
            ->bookings()
            ->where('id', $id)
            ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found.'
            ], 404);
        }

        // Only allow cancel if booking is pending or confirmed
        if (!in_array($booking->status, ['pending', 'confirmed'])) {
            return response()->json([
                'success' => false,
                'message' => 'Booking cannot be cancelled at this stage. Please contact support.'
            ], 400);
        }

        $booking->update(['status' => 'cancelled']);

        return response()->json([
            'success' => true,
            'message' => 'Booking cancelled successfully!',
            'booking' => $booking
        ]);
    }

    /**
     * Estimate booking cost without creating a booking.
     * Accepts same payload as store but returns pricing breakdown.
     */
    public function estimate(Request $request)
    {
        // For estimation, only location, date, and shifting info are required.
        // Contact details and customer_id are NOT needed just to calculate a price.
        $validated = $request->validate([
            'customer_id'           => 'nullable|exists:users,id',
            'pickup_location'       => 'required|string',
            'drop_location'         => 'required|string',
            'pickup_latitude'       => 'required|numeric',
            'pickup_longitude'      => 'required|numeric',
            'drop_latitude'         => 'required|numeric',
            'drop_longitude'        => 'required|numeric',
            'pickup_contact_name'   => 'nullable|string',
            'pickup_contact_mobile' => 'nullable|string',
            'drop_contact_name'     => 'nullable|string',
            'drop_contact_mobile'   => 'nullable|string',
            'shifting_date'         => 'required|date',
            'shifting_time'         => 'nullable|string',
            // Optional: items and add-on IDs if passed by the app
            'items'                 => 'nullable|array',
            'items.*.id'            => 'required_with:items|exists:items,id',
            'items.*.quantity'      => 'required_with:items|integer|min:1',
            'addon_ids'             => 'nullable|array',
            'addon_ids.*'           => 'exists:add_ons,id',
            'floors'                => 'nullable|integer|min:0',
        ]);

        // Use the same pricing engine logic as backend store
        $pricingEngine = new \App\Services\PricingEngine();
        $quote = $pricingEngine->calculateQuote($validated);

        return response()->json([
            'success' => true,
            'quote' => $quote,
        ]);
    }

    /**
     * Store a new booking.
     * Mirrors backend booking creation but returns JSON for API clients.
     */
    public function store(Request $request)
    {
        // customer_id always comes from the authenticated user — never from the request body
        $customerId = $request->user()->id;

        $validated = $request->validate([
            'pickup_location'       => 'required|string',
            'drop_location'         => 'required|string',
            'pickup_latitude'       => 'required|numeric',
            'pickup_longitude'      => 'required|numeric',
            'drop_latitude'         => 'required|numeric',
            'drop_longitude'        => 'required|numeric',
            'pickup_contact_name'   => 'nullable|string',
            'pickup_contact_mobile' => 'nullable|string',
            'drop_contact_name'     => 'nullable|string',
            'drop_contact_mobile'   => 'nullable|string',
            'shifting_date'         => 'required|date',
            'shifting_time'         => 'nullable|string',
            'floors'                => 'nullable|integer|min:0',
            // Items selected by user (from /api/items)
            'items'                 => 'nullable|array',
            'items.*.id'            => 'required_with:items|exists:items,id',
            'items.*.quantity'      => 'required_with:items|integer|min:1',
            // Add-ons selected by user (from /api/addons)
            'addon_ids'             => 'nullable|array',
            'addon_ids.*'           => 'exists:add_ons,id',
        ]);

        $validated['customer_id'] = $customerId;

        $pricingEngine = new \App\Services\PricingEngine();
        $quote = $pricingEngine->calculateQuote($validated);

        $loadingCharge   = $quote['loading_charge']   ?? 0;
        $unloadingCharge = $quote['unloading_charge'] ?? 0;
        $packingCharge   = $quote['packing_charge']   ?? 0;
        $labourCharge    = $quote['labour_charge']    ?? 0;

        $extraChargesTotal      = $loadingCharge + $unloadingCharge + $packingCharge + $labourCharge;
        $grandTotal             = $quote['total_amount'] + $extraChargesTotal;
        $vendorCommissionPct    = 15;
        $vendorCommissionAmount = round($grandTotal * ($vendorCommissionPct / 100), 2);
        $advanceAmount          = $quote['advance_amount'] ?? round($grandTotal * 0.20, 2);

        \DB::beginTransaction();
        try {
            $booking = \App\Models\Booking::create([
                'customer_id'              => $validated['customer_id'],
                'pickup_location'          => $validated['pickup_location'],
                'drop_location'            => $validated['drop_location'],
                'pickup_latitude'          => $validated['pickup_latitude'],
                'pickup_longitude'         => $validated['pickup_longitude'],
                'drop_latitude'            => $validated['drop_latitude'],
                'drop_longitude'           => $validated['drop_longitude'],
                'pickup_contact_name'      => $validated['pickup_contact_name'] ?? null,
                'pickup_contact_mobile'    => $validated['pickup_contact_mobile'] ?? null,
                'drop_contact_name'        => $validated['drop_contact_name'] ?? null,
                'drop_contact_mobile'      => $validated['drop_contact_mobile'] ?? null,
                'shifting_date'            => $validated['shifting_date'],
                'shifting_time'            => $validated['shifting_time'] ?? null,
                'floors'                   => $validated['floors'] ?? 0,
                'status'                   => 'pending',
                // PricingEngine output
                'total_volume_score' => $quote['total_volume_score'] ?? null,
                'category_id' => $quote['category_id'] ?? null,
                'vehicle_id' => $quote['vehicle_id'] ?? null,
                'total_distance' => $quote['total_distance_km'] ?? null,
                'base_fare' => $quote['base_fare'] ?? null,
                'distance_charges' => $quote['distance_charges'] ?? null,
                'addon_charges' => $quote['addon_charges'] ?? null,
                'floor_charges' => $quote['floor_charges'] ?? null,
                'weekend_charges' => $quote['weekend_charges'] ?? null,
                'month_end_charges' => $quote['month_end_charges'] ?? null,
                'loading_charge' => $loadingCharge,
                'unloading_charge' => $unloadingCharge,
                'packing_charge' => $packingCharge,
                'labour_charge' => $labourCharge,
                'amount' => $grandTotal,
                // Payment breakdown
                'advance_amount' => $advanceAmount,
                'remaining_amount' => $grandTotal - $advanceAmount,
                'advance_payment_status' => 'pending',
                'remaining_payment_status' => 'pending',
                // Vendor settlement
                'vendor_commission_amount' => $vendorCommissionAmount,
                'vendor_settlement_amount' => $grandTotal - $vendorCommissionAmount,
            ]);

            // Attach items from the request (user-selected items)
            if (!empty($validated['items'])) {
                foreach ($validated['items'] as $itemInput) {
                    $lineScore = 0;
                    // Try to find the line score from PricingEngine breakdown if available
                    if (!empty($quote['items_breakdown'])) {
                        foreach ($quote['items_breakdown'] as $breakdown) {
                            if ($breakdown['id'] == $itemInput['id']) {
                                $lineScore = $breakdown['line_score'] ?? 0;
                                break;
                            }
                        }
                    }
                    $booking->items()->attach($itemInput['id'], [
                        'quantity'                => $itemInput['quantity'],
                        'calculated_volume_score' => $lineScore,
                    ]);
                }
            }

            // Attach add-ons selected by user
            if (!empty($validated['addon_ids'])) {
                foreach ($validated['addon_ids'] as $addonId) {
                    $addonPrice = 0;
                    if (!empty($quote['addons_breakdown'])) {
                        foreach ($quote['addons_breakdown'] as $addonData) {
                            if ($addonData['id'] == $addonId) {
                                $addonPrice = $addonData['price'] ?? 0;
                                break;
                            }
                        }
                    }
                    $booking->addOns()->attach($addonId, ['price' => $addonPrice]);
                }
            }

            \DB::commit();
        } catch (\Throwable $e) {
            \DB::rollBack();
            return response()->json(['message' => 'Failed to create booking: ' . $e->getMessage()], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Booking created successfully',
            'booking_id' => $booking->id,
            'quote' => $quote,
        ]);
    }

    // Existing cancel method continues below
}
