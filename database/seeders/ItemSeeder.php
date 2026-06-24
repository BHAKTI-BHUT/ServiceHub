<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        /*
         * Volume Score System (Bhanderi Packers and Partner):
         *   Small  = 1 point  (cartons, chairs, fans, stools, small appliances)
         *   Medium = 3 points (single bed, mattress, washing machine, single door fridge, study table, small wardrobe)
         *   Large  = 5 points (sofa set, double bed, king size bed, large wardrobe, dining table, double door fridge)
         */
        $items = [
            // ── SMALL ITEMS (Score = 1) ─────────────────────────────────────
            ['item_name' => 'Carton Box (Medium)',          'volume_score' => 1, 'size' => 'small',  'status' => 'active'],
            ['item_name' => 'Carton Box (Large)',           'volume_score' => 1, 'size' => 'small',  'status' => 'active'],
            ['item_name' => 'Plastic Chair',                'volume_score' => 1, 'size' => 'small',  'status' => 'active'],
            ['item_name' => 'Ceiling Fan / Table Fan',      'volume_score' => 1, 'size' => 'small',  'status' => 'active'],
            ['item_name' => 'Stool / Ottoman',              'volume_score' => 1, 'size' => 'small',  'status' => 'active'],
            ['item_name' => 'Microwave Oven',               'volume_score' => 1, 'size' => 'small',  'status' => 'active'],
            ['item_name' => 'Water Purifier (RO)',           'volume_score' => 1, 'size' => 'small',  'status' => 'active'],
            ['item_name' => 'Geyser / Water Heater',        'volume_score' => 1, 'size' => 'small',  'status' => 'active'],
            ['item_name' => 'Mixer Grinder',                'volume_score' => 1, 'size' => 'small',  'status' => 'active'],
            ['item_name' => 'Bicycle (Adult)',               'volume_score' => 1, 'size' => 'small',  'status' => 'active'],
            ['item_name' => 'Shoe Rack (Plastic/Metal)',    'volume_score' => 1, 'size' => 'small',  'status' => 'active'],
            ['item_name' => 'Office Chair (Ergonomic)',     'volume_score' => 1, 'size' => 'small',  'status' => 'active'],
            ['item_name' => 'Luggage Bag / Suitcase',       'volume_score' => 1, 'size' => 'small',  'status' => 'active'],
            ['item_name' => 'Inverter / Battery (Small)',   'volume_score' => 1, 'size' => 'small',  'status' => 'active'],
            ['item_name' => 'Indoor Plant (Medium)',         'volume_score' => 1, 'size' => 'small',  'status' => 'active'],

            // ── MEDIUM ITEMS (Score = 3) ────────────────────────────────────
            ['item_name' => 'Single Bed (with Frame)',      'volume_score' => 3, 'size' => 'medium', 'status' => 'active'],
            ['item_name' => 'Single Mattress',              'volume_score' => 3, 'size' => 'medium', 'status' => 'active'],
            ['item_name' => 'Washing Machine (Top Load)',   'volume_score' => 3, 'size' => 'medium', 'status' => 'active'],
            ['item_name' => 'Washing Machine (Front Load)', 'volume_score' => 3, 'size' => 'medium', 'status' => 'active'],
            ['item_name' => 'Refrigerator (Single Door)',   'volume_score' => 3, 'size' => 'medium', 'status' => 'active'],
            ['item_name' => 'Study Table / Computer Desk',  'volume_score' => 3, 'size' => 'medium', 'status' => 'active'],
            ['item_name' => 'Small Wooden Wardrobe',        'volume_score' => 3, 'size' => 'medium', 'status' => 'active'],
            ['item_name' => 'Dressing Table with Mirror',   'volume_score' => 3, 'size' => 'medium', 'status' => 'active'],
            ['item_name' => 'LED TV (Up to 55 inch)',        'volume_score' => 3, 'size' => 'medium', 'status' => 'active'],
            ['item_name' => '2-Seater Sofa',                'volume_score' => 3, 'size' => 'medium', 'status' => 'active'],
            ['item_name' => 'Center Coffee Table',          'volume_score' => 3, 'size' => 'medium', 'status' => 'active'],
            ['item_name' => 'AC Indoor Unit (Split)',        'volume_score' => 3, 'size' => 'medium', 'status' => 'active'],
            ['item_name' => 'AC Outdoor Unit (Split)',       'volume_score' => 3, 'size' => 'medium', 'status' => 'active'],
            ['item_name' => 'Window AC',                    'volume_score' => 3, 'size' => 'medium', 'status' => 'active'],
            ['item_name' => 'Baby Cot / Kids Bed',          'volume_score' => 3, 'size' => 'medium', 'status' => 'active'],

            // ── LARGE ITEMS (Score = 5) ─────────────────────────────────────
            ['item_name' => 'Double Bed (with Frame)',      'volume_score' => 5, 'size' => 'large',  'status' => 'active'],
            ['item_name' => 'King Size Bed (with Frame)',   'volume_score' => 5, 'size' => 'large',  'status' => 'active'],
            ['item_name' => 'Double Mattress',              'volume_score' => 5, 'size' => 'large',  'status' => 'active'],
            ['item_name' => '3-Seater Sofa',                'volume_score' => 5, 'size' => 'large',  'status' => 'active'],
            ['item_name' => 'L-Shaped / Corner Sofa',       'volume_score' => 5, 'size' => 'large',  'status' => 'active'],
            ['item_name' => 'Refrigerator (Double Door)',   'volume_score' => 5, 'size' => 'large',  'status' => 'active'],
            ['item_name' => 'Large Wooden Wardrobe',        'volume_score' => 5, 'size' => 'large',  'status' => 'active'],
            ['item_name' => '4-Seater Dining Table (Set)',  'volume_score' => 5, 'size' => 'large',  'status' => 'active'],
            ['item_name' => '6-Seater Dining Table (Set)',  'volume_score' => 5, 'size' => 'large',  'status' => 'active'],
            ['item_name' => 'Steel Almirah (Double Door)',  'volume_score' => 5, 'size' => 'large',  'status' => 'active'],
            ['item_name' => 'TV Unit / Entertainment Stand','volume_score' => 5, 'size' => 'large',  'status' => 'active'],
            ['item_name' => 'Sofa Chair / Recliner',        'volume_score' => 5, 'size' => 'large',  'status' => 'active'],
            ['item_name' => 'Large LED TV (Above 55 inch)', 'volume_score' => 5, 'size' => 'large',  'status' => 'active'],
            ['item_name' => 'Treadmill / Gym Equipment',    'volume_score' => 5, 'size' => 'large',  'status' => 'active'],
            ['item_name' => 'Piano / Organ',                'volume_score' => 5, 'size' => 'large',  'status' => 'active'],
        ];

        $smallSize = DB::table('item_sizes')->where('size_name', 'Small')->first()->id;
        $mediumSize = DB::table('item_sizes')->where('size_name', 'Medium')->first()->id;
        $largeSize = DB::table('item_sizes')->where('size_name', 'Large')->first()->id;

        $data = [];
        foreach ($items as $item) {
            $sizeId = $smallSize;
            if ($item['size'] === 'medium') $sizeId = $mediumSize;
            if ($item['size'] === 'large') $sizeId = $largeSize;

            $data[] = [
                'item_name'    => $item['item_name'],
                'item_size_id' => $sizeId,
                'status'       => $item['status'],
                'created_at'   => now(),
                'updated_at'   => now(),
            ];
        }

        Schema::disableForeignKeyConstraints();
        DB::table('items')->truncate();
        Schema::enableForeignKeyConstraints();

        DB::table('items')->insert($data);
    }
}
