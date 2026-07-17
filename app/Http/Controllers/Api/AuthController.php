<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CheckMobileRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Http\Requests\Api\ResendOtpRequest;
use App\Http\Requests\Api\VerifyOtpRequest;
use App\Repositories\UserRepository;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    protected $otpService;
    protected $userRepository;

    public function __construct(OtpService $otpService, UserRepository $userRepository)
    {
        $this->otpService = $otpService;
        $this->userRepository = $userRepository;
    }

    /**
     * Format mobile number to exactly 10 digits.
     */
    private function formatMobile(string $mobile): string
    {
        $clean = preg_replace('/\D/', '', $mobile);
        return strlen($clean) > 10 ? substr($clean, -10) : $clean;
    }

    /**
     * Check if a user with the mobile number exists.
     */
    public function checkMobile(CheckMobileRequest $request)
    {
        $mobile = $this->formatMobile($request->validated('mobile'));
        $user = $this->userRepository->findByMobile($mobile);

        if ($user) {
            if ($user->status !== 'active') {
                return response()->json(['message' => 'Account is inactive.'], 403);
            }

            // Generate OTP since user exists
            try {
                $this->otpService->generateOtp($mobile);
                return response()->json(['exists' => true]);
            } catch (\Exception $e) {
                return response()->json(['message' => $e->getMessage()], 429); // Too Many Requests
            }
        }

        return response()->json(['exists' => false]);
    }

    /**
     * Register a new user and send OTP.
     */
    public function register(RegisterRequest $request)
    {
        $data = $request->validated();
        $data['mobile'] = $this->formatMobile($data['mobile']);
        
        $user = $this->userRepository->create($data);

        // Assign default 'User' role on app registration
        $role = Role::firstOrCreate(['name' => 'User', 'guard_name' => 'web']);
        $user->assignRole($role);

        try {
            $this->otpService->generateOtp($user->mobile);
            
            return response()->json([
                'success' => true,
                'otp_sent' => true
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 429);
        }
    }

    /**
     * Verify OTP and return Sanctum token.
     */
    public function verifyOtp(VerifyOtpRequest $request)
    {
        $mobile = $this->formatMobile($request->validated('mobile'));
        $otp = $request->validated('otp');

        try {
            $this->otpService->verifyOtp($mobile, $otp);

            $user = $this->userRepository->findByMobile($mobile);

            if (!$user || $user->status !== 'active') {
                return response()->json(['message' => 'User not found or inactive.'], 404);
            }

            // Automatically assign 'User' role if they don't have it (e.g. registered from web/admin)
            if (!$user->hasRole('User')) {
                $role = Role::firstOrCreate(['name' => 'User', 'guard_name' => 'web']);
                $user->assignRole($role);
            }

            // Revoke all existing tokens for simplicity (optional)
            $user->tokens()->delete();

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'mobile' => $user->mobile,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * Resend OTP.
     */
    public function resendOtp(ResendOtpRequest $request)
    {
        $mobile = $this->formatMobile($request->validated('mobile'));

        try {
            $this->otpService->generateOtp($mobile);
            
            return response()->json([
                'success' => true,
                'message' => 'OTP resent successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 429);
        }
    }

    /**
     * Logout and revoke Sanctum token.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully.'
        ]);
    }
}
