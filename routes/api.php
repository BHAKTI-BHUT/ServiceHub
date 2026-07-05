<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

Route::post('/check-mobile', [AuthController::class, 'checkMobile']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/resend-otp', [AuthController::class, 'resendOtp']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Items (for user to browse and select before booking)
    Route::get('/items', [App\Http\Controllers\Api\ItemController::class, 'index']);

    // Booking Requests
    Route::get('/booking-requests', [App\Http\Controllers\Api\BookingRequestController::class, 'index']);
    Route::post('/booking-requests', [App\Http\Controllers\Api\BookingRequestController::class, 'store']);
    // View a single booking request + admin-created booking details linked to it
    Route::get('/booking-requests/{id}', [App\Http\Controllers\Api\BookingRequestController::class, 'show']);

// Bookings
    Route::get('/bookings/init', [App\Http\Controllers\Api\BookingController::class, 'initData']);
    Route::get('/bookings', [App\Http\Controllers\Api\BookingController::class, 'index']);
    Route::get('/bookings/{id}', [App\Http\Controllers\Api\BookingController::class, 'show']);
    Route::post('/bookings/{id}/cancel', [App\Http\Controllers\Api\BookingController::class, 'cancel']);
    Route::post('/bookings/{id}/verify-registration-payment', [App\Http\Controllers\Api\BookingController::class, 'verifyRegistrationPayment']);
    // New endpoints for estimating and creating bookings
    Route::post('/bookings/estimate', [App\Http\Controllers\Api\BookingController::class, 'estimate']);
    Route::post('/bookings', [App\Http\Controllers\Api\BookingController::class, 'store']);
    // Add-ons
    Route::get('/addons', [App\Http\Controllers\Api\AddOnController::class, 'index']);

});
