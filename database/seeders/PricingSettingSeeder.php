<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PricingSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // Required parameters by PricingEngine
            ['key' => 'per_km_rate', 'value' => '20', 'is_enabled' => true],
            ['key' => 'per_floor_charge', 'value' => '100', 'is_enabled' => true],
            ['key' => 'weekend_surge_percentage', 'value' => '10', 'is_enabled' => true],
            ['key' => 'month_end_surge_percentage', 'value' => '15', 'is_enabled' => true],
            
            // 16 additional realistic settings
            ['key' => 'gst_tax_percentage', 'value' => '18', 'is_enabled' => true],
            ['key' => 'toll_charges_flat', 'value' => '300', 'is_enabled' => true],
            ['key' => 'packing_labor_charge_per_person', 'value' => '500', 'is_enabled' => true],
            ['key' => 'loading_labor_charge_per_person', 'value' => '400', 'is_enabled' => true],
            ['key' => 'unloading_labor_charge_per_person', 'value' => '400', 'is_enabled' => true],
            ['key' => 'minimum_distance_km', 'value' => '5', 'is_enabled' => true],
            ['key' => 'base_labor_count', 'value' => '2', 'is_enabled' => true],
            ['key' => 'intercity_rate_multiplier', 'value' => '1.5', 'is_enabled' => true],
            ['key' => 'insurance_base_rate_percentage', 'value' => '1.2', 'is_enabled' => true],
            ['key' => 'heavy_item_additional_charge', 'value' => '250', 'is_enabled' => true],
            ['key' => 'lift_available_discount_percentage', 'value' => '5', 'is_enabled' => true],
            ['key' => 'night_shift_charge_flat', 'value' => '1000', 'is_enabled' => true],
            ['key' => 'cancellation_fee_flat', 'value' => '500', 'is_enabled' => true],
            ['key' => 'booking_advance_payment_percentage', 'value' => '20', 'is_enabled' => true],
            ['key' => 'vendor_commission_percentage', 'value' => '15', 'is_enabled' => true],
            ['key' => 'fuel_surcharge_flat', 'value' => '200', 'is_enabled' => true],
        ];

        $data = [];
        foreach ($settings as $s) {
            $data[] = [
                'key' => $s['key'],
                'value' => $s['value'],
                'is_enabled' => $s['is_enabled'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Schema::disableForeignKeyConstraints();
        DB::table('pricing_settings')->truncate();
        Schema::enableForeignKeyConstraints();
        
        DB::table('pricing_settings')->insert($data);
    }
}
