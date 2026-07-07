<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Models\Booking;
use App\Models\User;
use App\Models\Category;
use App\Models\Vehicle;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Booking::truncate();
        Schema::enableForeignKeyConstraints();

        $customers = User::role('User')->get();
        $vendors = User::role('Vendor')->get();
        $categories = Category::all();

        // Realistic Indian location pairs
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

        $statuses = [
            'completed', 'completed', 'completed', 'completed',
            'completed', 'completed', 'completed', 'completed',
            'confirmed', 'confirmed', 'confirmed', 'confirmed',
            'in_progress', 'in_progress', 'in_progress',
            'cancelled', 'cancelled', 'cancelled',
            'pending', 'pending',
        ];

        for ($i = 0; $i < 20; $i++) {
            $route = $routes[$i];
            $customer = $customers->random();
            $vendor = $vendors->random();
            $category = $categories->random();

            $status = $statuses[$i];
            
            // Generate dates in 2026
            $day = rand(1, 28);
            $month = rand(1, 6); // past dates for completed / reports, or future
            $shiftingDate = "2026-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-" . str_pad($day, 2, '0', STR_PAD_LEFT);
            
            $times = ['08:00:00', '10:00:00', '13:00:00', '16:00:00'];
            $shiftingTime = $times[rand(0, 3)];

            // Setup pricing breakdown
            $base_fare = $category->base_fare;
            $distance_charges = rand(5, 50) * 20.00; // km * rate
            $addon_charges = rand(0, 3) * 500.00;
            $floor_charges = rand(0, 4) * 100.00;
            
            $subtotal = $base_fare + $distance_charges + $addon_charges + $floor_charges;
            $weekend_charges = (rand(0, 1) == 1) ? round($subtotal * 0.10, 2) : 0.00;
            $month_end_charges = (rand(0, 1) == 1) ? round($subtotal * 0.15, 2) : 0.00;
            
            $amount = $subtotal + $weekend_charges + $month_end_charges;
            $advance_amount = 0.00;
            $remaining_amount = $amount;

            // Vendor Settlement
            $vendor_commission_amount = round($amount * 0.15, 2);
            $vendor_settlement_amount = $amount - $vendor_commission_amount;

            // Tracking and Payment statuses
            if ($status === 'completed') {
                $tracking_status = 'completed';
                $advance_payment_status = 'paid';
                $remaining_payment_status = 'paid';
                $registration_payment_status = 'paid';
            } elseif ($status === 'in_progress') {
                $tracking_status = 'in_transit';
                $advance_payment_status = 'paid';
                $remaining_payment_status = 'pending';
                $registration_payment_status = 'paid';
            } elseif ($status === 'confirmed') {
                $tracking_status = 'confirmed';
                $advance_payment_status = 'paid';
                $remaining_payment_status = 'pending';
                $registration_payment_status = 'paid';
            } elseif ($status === 'cancelled') {
                $tracking_status = 'cancelled';
                $advance_payment_status = 'paid';
                $remaining_payment_status = 'pending';
                $registration_payment_status = 'pending';
            } else {
                $tracking_status = 'pending_confirmation';
                $advance_payment_status = 'paid';
                $remaining_payment_status = 'pending';
                $registration_payment_status = 'pending';
            }

            Booking::create([
                'booking_number' => 'BPP-2026-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'customer_id' => $customer->id,
                'vendor_id' => $vendor->id,
                'booking_request_id' => null,
                'pickup_location' => $route['pickup'],
                'drop_location' => $route['drop'],
                'pickup_latitude' => 22.0 + (rand(0, 1000) / 100.0),
                'pickup_longitude' => 72.0 + (rand(0, 1000) / 100.0),
                'drop_latitude' => 22.0 + (rand(0, 1000) / 100.0),
                'drop_longitude' => 72.0 + (rand(0, 1000) / 100.0),
                'pickup_contact_name' => $customer->name,
                'pickup_contact_mobile' => $customer->mobile,
                'drop_contact_name' => 'Recipient at Destination',
                'drop_contact_mobile' => '98' . rand(10000000, 99999999),
                'shifting_date' => $shiftingDate,
                'shifting_time' => $shiftingTime,
                'amount' => $amount,
                'base_fare' => $base_fare,
                'distance_charges' => $distance_charges,
                'addon_charges' => $addon_charges,
                'floor_charges' => $floor_charges,
                'weekend_charges' => $weekend_charges,
                'month_end_charges' => $month_end_charges,
                'advance_amount' => $advance_amount,
                'remaining_amount' => $remaining_amount,
                'advance_payment_status' => $advance_payment_status,
                'remaining_payment_status' => $remaining_payment_status,
                'registration_charge' => 500.00,
                'registration_payment_status' => $registration_payment_status,
                'status' => $status,
                'tracking_status' => $tracking_status,
                'vendor_commission_amount' => $vendor_commission_amount,
                'vendor_settlement_amount' => $vendor_settlement_amount,
                'category_id' => $category->id,
                'vehicle_id' => $category->vehicle_id,
                'total_distance' => rand(10, 150),
                'total_volume_score' => rand(10, 100),
            ]);
        }
    }
}
