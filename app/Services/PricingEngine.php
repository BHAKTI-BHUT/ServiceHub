<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Item;
use App\Models\AddOn;
use App\Models\Vehicle;
use App\Models\PricingSetting;
use Carbon\Carbon;

class PricingEngine
{
    /**
     * Survey Required threshold — score above this means no auto-quote can be given.
     * Applies to 6 BHK+, Villas, Duplex, Commercial Offices, Warehouses, Factories etc.
     */
    const SURVEY_REQUIRED_THRESHOLD = 310;

    /**
     * Calculate comprehensive pricing quote.
     *
     * Expected $data format:
     * [
     *    'items'             => [ ['id' => 1, 'quantity' => 2], ... ],
     *    'addons'            => [1, 3, 5],           // addon IDs
     *    'pickup_latitude'   => 23.0225,
     *    'pickup_longitude'  => 72.5714,
     *    'drop_latitude'     => 23.0338,
     *    'drop_longitude'    => 72.5850,
     *    'shifting_date'     => '2026-10-15',
     *    'floors'            => 2,                   // floors to carry without lift
     * ]
     *
     * Returns array with pricing breakdown. If survey_required = true,
     * no automatic price is generated — a physical survey must be conducted.
     */
    public function calculateQuote(array $data): array
    {
        $breakdown = [
            'survey_required'   => false,
            'total_volume_score'=> 0,
            'category_id'       => null,
            'category_name'     => null,
            'vehicle_id'        => null,
            'vehicle_name'      => null,
            'base_fare'         => 0,
            'distance_charges'  => 0,
            'addon_charges'     => 0,
            'floor_charges'     => 0,
            'weekend_charges'   => 0,
            'month_end_charges' => 0,
            'total_amount'      => 0,
            'total_distance_km' => 0,
            'items_breakdown'   => [],
            'addons_breakdown'  => [],
        ];

        // ── Step 1: Calculate Total Volume Score ──────────────────────────────
        if (isset($data['items']) && is_array($data['items'])) {
            foreach ($data['items'] as $itemData) {
                $item = Item::with('size')->find($itemData['id']);
                if ($item && $item->size) {
                    $qty   = max(1, (int) ($itemData['quantity'] ?? 1));
                    $score = $item->size->volume_score * $qty;
                    $breakdown['total_volume_score'] += $score;
                    $breakdown['items_breakdown'][] = [
                        'id'           => $item->id,
                        'name'         => $item->item_name,
                        'volume_score' => $item->size->volume_score,
                        'quantity'     => $qty,
                        'line_score'   => $score,
                    ];
                }
            }
        }

        // ── Step 2: Survey Required Check ─────────────────────────────────────
        // If total volume exceeds the threshold, no automatic quote can be given.
        // A Bhanderi Packers and Partner representative must visit for a survey.
        if ($breakdown['total_volume_score'] > self::SURVEY_REQUIRED_THRESHOLD) {
            $breakdown['survey_required'] = true;
            $breakdown['survey_message']  = 'Your shifting requirement is too large for an automatic quote. '
                . 'A Bhanderi Packers and Partner representative will visit your location for a free survey and custom quotation.';
            return $breakdown;
        }

        // ── Step 3: Auto-select Category & Vehicle by Volume Score ────────────
        $category = Category::where('min_score', '<=', $breakdown['total_volume_score'])
            ->where('max_score', '>=', $breakdown['total_volume_score'])
            ->where('status', 'active')
            ->orderBy('min_score', 'asc')
            ->first();

        // Fallback: if somehow nothing matches, pick the highest active category
        if (!$category) {
            $category = Category::where('status', 'active')
                ->orderBy('max_score', 'desc')
                ->first();
        }

        if ($category) {
            $breakdown['category_id']   = $category->id;
            $breakdown['category_name'] = $category->category_name;
            $breakdown['base_fare']     = (float) $category->base_fare;

            if ($category->vehicle_id) {
                $vehicle = Vehicle::find($category->vehicle_id);
                if ($vehicle) {
                    $breakdown['vehicle_id']   = $vehicle->id;
                    $breakdown['vehicle_name'] = $vehicle->vehicle_name;
                }
            }
        }

        // ── Step 4: Distance Charges (Haversine) ─────────────────────────────
        $lat1 = $data['pickup_latitude']  ?? null;
        $lon1 = $data['pickup_longitude'] ?? null;
        $lat2 = $data['drop_latitude']    ?? null;
        $lon2 = $data['drop_longitude']   ?? null;

        if ($lat1 && $lon1 && $lat2 && $lon2) {
            $distanceKm = $this->haversineGreatCircleDistance($lat1, $lon1, $lat2, $lon2);
            $breakdown['total_distance_km'] = round($distanceKm, 2);

            $perKmRate    = $this->getSettingValue('per_km_rate', 20);
            $baseDistance = 5; // first 5 km covered in base fare

            if ($breakdown['total_distance_km'] > $baseDistance) {
                $extraDistance = $breakdown['total_distance_km'] - $baseDistance;
                $breakdown['distance_charges'] = round($extraDistance * $perKmRate, 2);
            }
        }

        // ── Step 5: Add-On Services ───────────────────────────────────────────
        if (isset($data['addons']) && is_array($data['addons'])) {
            foreach ($data['addons'] as $addonId) {
                $addon = AddOn::find($addonId);
                if ($addon) {
                    $breakdown['addon_charges'] += $addon->price;
                    $breakdown['addons_breakdown'][] = [
                        'id'    => $addon->id,
                        'name'  => $addon->addon_name,
                        'price' => (float) $addon->price,
                    ];
                }
            }
        }

        // ── Step 6: Floor / Stair Charges ────────────────────────────────────
        if (isset($data['floors']) && (int)$data['floors'] > 0) {
            $perFloorCharge = $this->getSettingValue('per_floor_charge', 150);
            $breakdown['floor_charges'] = (int)$data['floors'] * $perFloorCharge;
        }

        // ── Step 7: Date-Based Surge Charges ─────────────────────────────────
        if (!empty($data['shifting_date'])) {
            $date = Carbon::parse($data['shifting_date']);

            // Weekend surcharge (Saturday / Sunday)
            if ($date->isWeekend()) {
                $weekendSurge = $this->getSettingValue('weekend_surge_percentage', 10);
                $breakdown['weekend_charges'] = round(
                    ($breakdown['base_fare'] + $breakdown['distance_charges']) * ($weekendSurge / 100),
                    2
                );
            }

            // Month-end surcharge (last 3 days or first 2 days of month)
            $daysInMonth = $date->daysInMonth;
            if ($date->day >= ($daysInMonth - 2) || $date->day <= 2) {
                $monthEndSurge = $this->getSettingValue('month_end_surge_percentage', 15);
                $breakdown['month_end_charges'] = round(
                    ($breakdown['base_fare'] + $breakdown['distance_charges']) * ($monthEndSurge / 100),
                    2
                );
            }
        }

        // ── Step 8: Grand Total ───────────────────────────────────────────────
        $breakdown['total_amount'] = round(
            $breakdown['base_fare']
            + $breakdown['distance_charges']
            + $breakdown['addon_charges']
            + $breakdown['floor_charges']
            + $breakdown['weekend_charges']
            + $breakdown['month_end_charges'],
            2
        );

        return $breakdown;
    }

    /**
     * Haversine Great-Circle Distance between two lat/lon points (in km).
     */
    private function haversineGreatCircleDistance(
        float $latitudeFrom, float $longitudeFrom,
        float $latitudeTo,   float $longitudeTo,
        float $earthRadius = 6371
    ): float {
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo   = deg2rad($latitudeTo);
        $lonTo   = deg2rad($longitudeTo);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(
            pow(sin($latDelta / 2), 2)
            + cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)
        ));

        return $angle * $earthRadius;
    }

    /**
     * Helper: safely fetch a pricing setting value.
     */
    private function getSettingValue(string $key, float $default): float
    {
        $setting = PricingSetting::where('key', $key)
            ->where('is_enabled', 1)
            ->first();
        return $setting ? (float) $setting->value : $default;
    }
}
