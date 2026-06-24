<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Vehicle;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $vehicles = Vehicle::all()->keyBy('vehicle_name');

        /*
         * Volume Score System:
         *   Small Item  = 1 point   (carton, chair, fan, stool, small appliance)
         *   Medium Item = 3 points  (single bed, mattress, washing machine, single fridge, study table)
         *   Large Item  = 5 points  (sofa set, double bed, king bed, large wardrobe, dining table, double fridge)
         *
         * Survey Required triggers when score > 310 (6 BHK+, Villas, Commercial, Warehouse, Factory)
         */
        $categories = [
            [
                'category_name' => 'Micro Shifting',
                'vehicle_name'  => 'Maruti Suzuki Super Carry',
                'min_score'     => 0,
                'max_score'     => 5,
                'base_fare'     => 1000.00,
                'status'        => 'active',
            ],
            [
                'category_name' => '1 RK Shifting',
                'vehicle_name'  => 'Tata Ace (Chota Hathi)',
                'min_score'     => 6,
                'max_score'     => 12,
                'base_fare'     => 1500.00,
                'status'        => 'active',
            ],
            [
                'category_name' => '1 BHK Small',
                'vehicle_name'  => 'Mahindra Supro Mini Truck',
                'min_score'     => 13,
                'max_score'     => 22,
                'base_fare'     => 2200.00,
                'status'        => 'active',
            ],
            [
                'category_name' => '1 BHK Medium',
                'vehicle_name'  => 'Mahindra Bolero Maxi Truck',
                'min_score'     => 23,
                'max_score'     => 32,
                'base_fare'     => 2800.00,
                'status'        => 'active',
            ],
            [
                'category_name' => '1 BHK Big',
                'vehicle_name'  => 'Ashok Leyland Dost',
                'min_score'     => 33,
                'max_score'     => 42,
                'base_fare'     => 3500.00,
                'status'        => 'active',
            ],
            [
                'category_name' => '2 BHK Small',
                'vehicle_name'  => 'Tata 407 (10 Ft Closed Container)',
                'min_score'     => 43,
                'max_score'     => 55,
                'base_fare'     => 4500.00,
                'status'        => 'active',
            ],
            [
                'category_name' => '2 BHK Medium',
                'vehicle_name'  => 'Ashok Leyland Partner (14 Ft)',
                'min_score'     => 56,
                'max_score'     => 70,
                'base_fare'     => 5500.00,
                'status'        => 'active',
            ],
            [
                'category_name' => '2 BHK Big',
                'vehicle_name'  => 'Eicher Pro 2049 (14 Ft Closed)',
                'min_score'     => 71,
                'max_score'     => 85,
                'base_fare'     => 6500.00,
                'status'        => 'active',
            ],
            [
                'category_name' => '3 BHK Small',
                'vehicle_name'  => 'Eicher Pro 2059 (17 Ft Open)',
                'min_score'     => 86,
                'max_score'     => 100,
                'base_fare'     => 8000.00,
                'status'        => 'active',
            ],
            [
                'category_name' => '3 BHK Medium',
                'vehicle_name'  => 'Eicher Pro 2059 (17 Ft Closed Container)',
                'min_score'     => 101,
                'max_score'     => 120,
                'base_fare'     => 9500.00,
                'status'        => 'active',
            ],
            [
                'category_name' => '3 BHK Big',
                'vehicle_name'  => 'Tata Ultra (17 Ft Container)',
                'min_score'     => 121,
                'max_score'     => 140,
                'base_fare'     => 11000.00,
                'status'        => 'active',
            ],
            [
                'category_name' => '4 BHK Small',
                'vehicle_name'  => 'Tata LPT 1109 (19 Ft Closed Container)',
                'min_score'     => 141,
                'max_score'     => 160,
                'base_fare'     => 13000.00,
                'status'        => 'active',
            ],
            [
                'category_name' => '4 BHK Medium',
                'vehicle_name'  => 'Eicher 20 Ft Container Truck',
                'min_score'     => 161,
                'max_score'     => 185,
                'base_fare'     => 15500.00,
                'status'        => 'active',
            ],
            [
                'category_name' => '4 BHK Big',
                'vehicle_name'  => 'Tata 22 Ft Container Truck',
                'min_score'     => 186,
                'max_score'     => 210,
                'base_fare'     => 18000.00,
                'status'        => 'active',
            ],
            [
                'category_name' => '5 BHK Small',
                'vehicle_name'  => 'Eicher Pro 3015 (20 Ft Container)',
                'min_score'     => 211,
                'max_score'     => 240,
                'base_fare'     => 21000.00,
                'status'        => 'active',
            ],
            [
                'category_name' => '5 BHK Medium',
                'vehicle_name'  => 'Container Truck (24 Ft Multi-axle)',
                'min_score'     => 241,
                'max_score'     => 270,
                'base_fare'     => 25000.00,
                'status'        => 'active',
            ],
            [
                'category_name' => '5 BHK Big',
                'vehicle_name'  => 'Container Truck (24 Ft Multi-axle)',
                'min_score'     => 271,
                'max_score'     => 310,
                'base_fare'     => 30000.00,
                'status'        => 'active',
            ],
            [
                // 6 BHK+, Villas, Duplex, Commercial Office, Warehouse, Factory etc.
                'category_name' => 'Survey Required',
                'vehicle_name'  => null,
                'min_score'     => 311,
                'max_score'     => 9999,
                'base_fare'     => 0.00,
                'status'        => 'active',
            ],
        ];

        $data = [];
        foreach ($categories as $c) {
            $vehicle   = $c['vehicle_name'] ? $vehicles->get($c['vehicle_name']) : null;
            $data[] = [
                'category_name' => $c['category_name'],
                'vehicle_id'    => $vehicle ? $vehicle->id : null,
                'min_score'     => $c['min_score'],
                'max_score'     => $c['max_score'],
                'base_fare'     => $c['base_fare'],
                'status'        => $c['status'],
                'created_at'    => now(),
                'updated_at'    => now(),
            ];
        }

        Schema::disableForeignKeyConstraints();
        DB::table('categories')->truncate();
        Schema::enableForeignKeyConstraints();

        DB::table('categories')->insert($data);
    }
}
