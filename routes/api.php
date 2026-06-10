<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\User\AuthController;
use App\Http\Controllers\Api\User\ProfileController;
use App\Http\Controllers\Api\User\DashboardController;
use App\Http\Controllers\Api\User\LocationController;
use App\Http\Controllers\Api\User\PropertyController;
use App\Http\Controllers\Api\User\BookingController;
use App\Http\Controllers\Api\User\ReviewController;
use App\Http\Controllers\Api\User\PromoCodeController;
use App\Http\Controllers\Api\User\BannerController;

Route::prefix('v1/user')->group(function () {

    // Public — Auth
    Route::post('auth/register', [AuthController::class, 'register']);
    Route::post('auth/login', [AuthController::class, 'login']);

    // Public — Browse
    Route::get('locations', [LocationController::class, 'index']);
    Route::get('locations/{location}', [LocationController::class, 'show']);
    Route::get('properties', [PropertyController::class, 'index']);
    Route::get('properties/{property}', [PropertyController::class, 'show']);
    Route::get('properties/{property}/rooms', [PropertyController::class, 'rooms']);
    Route::get('banners', [BannerController::class, 'index']);
    Route::post('promo-codes/validate', [PromoCodeController::class, 'validateCode']);
    Route::post('bookings/calculate-price', [BookingController::class, 'calculatePrice']);

    // Protected — Customer token required
    Route::middleware(['auth:sanctum', 'active:customer'])->group(function () {
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('auth/me', [AuthController::class, 'me']);

        Route::get('profile', [ProfileController::class, 'show']);
        Route::put('profile', [ProfileController::class, 'update']);

        Route::get('dashboard', [DashboardController::class, 'index']);

        Route::get('bookings', [BookingController::class, 'index']);
        Route::post('bookings', [BookingController::class, 'store']);
        Route::get('bookings/{booking}', [BookingController::class, 'show']);
        Route::post('bookings/{booking}/cancel', [BookingController::class, 'cancel']);
        Route::post('bookings/{booking}/review', [ReviewController::class, 'store']);

        Route::get('reviews', [ReviewController::class, 'index']);
    });
});
