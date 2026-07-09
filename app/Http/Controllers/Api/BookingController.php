<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    /**
     * Get metadata and configuration needed to initialize a booking.
     */
    public function initData()
    {
        $itemSizes = \App\Models\ItemSize::where('status', 'active')
            ->with(['items' => function ($query) {
                $query->where('status', 'active');
            }])->get();

        $addons = \App\Models\AddOnCategory::with(['addons' => function($q) {
            $q->where('status', '1');
        }])->where('status', 'active')->get();

        $pricingSettings = \App\Models\PricingSetting::whereIn('key', [
            'per_km_rate',
            'per_floor_charge',
            'weekend_surge_percentage',
            'month_end_surge_percentage',
            'peak_time_surge_percentage',
            'peak_time_start',
            'peak_time_end',
            'advance_payment_percentage',
            'registration_fee',
        ])->get()->keyBy('key');

        $pricingConfig = [
            'per_km_rate' => (float) ($pricingSettings->get('per_km_rate')->value ?? 20),
            'per_floor_charge' => (float) ($pricingSettings->get('per_floor_charge')->value ?? 150),
            'weekend_surge_percentage' => (float) ($pricingSettings->get('weekend_surge_percentage')->value ?? 10),
            'month_end_surge_percentage' => (float) ($pricingSettings->get('month_end_surge_percentage')->value ?? 15),
            'peak_time_surge_percentage' => (float) ($pricingSettings->get('peak_time_surge_percentage')->value ?? 0),
            'peak_time_start' => $pricingSettings->get('peak_time_start')->value ?? '20:00',
            'peak_time_end' => $pricingSettings->get('peak_time_end')->value ?? '23:00',
            'advance_payment_percentage' => 0.0,
            'registration_fee' => (float) ($pricingSettings->get('registration_fee')->value ?? 500),
        ];

        return response()->json([
            'success' => true,
            'item_sizes' => $itemSizes,
            'addons' => $addons,
            'pricing_config' => $pricingConfig
        ]);
    }

    /**
     * Get just the dynamic registration fee amount.
     */
    public function getRegistrationFee()
    {
        $regFeeSetting = \App\Models\PricingSetting::where('key', 'registration_fee')->first();
        $fee = (float) ($regFeeSetting->value ?? 500);

        return response()->json([
            'success' => true,
            'registration_fee' => $fee,
            'currency' => 'INR'
        ]);
    }

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
            ->with(['vendor:id,name,email,mobile,image,phone', 'supervisor:id,name,email,mobile,image,phone'])
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
        $advanceAmount          = 0.00;

        // Create Razorpay Order for Registration Charge (dynamic from settings)
        $keyId = config('services.razorpay.key_id');
        $keySecret = config('services.razorpay.key_secret');
        $regFeeSetting = \App\Models\PricingSetting::where('key', 'registration_fee')->first();
        $registrationCharge = (float) ($regFeeSetting->value ?? 500);
        $razorpayOrderId = null;

        if ($keyId && $keySecret) {
            try {
                $response = \Illuminate\Support\Facades\Http::withBasicAuth($keyId, $keySecret)
                    ->post('https://api.razorpay.com/v1/orders', [
                        'amount' => (int) ($registrationCharge * 100), // 50000 paise
                        'currency' => 'INR',
                        'receipt' => 'reg_booking_' . time() . '_' . rand(100, 999),
                    ]);

                if ($response->successful()) {
                    $razorpayOrderId = $response->json()['id'] ?? null;
                } else {
                    \Log::error('Razorpay Order Creation Failed: ' . $response->body());
                }
            } catch (\Exception $e) {
                \Log::error('Razorpay Order Exception: ' . $e->getMessage());
            }
        }

        \DB::beginTransaction();
        try {
        // Check if a pending booking already exists for this customer on the same shifting date
        $existingBooking = \App\Models\Booking::where('customer_id', $customerId)
            ->where('shifting_date', $validated['shifting_date'])
            ->where('status', 'pending')
            ->first();

        if ($existingBooking) {
            // Update the existing booking instead of creating a new one
            $booking = $existingBooking;
            // Update core fields
            $booking->update([
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
                'shifting_time'            => $validated['shifting_time'] ? date('H:i:s', strtotime($validated['shifting_time'])) : null,
                'floors'                   => $validated['floors'] ?? 0,
                // PricingEngine output (same as creation)
                'total_volume_score'       => $quote['total_volume_score'] ?? null,
                'category_id'              => $quote['category_id'] ?? null,
                'vehicle_id'               => $quote['vehicle_id'] ?? null,
                'total_distance'           => $quote['total_distance_km'] ?? null,
                'base_fare'                => $quote['base_fare'] ?? null,
                'distance_charges'         => $quote['distance_charges'] ?? null,
                'addon_charges'            => $quote['addon_charges'] ?? null,
                'floor_charges'            => $quote['floor_charges'] ?? null,
                'weekend_charges'          => $quote['weekend_charges'] ?? null,
                'month_end_charges'        => $quote['month_end_charges'] ?? null,
                'loading_charge'           => $loadingCharge,
                'unloading_charge'        => $unloadingCharge,
                'packing_charge'           => $packingCharge,
                'labour_charge'            => $labourCharge,
                'amount'                   => $grandTotal,
                // Payment breakdown (keep registration fields unchanged)
                'remaining_amount'         => $grandTotal,
                'vendor_commission_amount'=> $vendorCommissionAmount,
                'vendor_settlement_amount'=> $grandTotal - $vendorCommissionAmount,
            ]);

            // Sync items
            $syncItems = [];
            if (!empty($validated['items'])) {
                foreach ($validated['items'] as $itemInput) {
                    $lineScore = 0;
                    if (!empty($quote['items_breakdown'])) {
                        foreach ($quote['items_breakdown'] as $breakdown) {
                            if ($breakdown['id'] == $itemInput['id']) {
                                $lineScore = $breakdown['line_score'] ?? 0;
                                break;
                            }
                        }
                    }
                    $syncItems[$itemInput['id']] = [
                        'quantity' => $itemInput['quantity'],
                        'calculated_volume_score' => $lineScore,
                    ];
                }
            }
            $booking->items()->sync($syncItems);

            // Sync add-ons
            $syncAddons = [];
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
                    $syncAddons[$addonId] = ['price' => $addonPrice];
                }
            }
            $booking->addOns()->sync($syncAddons);
        } else {
            // Create a brand‑new booking (original logic)
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
                'shifting_time'            => $validated['shifting_time'] ? date('H:i:s', strtotime($validated['shifting_time'])) : null,
                'floors'                   => $validated['floors'] ?? 0,
                'status'                   => 'pending',
                // PricingEngine output
                'total_volume_score'       => $quote['total_volume_score'] ?? null,
                'category_id'             => $quote['category_id'] ?? null,
                'vehicle_id'               => $quote['vehicle_id'] ?? null,
                'total_distance'           => $quote['total_distance_km'] ?? null,
                'base_fare'                => $quote['base_fare'] ?? null,
                'distance_charges'        => $quote['distance_charges'] ?? null,
                'addon_charges'           => $quote['addon_charges'] ?? null,
                'floor_charges'           => $quote['floor_charges'] ?? null,
                'weekend_charges'         => $quote['weekend_charges'] ?? null,
                'month_end_charges'       => $quote['month_end_charges'] ?? null,
                'loading_charge'          => $loadingCharge,
                'unloading_charge'         => $unloadingCharge,
                'packing_charge'          => $packingCharge,
                'labour_charge'           => $labourCharge,
                'amount'                  => $grandTotal,
                // Payment breakdown
                'advance_amount'          => 0.00,
                'remaining_amount'        => $grandTotal,
                'advance_payment_status'  => 'paid',
                'remaining_payment_status'=> 'pending',
                // Vendor settlement
                'vendor_commission_amount'=> $vendorCommissionAmount,
                'vendor_settlement_amount'=> $grandTotal - $vendorCommissionAmount,
                // Registration Payment fields
                'registration_charge'      => $registrationCharge,
                'registration_payment_status'=> 'pending',
                'registration_order_id'   => $razorpayOrderId,
            ]);

            // Attach items
            if (!empty($validated['items'])) {
                foreach ($validated['items'] as $itemInput) {
                    $lineScore = 0;
                    if (!empty($quote['items_breakdown'])) {
                        foreach ($quote['items_breakdown'] as $breakdown) {
                            if ($breakdown['id'] == $itemInput['id']) {
                                $lineScore = $breakdown['line_score'] ?? 0;
                                break;
                            }
                        }
                    }
                    $booking->items()->attach($itemInput['id'], [
                        'quantity' => $itemInput['quantity'],
                        'calculated_volume_score' => $lineScore,
                    ]);
                }
            }

            // Attach add‑ons
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
            'registration_order_id' => $booking->registration_order_id,
            'registration_charge' => $booking->registration_charge,
            'quote' => $quote,
        ]);
    }

    /**
     * Update an existing pending booking by the customer.
     */
    public function update(Request $request, $id)
    {
        $booking = $request->user()->bookings()->find($id);

        if (!$booking) {
            return response()->json(['success' => false, 'message' => 'Booking not found.'], 404);
        }

        if ($booking->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Booking cannot be edited at this stage. Only pending bookings can be updated.'
            ], 400);
        }

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
            // Items and Add-ons
            'items'                 => 'nullable|array',
            'items.*.id'            => 'required_with:items|exists:items,id',
            'items.*.quantity'      => 'required_with:items|integer|min:1',
            'addon_ids'             => 'nullable|array',
            'addon_ids.*'           => 'exists:add_ons,id',
        ]);

        $validated['customer_id'] = $booking->customer_id;

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

        \DB::beginTransaction();
        try {
            $booking->update([
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
                'shifting_time'            => $validated['shifting_time'] ? date('H:i:s', strtotime($validated['shifting_time'])) : null,
                'floors'                   => $validated['floors'] ?? 0,
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
                // Payment update
                'remaining_amount' => $grandTotal, // Registration fee doesn't change, we just update the total amount
                'vendor_commission_amount' => $vendorCommissionAmount,
                'vendor_settlement_amount' => $grandTotal - $vendorCommissionAmount,
            ]);

            // Sync items
            $syncItems = [];
            if (!empty($validated['items'])) {
                foreach ($validated['items'] as $itemInput) {
                    $lineScore = 0;
                    if (!empty($quote['items_breakdown'])) {
                        foreach ($quote['items_breakdown'] as $breakdown) {
                            if ($breakdown['id'] == $itemInput['id']) {
                                $lineScore = $breakdown['line_score'] ?? 0;
                                break;
                            }
                        }
                    }
                    $syncItems[$itemInput['id']] = [
                        'quantity'                => $itemInput['quantity'],
                        'calculated_volume_score' => $lineScore,
                    ];
                }
            }
            $booking->items()->sync($syncItems);

            // Sync add-ons
            $syncAddons = [];
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
                    $syncAddons[$addonId] = ['price' => $addonPrice];
                }
            }
            $booking->addOns()->sync($syncAddons);

            \DB::commit();
        } catch (\Throwable $e) {
            \DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to update booking: ' . $e->getMessage()], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Booking updated successfully',
            'booking_id' => $booking->id,
            'quote' => $quote,
        ]);
    }

    public function verifyRegistrationPayment(Request $request, $id)
    {
        $booking = $request->user()->bookings()->find($id);

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found.'
            ], 404);
        }

        $validated = $request->validate([
            'razorpay_payment_id' => 'required|string',
            'razorpay_order_id'   => 'required|string',
            'razorpay_signature'  => 'required|string',
        ]);

        $keySecret = config('services.razorpay.key_secret');

        // Verify Razorpay Signature
        $expectedSignature = hash_hmac(
            'sha256',
            $validated['razorpay_order_id'] . '|' . $validated['razorpay_payment_id'],
            $keySecret
        );

        if (hash_equals($expectedSignature, $validated['razorpay_signature'])) {
            $booking->update([
                'status'                      => 'confirmed',
                'tracking_status'             => 'confirmed',
                'registration_payment_status' => 'paid',
                'registration_payment_id'     => $validated['razorpay_payment_id'],
            ]);

            \App\Models\OrderTracking::create([
                'booking_id' => $booking->id,
                'status' => 'confirmed',
                'notes' => 'Registration payment of Rs. ' . $booking->registration_charge . ' verified. Booking confirmed.',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Registration payment verified successfully. Booking status updated to confirmed.',
                'booking' => $booking
            ]);
        }

        $booking->update([
            'registration_payment_status' => 'failed'
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Invalid payment signature verification failed.'
        ], 400);
    }

    /**
     * Get the assigned vendor and supervisor details for a booking.
     */
    public function assignedVendor(Request $request, $id)
    {
        $booking = $request->user()->bookings()
            ->with(['vendor:id,name,email,mobile,image,phone', 'supervisor:id,name,email,mobile,image,phone'])
            ->find($id);

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found.'
            ], 404);
        }

        if (!$booking->vendor_id) {
            return response()->json([
                'success' => true,
                'message' => 'No vendor assigned to this booking yet.',
                'vendor' => null,
                'supervisor' => null,
            ]);
        }

        return response()->json([
            'success' => true,
            'booking_id' => $booking->id,
            'booking_number' => $booking->booking_number,
            'vendor_acceptance_status' => $booking->vendor_acceptance_status,
            'vendor' => $booking->vendor,
            'supervisor' => $booking->supervisor,
        ]);
    }
}
