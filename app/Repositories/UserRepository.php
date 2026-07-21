<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserRepository
{
    /**
     * Find user by mobile number.
     *
     * @param string $mobile
     * @return User|null
     */
    public function findByMobile(string $mobile): ?User
    {
        $clean = preg_replace('/\D/', '', $mobile);
        if (strlen($clean) > 10) {
            $clean = substr($clean, -10);
        }
        return User::where('mobile', $clean)->first();
    }

    /**
     * Create a new user from registration data.
     *
     * @param array $data
     * @return User
     */
    public function create(array $data): User
    {
        $clean = preg_replace('/\D/', '', $data['mobile']);
        if (strlen($clean) > 10) {
            $clean = substr($clean, -10);
        }
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'mobile' => $clean,
            'password' => Hash::make(Str::random(16)), // Random password for OTP users
            'status' => 'active',
        ]);
    }
}
