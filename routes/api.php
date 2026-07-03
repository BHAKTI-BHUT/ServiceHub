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

    // Booking Requests
    Route::get('/booking-requests', [App\Http\Controllers\Api\BookingRequestController::class, 'index']);
    Route::post('/booking-requests', [App\Http\Controllers\Api\BookingRequestController::class, 'store']);

// Bookings
    Route::get('/bookings', [App\Http\Controllers\Api\BookingController::class, 'index']);
    Route::get('/bookings/{id}', [App\Http\Controllers\Api\BookingController::class, 'show']);
    Route::post('/bookings/{id}/cancel', [App\Http\Controllers\Api\BookingController::class, 'cancel']);
    // New endpoints for estimating and creating bookings
    Route::post('/bookings/estimate', [App\Http\Controllers\Api\BookingController::class, 'estimate']);
    Route::post('/bookings', [App\Http\Controllers\Api\BookingController::class, 'store']);
    // Add-ons
    Route::get('/addons', [App\Http\Controllers\Api\AddOnController::class, 'index']);

});
