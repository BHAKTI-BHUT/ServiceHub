<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

class BookingRequestSeeder extends Seeder
{
    public function run(): void
    {
        // Get customer IDs from users with User role
        $customerIds = User::role('User')->pluck('id')->toArray();

        // 20 realistic Indian location pairs (Ahmedabad, Surat, Vadodara, Rajkot, Mumbai, Pune, Bangalore, Delhi)
        $routes = [
            ['pickup' => 'A-404, Dev Aurum, Prahlad Nagar, Ahmedabad, Gujarat 380015', 'drop' => 'B-12, Goyal Intercity, Drive-In Road, Ahmedabad, Gujarat 380054'],
            ['pickup' => 'Flat 1202, Lodha Bellissimo, Lower Parel, Mumbai, Maharashtra 400013', 'drop' => 'Flat 401, Hiranandani Gardens, Powai, Mumbai, Maharashtra 400076'],
            ['pickup' => '302, Rajhans Heights, Piplod, Surat, Gujarat 395007', 'drop' => '105, Green Elina, Adajan, Surat, Gujarat 395009'],
            ['pickup' => 'B-22, Sun Villa, Vasna Road, Vadodara, Gujarat 390007', 'drop' => '403, Sterling Apartment, Alkapuri, Vadodara, Gujarat 390007'],
            ['pickup' => 'A-501, Imperial Heights, 150 Feet Ring Road, Rajkot, Gujarat 360005', 'drop' => '12, Shrinathji Society, Kalawad Road, Rajkot, Gujarat 360005'],
            ['pickup' => 'Flat 8, Clover Park, Viman Nagar, Pune, Maharashtra 411014', 'drop' => 'A-903, Megapolis Splendour, Hinjawadi, Pune, Maharashtra 411057'],
            ['pickup' => '204, Prestige Lakeside Habitat, Whitefield, Bangalore, Karnataka 560087', 'drop' => '105, Sobha Elanza, HSR Layout, Bangalore, Karnataka 560102'],
            ['pickup' => 'H-19, Green Park, New Delhi, Delhi 110016', 'drop' => 'Sector 45, Gurgaon, Haryana 122003'],
            ['pickup' => 'Tower 5, Apex Athena, Sector 75, Noida, Uttar Pradesh 201301', 'drop' => 'Sector 62, Noida, Uttar Pradesh 201301'],
            ['pickup' => 'Flat 1405, My Home Avatar, Gachibowli, Hyderabad, Telangana 500075', 'drop' => 'Flat 302, Aparna Sarovar, Nallagandla, Hyderabad, Telangana 500019'],
            ['pickup' => '23, Salt Lake Sector 2, Kolkata, West Bengal 700091', 'drop' => 'New Town Action Area 1, Kolkata, West Bengal 700156'],
            ['pickup' => 'Block C, Anna Nagar, Chennai, Tamil Nadu 600040', 'drop' => 'Velachery Main Road, Chennai, Tamil Nadu 600042'],
            ['pickup' => 'A-10, Shanti Nagar, Ahmedabad, Gujarat 380013', 'drop' => 'Sector 3, Gandhinagar, Gujarat 382007'],
            ['pickup' => 'Flat 504, Kalpataru Aura, Ghatkopar, Mumbai, Maharashtra 400086', 'drop' => 'Sector 15, Vashi, Navi Mumbai, Maharashtra 400703'],
            ['pickup' => '12/A, Rander Road, Surat, Gujarat 395005', 'drop' => 'Flat 202, Surya Complex, Ghod Dod Road, Surat, Gujarat 395007'],
            ['pickup' => 'G-3, Paras Society, Gotri, Vadodara, Gujarat 390021', 'drop' => '44, Akota, Vadodara, Gujarat 390020'],
            ['pickup' => '201, Elite Homes, Karve Road, Pune, Maharashtra 411004', 'drop' => 'Sector 2, Kothrud, Pune, Maharashtra 411038'],
            ['pickup' => 'H-302, DLF Phase 3, Gurgaon, Haryana 122002', 'drop' => 'Sector 56, Gurgaon, Haryana 122011'],
            ['pickup' => '405, Brigade Metropolis, Mahadevapura, Bangalore, Karnataka 560048', 'drop' => 'Electronic City Phase 1, Bangalore, Karnataka 560100'],
            ['pickup' => 'Flat 12B, Sunrise Apartments, Garia, Kolkata, West Bengal 700084', 'drop' => 'Tollygunge, Kolkata, West Bengal 700033'],
        ];

        $statuses = ['pending', 'approved', 'rejected'];

        $data = [];
        for ($i = 0; $i < 20; $i++) {
            $route = $routes[$i];
            $customerId = $customerIds[$i % count($customerIds)];

            // Shifting dates from July 2026 to December 2026
            $day = rand(1, 28);
            $month = rand(7, 12);
            $shiftingDate = "2026-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-" . str_pad($day, 2, '0', STR_PAD_LEFT);

            // Shifting times
            $times = ['09:00:00', '11:30:00', '14:00:00', '16:30:00'];
            $shiftingTime = $times[rand(0, 3)];

            $data[] = [
                'customer_id' => $customerId,
                'pickup_location' => $route['pickup'],
                'drop_location' => $route['drop'],
                'pickup_latitude' => 22.0 + (rand(0, 1000) / 100.0),
                'pickup_longitude' => 72.0 + (rand(0, 1000) / 100.0),
                'drop_latitude' => 22.0 + (rand(0, 1000) / 100.0),
                'drop_longitude' => 72.0 + (rand(0, 1000) / 100.0),
                'shifting_date' => $shiftingDate,
                'shifting_time' => $shiftingTime,
                'estimated_amount' => '0.00',
                'status' => $statuses[$i % count($statuses)],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Schema::disableForeignKeyConstraints();
        DB::table('booking_requests')->truncate();
        Schema::enableForeignKeyConstraints();
        
        DB::table('booking_requests')->insert($data);
    }
}
