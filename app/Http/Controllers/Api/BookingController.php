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
}
