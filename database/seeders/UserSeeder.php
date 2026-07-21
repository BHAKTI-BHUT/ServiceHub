<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Make sure roles exist
        $userRole = Role::firstOrCreate(['name' => 'User', 'guard_name' => 'web']);
        $vendorRole = Role::firstOrCreate(['name' => 'Vendor', 'guard_name' => 'web']);

        // 20 Indian Customer names
        $customers = [
            ['name' => 'Amit Bhanderi', 'email' => 'amit.bhanderi@gmail.com', 'mobile' => '9876543210'],
            ['name' => 'Rajesh Sharma', 'email' => 'rajesh.sharma@yahoo.com', 'mobile' => '9823456789'],
            ['name' => 'Priya Patel', 'email' => 'priya.patel@gmail.com', 'mobile' => '9712345678'],
            ['name' => 'Vikram Malhotra', 'email' => 'vikram.m@outlook.com', 'mobile' => '9609876543'],
            ['name' => 'Rahul Deshmukh', 'email' => 'rahul.d@gmail.com', 'mobile' => '9598765432'],
            ['name' => 'Kavita Nair', 'email' => 'kavita.nair@hotmail.com', 'mobile' => '9487654321'],
            ['name' => 'Sandeep Gupta', 'email' => 'sandeep.g@gmail.com', 'mobile' => '9376543210'],
            ['name' => 'Sneha Reddy', 'email' => 'sneha.reddy@yahoo.com', 'mobile' => '9265432109'],
            ['name' => 'Manoj Tiwari', 'email' => 'tiwari.manoj@gmail.com', 'mobile' => '9154321098'],
            ['name' => 'Ajay Kumar', 'email' => 'ajay.kumar@gmail.com', 'mobile' => '9043210987'],
            ['name' => 'Rohit Yadav', 'email' => 'rohit.yadav@gmail.com', 'mobile' => '8932109876'],
            ['name' => 'Deepa Joshi', 'email' => 'deepa.joshi@outlook.com', 'mobile' => '8821098765'],
            ['name' => 'Sanjay Rathi', 'email' => 'sanjay.rathi@gmail.com', 'mobile' => '8710987654'],
            ['name' => 'Anil Verma', 'email' => 'anil.v@gmail.com', 'mobile' => '8609876543'],
            ['name' => 'Rakesh Shah', 'email' => 'rakesh.shah@gmail.com', 'mobile' => '8598765432'],
            ['name' => 'Manish Singh', 'email' => 'manish.singh@yahoo.com', 'mobile' => '8487654321'],
            ['name' => 'Kavya Iyer', 'email' => 'kavya.iyer@gmail.com', 'mobile' => '8376543210'],
            ['name' => 'Vinay Gowda', 'email' => 'vinay.gowda@gmail.com', 'mobile' => '8265432109'],
            ['name' => 'Suresh Pillai', 'email' => 'suresh.p@gmail.com', 'mobile' => '8154321098'],
            ['name' => 'Harish Mehta', 'email' => 'harish.mehta@outlook.com', 'mobile' => '8043210987'],
            ['name' => 'Neha Bhatia', 'email' => 'neha.bhatia@gmail.com', 'mobile' => '7932109876'],
            ['name' => 'Divya Saxena', 'email' => 'divya.s@gmail.com', 'mobile' => '7821098765'],
            ['name' => 'Kiran More', 'email' => 'kiran.more@yahoo.com', 'mobile' => '7710987654'],
            ['name' => 'Alok Misra', 'email' => 'alok.misra@gmail.com', 'mobile' => '7609876543'],
            ['name' => 'Poonam Sen', 'email' => 'poonam.sen@gmail.com', 'mobile' => '7598765432'],
        ];

        foreach ($customers as $c) {
            $user = User::updateOrCreate(
                ['email' => $c['email']],
                [
                    'name' => $c['name'],
                    'mobile' => $c['mobile'],
                    'password' => bcrypt('password'),
                    'status' => 'active',
                ]
            );
            $user->assignRole($userRole);
        }

        // 5 Indian Packers & Movers Vendor names
        $vendors = [
            ['name' => 'Bhanderi Packers and Partner', 'email' => 'vendor.bhanderi@gmail.com', 'mobile' => '9988776655'],
            ['name' => 'Balaji Packers & Movers', 'email' => 'vendor.balaji@gmail.com', 'mobile' => '9977665544'],
            ['name' => 'Jai Shree Ram Transports', 'email' => 'vendor.jsr@gmail.com', 'mobile' => '9966554433'],
            ['name' => 'Om Sai Packers & Movers', 'email' => 'vendor.omsai@gmail.com', 'mobile' => '9955443322'],
            ['name' => 'Gujarat Goods Carrier', 'email' => 'vendor.ggc@gmail.com', 'mobile' => '9944332211'],
        ];

        foreach ($vendors as $v) {
            $user = User::updateOrCreate(
                ['email' => $v['email']],
                [
                    'name' => $v['name'],
                    'mobile' => $v['mobile'],
                    'password' => bcrypt('password'),
                    'status' => 'active',
                ]
            );
            $user->assignRole($vendorRole);
        }
    }
}
