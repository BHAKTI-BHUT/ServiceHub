<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BookingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookingRequestController extends Controller
{
    /**
     * Display a listing of customer's booking requests.
     */
    public function index(Request $request)
    {
        $requests = $request->user()
            ->bookingRequests()
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'booking_requests' => $requests
        ]);
    }

    /**
     * Store a newly created booking request in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pickup_location'  => 'required|string|max:255',
            'drop_location'    => 'required|string|max:255',
            'phone_number'     => 'nullable|string|max:15',
            'shifting_date'    => 'required|date|after_or_equal:today',
            // shifting_time is optional (app may not show time picker)
            'shifting_time'    => 'nullable|date_format:H:i',
            // lat/lng are optional — used for distance-based price estimation if provided
            'pickup_latitude'  => 'nullable|numeric',
            'pickup_longitude' => 'nullable|numeric',
            'drop_latitude'    => 'nullable|numeric',
            'drop_longitude'   => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        // Calculate estimated amount only if lat/lng are provided
        $estimatedAmount = $this->calculateEstimatedAmount(
            $request->pickup_latitude,
            $request->pickup_longitude,
            $request->drop_latitude,
            $request->drop_longitude
        );

        $bookingRequest = $request->user()->bookingRequests()->create([
            'phone_number'      => $request->phone_number ?? $request->user()->mobile,
            'pickup_location'   => $request->pickup_location,
            'drop_location'     => $request->drop_location,
            'pickup_latitude'   => $request->pickup_latitude,
            'pickup_longitude'  => $request->pickup_longitude,
            'drop_latitude'     => $request->drop_latitude,
            'drop_longitude'    => $request->drop_longitude,
            'shifting_date'     => $request->shifting_date,
            'shifting_time'     => $request->shifting_time,
            'estimated_amount'  => $estimatedAmount,
            'status'            => 'pending',
        ]);

        return response()->json([
            'success'         => true,
            'message'         => 'Booking request submitted successfully!',
            'booking_request' => $bookingRequest,
            'estimated_amount'=> '₹' . number_format($estimatedAmount, 2),
        ], 201);
    }

    /**
     * Helper to calculate estimated amount using Haversine formula
     */
    private function calculateEstimatedAmount($lat1, $lon1, $lat2, $lon2)
    {
        if (is_null($lat1) || is_null($lon1) || is_null($lat2) || is_null($lon2)) {
            return 500.00;
        }

        $earthRadius = 6371; // earth radius in km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c; // distance in km

        // Formula: Base fare of 500 + 50 per KM
        $baseFare = 500;
        $perKmRate = 50;
        $amount = $baseFare + ($distance * $perKmRate);

        return round($amount, 2);
    }
}
