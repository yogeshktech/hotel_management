<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\FranchiseEnquiryController;
use App\Http\Controllers\WaitingListController;
use App\Http\Controllers\HomestayController;
use App\Http\Controllers\PromoCodeController;
use App\Http\Controllers\GiftCardController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\StaticPageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\VendorController;
use App\Http\Controllers\Admin\PropertyController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Vendor\DashboardController as VendorDashboardController;
use App\Http\Controllers\Staff\Auth\LoginController as StaffLoginController;
use App\Http\Controllers\Customer\Auth\LoginController as CustomerLoginController;
use App\Http\Controllers\Customer\Auth\RegisterController as CustomerRegisterController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\Customer\ProfileController as CustomerProfileController;
use App\Http\Controllers\Customer\ReviewController as CustomerReviewController;

Route::get('/', function () {
    return view('welcome');
});

// ─── Customer Auth (separate) ───
Route::prefix('customer')->name('customer.')->group(function () {
    Route::middleware('guest:customer')->group(function () {
        Route::get('login', [CustomerLoginController::class, 'showLoginForm'])->name('login');
        Route::post('login', [CustomerLoginController::class, 'login']);
        Route::get('register', [CustomerRegisterController::class, 'showRegistrationForm'])->name('register');
        Route::post('register', [CustomerRegisterController::class, 'register']);
    });
    Route::post('logout', [CustomerLoginController::class, 'logout'])->name('logout')->middleware('auth:customer');
});

// ─── Staff / Admin / Team Auth (separate) ───
Route::prefix('staff')->name('staff.')->group(function () {
    Route::middleware('guest:staff')->group(function () {
        Route::get('login', [StaffLoginController::class, 'showLoginForm'])->name('login');
        Route::post('login', [StaffLoginController::class, 'login']);
    });
    Route::post('logout', [StaffLoginController::class, 'logout'])->name('logout')->middleware('auth:staff');
});

// Public booking
Route::resource('bookings', BookingController::class)->only(['create', 'store']);
Route::post('franchise-enquiries', [FranchiseEnquiryController::class, 'store']);
Route::post('waiting-lists', [WaitingListController::class, 'store']);
Route::get('payments/{booking}', [PaymentController::class, 'create'])->name('payments.create');
Route::post('payments/success', [PaymentController::class, 'success'])->name('payments.success');

// ─── Customer Panel ───
Route::middleware(['auth:customer', 'active:customer'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');
    Route::get('profile', [CustomerProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [CustomerProfileController::class, 'update'])->name('profile.update');
    Route::get('bookings/{booking}/review', [CustomerReviewController::class, 'create'])->name('reviews.create');
    Route::post('bookings/{booking}/review', [CustomerReviewController::class, 'store'])->name('reviews.store');
});

// ─── Staff Panel (Super Admin / Team / Vendor) ───
Route::middleware(['auth:staff', 'active:staff'])->group(function () {

    Route::middleware('role:super_admin|admin_staff')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('profile', [AdminProfileController::class, 'edit'])->name('profile.edit');
        Route::put('profile', [AdminProfileController::class, 'update'])->name('profile.update');

        Route::middleware('permission:users.view')->group(function () {
            Route::get('staff', [StaffController::class, 'index'])->name('staff.index');
            Route::get('staff/create', [StaffController::class, 'create'])->middleware('permission:users.create')->name('staff.create');
            Route::post('staff', [StaffController::class, 'store'])->middleware('permission:users.create')->name('staff.store');
            Route::get('staff/{staff}/edit', [StaffController::class, 'edit'])->middleware('permission:users.edit')->name('staff.edit');
            Route::put('staff/{staff}', [StaffController::class, 'update'])->middleware('permission:users.edit')->name('staff.update');
            Route::delete('staff/{staff}', [StaffController::class, 'destroy'])->middleware('permission:users.delete')->name('staff.destroy');
            Route::post('staff/{staff}/permissions', [StaffController::class, 'updatePermissions'])->middleware('permission:roles.manage')->name('staff.permissions');
            Route::post('staff/{staff}/toggle-active', [StaffController::class, 'toggleActive'])->name('staff.toggleActive');
        });

        Route::middleware('permission:roles.view')->group(function () {
            Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
            Route::post('roles', [RoleController::class, 'store'])->middleware('permission:roles.manage')->name('roles.store');
            Route::get('roles/{role}/edit', [RoleController::class, 'edit'])->middleware('permission:roles.manage')->name('roles.edit');
            Route::put('roles/{role}', [RoleController::class, 'update'])->middleware('permission:roles.manage')->name('roles.update');
            Route::delete('roles/{role}', [RoleController::class, 'destroy'])->middleware('permission:roles.manage')->name('roles.destroy');
        });

        Route::middleware('permission:vendors.view')->group(function () {
            Route::get('vendors', [VendorController::class, 'index'])->name('vendors.index');
            Route::get('vendors/{vendor}', [VendorController::class, 'show'])->name('vendors.show');
            Route::post('vendors/{vendor}/approve', [VendorController::class, 'approve'])->middleware('permission:vendors.approve')->name('vendors.approve');
            Route::post('vendors/{vendor}/reject', [VendorController::class, 'reject'])->middleware('permission:vendors.approve')->name('vendors.reject');
            Route::post('vendors/{vendor}/suspend', [VendorController::class, 'suspend'])->middleware('permission:vendors.manage')->name('vendors.suspend');
        });

        Route::middleware('permission:properties.view')->group(function () {
            Route::get('properties', [PropertyController::class, 'index'])->name('properties.index');
            Route::get('properties/{property}', [PropertyController::class, 'show'])->name('properties.show');
            Route::post('properties/{property}/approve', [PropertyController::class, 'approve'])->middleware('permission:properties.approve')->name('properties.approve');
            Route::post('properties/{property}/reject', [PropertyController::class, 'reject'])->middleware('permission:properties.approve')->name('properties.reject');
        });

        Route::middleware('permission:bookings.view')->group(function () {
            Route::get('bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
            Route::get('bookings/create-offline', [AdminBookingController::class, 'createOffline'])->middleware('permission:bookings.manage')->name('bookings.create-offline');
            Route::post('bookings/offline', [AdminBookingController::class, 'storeOffline'])->middleware('permission:bookings.manage')->name('bookings.store-offline');
            Route::get('bookings/{booking}', [AdminBookingController::class, 'show'])->name('bookings.show');
            Route::post('bookings/{booking}/check-in', [AdminBookingController::class, 'checkIn'])->middleware('permission:bookings.manage')->name('bookings.check-in');
            Route::post('bookings/{booking}/check-out', [AdminBookingController::class, 'checkOut'])->middleware('permission:bookings.manage')->name('bookings.check-out');
        });

        Route::middleware('permission:locations.manage')->group(function () {
            Route::get('locations', [LocationController::class, 'index'])->name('locations.index');
            Route::get('locations/create', [LocationController::class, 'create'])->name('locations.create');
            Route::post('locations', [LocationController::class, 'store'])->name('locations.store');
            Route::get('locations/{location}/edit', [LocationController::class, 'edit'])->name('locations.edit');
            Route::put('locations/{location}', [LocationController::class, 'update'])->name('locations.update');
            Route::delete('locations/{location}', [LocationController::class, 'destroy'])->name('locations.destroy');
        });
    });

    Route::middleware('role:super_admin')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('homestays', HomestayController::class);
        Route::resource('promo-codes', PromoCodeController::class);
        Route::resource('gift-cards', GiftCardController::class);
        Route::resource('banners', BannerController::class);
    });

    Route::middleware('role:vendor')->prefix('vendor')->name('vendor.')->group(function () {
        Route::get('/', [VendorDashboardController::class, 'index'])->name('dashboard');
    });
});
