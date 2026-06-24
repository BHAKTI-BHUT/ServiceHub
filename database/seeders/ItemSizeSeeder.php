<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ItemSizeSeeder extends Seeder
{
    public function run(): void
    {
        $sizes = [
            ['size_name' => 'Small', 'volume_score' => 1, 'status' => 'active'],
            ['size_name' => 'Medium', 'volume_score' => 3, 'status' => 'active'],
            ['size_name' => 'Large', 'volume_score' => 5, 'status' => 'active'],
        ];

        Schema::disableForeignKeyConstraints();
        DB::table('item_sizes')->truncate();
        Schema::enableForeignKeyConstraints();

        $now = now();
        foreach ($sizes as &$size) {
            $size['created_at'] = $now;
            $size['updated_at'] = $now;
        }

        DB::table('item_sizes')->insert($sizes);
    }
}
