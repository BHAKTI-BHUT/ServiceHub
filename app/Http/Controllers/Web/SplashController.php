<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redirect;

class SplashController extends Controller
{
    // Show initial splash screen (could be a simple loading animation)
    public function showSplash()
    {
        return view('auth.splash');
    }

    // Show mobile number input form
    public function showEnterMobile()
    {
        return view('auth.enter_mobile');
    }

    // Handle mobile number check via API
    public function postCheckMobile(Request $request)
    {
        $request->validate([
            'mobile' => 'required|string|max:20',
        ]);
        $response = Http::post(url('/api/check-mobile'), [
            'mobile' => $request->mobile,
        ]);
        if ($response->successful()) {
            $exists = $response->json('exists');
            if ($exists) {
                // Existing user: send OTP
                return redirect()->route('auth.verify-otp')->with('mobile', $request->mobile);
            } else {
                // New registration flow
                return redirect()->route('auth.register')->with('mobile', $request->mobile);
            }
        }
        return back()->withErrors(['mobile' => 'Unable to verify mobile number.']);
    }

    // Show registration form for new users (name, email, mobile)
    public function showRegister()
    {
        return view('auth.register');
    }

    // Handle registration via API
    public function postRegister(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'mobile' => 'required|string|max:20',
        ]);
        $response = Http::post(url('/api/register'), $request->only(['name', 'email', 'mobile']));
        if ($response->successful()) {
            return redirect()->route('auth.verify-otp')->with('mobile', $request->mobile);
        }
        return back()->withErrors(['register' => 'Registration failed.']);
    }

    // Show OTP verification form
    public function showVerifyOtp(Request $request)
    {
        $mobile = session('mobile') ?? $request->query('mobile');
        return view('auth.verify_otp', compact('mobile'));
    }

    // Verify OTP via API
    public function postVerifyOtp(Request $request)
    {
        $request->validate([
            'mobile' => 'required|string',
            'otp' => 'required|string',
        ]);
        $response = Http::post(url('/api/verify-otp'), $request->only(['mobile', 'otp']));
        if ($response->successful() && $response->json('success')) {
            // Store token in session for subsequent API calls
            session(['auth_token' => $response->json('token')]);
            return redirect()->route('request.quick');
        }
        return back()->withErrors(['otp' => 'Invalid OTP.']);
    }

    // Show quick request screen (pickup, drop, etc.)
    public function showQuickRequest()
    {
        return view('request.quick');
    }

    // Submit quick request – create a booking request via API
    public function postQuickRequest(Request $request)
    {
        $request->validate([
            'pickup_location' => 'required|string',
            'drop_location' => 'required|string',
            'pickup_latitude' => 'nullable|numeric',
            'pickup_longitude' => 'nullable|numeric',
            'drop_latitude' => 'nullable|numeric',
            'drop_longitude' => 'nullable|numeric',
        ]);
        $payload = $request->all();
        $payload['token'] = session('auth_token');
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . session('auth_token'),
        ])->post(url('/api/booking-requests'), $payload);
        if ($response->successful()) {
            return redirect()->route('dashboard')->with('status', 'Request submitted');
        }
        return back()->withErrors(['request' => 'Failed to submit request']);
    }
}
