<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use App\Models\BookingVendorRequest;
use App\Models\OrderTracking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VendorPortalController extends Controller
{
    /**
     * Get dashboard stats for the logged-in vendor.
     */
    public function getDashboardStats(Request $request)
    {
        $vendor = $request->user();

        $stats = [
            'total_bookings'     => Booking::where('vendor_id', $vendor->id)->count(),
            'pending_requests'   => Booking::where('vendor_id', $vendor->id)->where('vendor_acceptance_status', 'pending')->count(),
            'accepted_bookings'  => Booking::where('vendor_id', $vendor->id)->where('vendor_acceptance_status', 'accepted')->count(),
            'completed_bookings' => Booking::where('vendor_id', $vendor->id)->where('status', 'completed')->count(),
            'pending_bookings'   => Booking::where('vendor_id', $vendor->id)->where('status', 'pending')->count(),
            'confirmed_bookings' => Booking::where('vendor_id', $vendor->id)->where('status', 'confirmed')->count(),
            'cancelled_bookings' => Booking::where('vendor_id', $vendor->id)->where('status', 'cancelled')->count(),
            'total_revenue'      => (float) Booking::where('vendor_id', $vendor->id)->where('status', 'completed')->sum('vendor_settlement_amount'),
            'pending_revenue'    => (float) Booking::where('vendor_id', $vendor->id)->where('status', '!=', 'cancelled')->where('remaining_payment_status', 'pending')->sum('remaining_amount'),
        ];

        return response()->json([
            'success' => true,
            'stats'   => $stats,
        ]);
    }

    /**
     * Get the listing of bookings assigned to this vendor.
     */
    public function getBookings(Request $request)
    {
        $vendor = $request->user();

        // Get filter from request: e.g. status = pending_requests, accepted, completed, etc.
        $statusFilter = $request->query('filter');

        $query = Booking::with(['customer:id,name,email,mobile', 'supervisor:id,name,email,mobile'])
            ->join('booking_vendor_requests', 'bookings.id', '=', 'booking_vendor_requests.booking_id')
            ->where('booking_vendor_requests.vendor_id', $vendor->id)
            ->select('bookings.*', 'booking_vendor_requests.status as vendor_req_status')
            ->orderBy('booking_vendor_requests.created_at', 'desc');

        if ($statusFilter === 'pending') {
            $query->where('booking_vendor_requests.status', 'pending');
        } elseif ($statusFilter === 'accepted') {
            $query->where('booking_vendor_requests.status', 'accepted');
        } elseif ($statusFilter === 'rejected') {
            $query->where('booking_vendor_requests.status', 'rejected');
        }

        $bookings = $query->paginate(15);

        return response()->json([
            'success'  => true,
            'bookings' => $bookings,
        ]);
    }

    /**
     * Get detailed info of a specific vendor booking.
     */
    public function getBookingDetail(Request $request, $id)
    {
        $vendor = $request->user();

        $booking = Booking::with([
            'customer:id,name,email,mobile',
            'supervisor:id,name,email,mobile',
            'items',
            'addOns',
            'category:id,name',
            'vehicle:id,name,registration_number'
        ])
        ->where('vendor_id', $vendor->id)
        ->where('id', $id)
        ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found or unauthorized.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'booking' => $booking,
        ]);
    }

    /**
     * Respond (Accept/Reject) to a booking request.
     */
    public function respondToBooking(Request $request, $id)
    {
        $vendor = $request->user();

        $booking = Booking::where('vendor_id', $vendor->id)
            ->where('id', $id)
            ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found or unauthorized.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:accepted,rejected',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $responseStatus = $request->status;

        // Update the pivot request status
        BookingVendorRequest::where('booking_id', $booking->id)
            ->where('vendor_id', $vendor->id)
            ->update(['status' => $responseStatus]);

        $booking->vendor_acceptance_status = $responseStatus;
        if ($responseStatus === 'accepted') {
            $booking->status = 'confirmed';
            $booking->tracking_status = 'confirmed';
        } else {
            $booking->status = 'pending'; // revert to pending to allow admin to assign another vendor
            $booking->tracking_status = 'pending_confirmation';
        }
        $booking->save();

        // Track in order history
        OrderTracking::create([
            'booking_id' => $booking->id,
            'status'     => ($responseStatus === 'accepted') ? 'confirmed' : 'rejected',
            'notes'      => 'Vendor ' . ($responseStatus === 'accepted' ? 'accepted' : 'rejected') . ' the booking request via Mobile App.',
        ]);

        return response()->json([
            'success'                  => true,
            'message'                  => 'Booking request ' . ($responseStatus === 'accepted' ? 'accepted!' : 'rejected!'),
            'vendor_acceptance_status' => $booking->vendor_acceptance_status,
            'booking_status'           => $booking->status,
        ]);
    }

    /**
     * Get listing of active supervisors linked to this vendor.
     */
    public function getSupervisors(Request $request)
    {
        $vendor = $request->user();

        $supervisors = $vendor->supervisors()
            ->where('vendor_supervisors.status', 'active')
            ->select('users.id', 'users.name', 'users.email', 'users.mobile')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success'     => true,
            'supervisors' => $supervisors,
        ]);
    }

    /**
     * Assign a supervisor to a booking.
     */
    public function assignSupervisor(Request $request, $id)
    {
        $vendor = $request->user();

        $booking = Booking::where('vendor_id', $vendor->id)
            ->where('id', $id)
            ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found or unauthorized.',
            ], 404);
        }

        if ($booking->vendor_acceptance_status !== 'accepted') {
            return response()->json([
                'success' => false,
                'message' => 'You must accept the booking before assigning a supervisor.',
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'supervisor_id' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $supervisorId = $request->supervisor_id ?: null;

        // If assigning, verify supervisor belongs to this vendor
        if ($supervisorId) {
            $isLinked = $vendor->supervisors()
                ->where('users.id', $supervisorId)
                ->where('vendor_supervisors.status', 'active')
                ->exists();

            if (!$isLinked) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected supervisor is not linked to your vendor account.',
                ], 400);
            }
        }

        $booking->supervisor_id = $supervisorId;
        $booking->supervisor_acceptance_status = $supervisorId ? 'pending' : null;
        $booking->save();

        if ($supervisorId) {
            $supervisorName = User::find($supervisorId)->name;
            $notes = 'Vendor assigned supervisor: ' . $supervisorName . ' via Mobile App.';
        } else {
            $notes = 'Vendor unassigned supervisor via Mobile App.';
        }

        OrderTracking::create([
            'booking_id' => $booking->id,
            'status'     => $booking->status,
            'notes'      => $notes,
        ]);

        return response()->json([
            'success'                      => true,
            'message'                      => $supervisorId ? 'Supervisor assigned successfully!' : 'Supervisor unassigned successfully!',
            'supervisor_id'                => $booking->supervisor_id,
            'supervisor_acceptance_status' => $booking->supervisor_acceptance_status,
        ]);
    }
}
