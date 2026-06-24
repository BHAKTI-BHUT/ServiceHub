<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class VehicleSeeder extends Seeder
{
    public function run(): void
    {
        $vehicles = [
            ['vehicle_name' => 'Tata Ace (Chota Hathi)', 'vehicle_capacity_score' => 15, 'status' => 'active'],
            ['vehicle_name' => 'Mahindra Bolero Maxi Truck', 'vehicle_capacity_score' => 25, 'status' => 'active'],
            ['vehicle_name' => 'Ashok Leyland Dost', 'vehicle_capacity_score' => 30, 'status' => 'active'],
            ['vehicle_name' => 'Mahindra Supro Mini Truck', 'vehicle_capacity_score' => 18, 'status' => 'active'],
            ['vehicle_name' => 'Maruti Suzuki Super Carry', 'vehicle_capacity_score' => 12, 'status' => 'active'],
            ['vehicle_name' => 'Tata 407 (10 Ft Open)', 'vehicle_capacity_score' => 45, 'status' => 'active'],
            ['vehicle_name' => 'Tata 407 (10 Ft Closed Container)', 'vehicle_capacity_score' => 40, 'status' => 'active'],
            ['vehicle_name' => 'Eicher Pro 2049 (14 Ft Open)', 'vehicle_capacity_score' => 65, 'status' => 'active'],
            ['vehicle_name' => 'Eicher Pro 2049 (14 Ft Closed)', 'vehicle_capacity_score' => 60, 'status' => 'active'],
            ['vehicle_name' => 'Eicher Pro 2059 (17 Ft Open)', 'vehicle_capacity_score' => 80, 'status' => 'active'],
            ['vehicle_name' => 'Eicher Pro 2059 (17 Ft Closed Container)', 'vehicle_capacity_score' => 90, 'status' => 'active'],
            ['vehicle_name' => 'Tata LPT 1109 (19 Ft Open)', 'vehicle_capacity_score' => 120, 'status' => 'active'],
            ['vehicle_name' => 'Tata LPT 1109 (19 Ft Closed Container)', 'vehicle_capacity_score' => 110, 'status' => 'active'],
            ['vehicle_name' => 'Eicher 20 Ft Container Truck', 'vehicle_capacity_score' => 150, 'status' => 'active'],
            ['vehicle_name' => 'Tata 22 Ft Container Truck', 'vehicle_capacity_score' => 180, 'status' => 'active'],
            ['vehicle_name' => 'Mahindra Furio 12 (17 Ft)', 'vehicle_capacity_score' => 75, 'status' => 'active'],
            ['vehicle_name' => 'Ashok Leyland Partner (14 Ft)', 'vehicle_capacity_score' => 55, 'status' => 'active'],
            ['vehicle_name' => 'Eicher Pro 3015 (20 Ft Container)', 'vehicle_capacity_score' => 140, 'status' => 'active'],
            ['vehicle_name' => 'Tata Ultra (17 Ft Container)', 'vehicle_capacity_score' => 100, 'status' => 'active'],
            ['vehicle_name' => 'Container Truck (24 Ft Multi-axle)', 'vehicle_capacity_score' => 250, 'status' => 'active'],
        ];

        $data = [];
        foreach ($vehicles as $v) {
            $data[] = [
                'vehicle_name' => $v['vehicle_name'],
                'vehicle_capacity_score' => $v['vehicle_capacity_score'],
                'status' => $v['status'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Schema::disableForeignKeyConstraints();
        DB::table('vehicles')->truncate();
        Schema::enableForeignKeyConstraints();
        
        DB::table('vehicles')->insert($data);
    }
}
