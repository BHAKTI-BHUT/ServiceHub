<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AddOn;

class AddOnSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Required add-on services for Bhanderi Packers and Partner:
     * Packing, Unpacking, Loading & Unloading, Stair/Floor Charges,
     * Lift Not Available, AC/TV/RO/Geyser Installation, Furniture Dismantling
     */
    public function run(): void
    {
        $addons = [
            ['addon_name' => 'Packing Service',                    'price' => 800.00,  'status' => 'active'],
            ['addon_name' => 'Unpacking Service',                   'price' => 600.00,  'status' => 'active'],
            ['addon_name' => 'Loading & Unloading (Labor)',         'price' => 1000.00, 'status' => 'active'],
            ['addon_name' => 'Stair / Floor Charges (Per Floor)',   'price' => 150.00,  'status' => 'active'],
            ['addon_name' => 'Lift Not Available Charges',          'price' => 500.00,  'status' => 'active'],
            ['addon_name' => 'AC Installation / Uninstallation',    'price' => 1200.00, 'status' => 'active'],
            ['addon_name' => 'TV Installation / Uninstallation',    'price' => 500.00,  'status' => 'active'],
            ['addon_name' => 'RO / Water Purifier Installation',    'price' => 600.00,  'status' => 'active'],
            ['addon_name' => 'Geyser / Water Heater Installation',  'price' => 400.00,  'status' => 'active'],
            ['addon_name' => 'Furniture Dismantling & Reassembly',  'price' => 1000.00, 'status' => 'active'],
        ];

        foreach ($addons as $addon) {
            AddOn::updateOrCreate(
                ['addon_name' => $addon['addon_name']],
                [
                    'price'  => $addon['price'],
                    'status' => $addon['status'],
                ]
            );
        }
    }
}
