<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UnauthenticatedController;

Route::controller(UnauthenticatedController::class)->group(function () {
    Route::get('/unauthenticated', 'unauth')->name('unauthenticated');
});

/*
|--------------------------------------------------------------------------
| API Routes for Administrator
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\API\Admin\Auth\LoginController as AdminLoginController;
use App\Http\Controllers\API\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\API\Admin\Auth\LogoutController as AdminLogoutController;

// Admin Login Route
Route::controller(AdminLoginController::class)->group(function () {
    Route::post('/admin/login', 'login');
});

// Middleware Route API for Admin.
Route::middleware(['auth:admin-api'])->group(function () {
    // Admin Data Route
    Route::controller(AdminDashboardController::class)->group(function () {
        Route::get('/admin/data', 'auth');
    });
    // Logout Route
    Route::controller(AdminLogoutController::class)->group(function () {
        Route::post('/admin/logout', 'logout');
    });
});


/*
|--------------------------------------------------------------------------
| API Routes for User
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\API\Doctor\Auth\LoginController as DoctorLoginController;
use App\Http\Controllers\API\Doctor\Auth\RegisterController as DoctorRegisterController;
use App\Http\Controllers\API\Doctor\DashboardController as DoctorDashboardController;
use App\Http\Controllers\API\Doctor\Auth\LogoutController as DoctorLogoutController;

// Doctor Login Route
Route::controller(DoctorLoginController::class)->group(function () {
    Route::post('/doctor/login', 'login');
});
// Doctor Register Route
Route::controller(DoctorRegisterController::class)->group(function () {
    Route::post('/doctor/register', 'register');
});

// Middleware Route API for Doctor.
Route::middleware(['auth:doctor-api'])->group(function () {
    // Doctor Data Route
    Route::controller(DoctorDashboardController::class)->group(function () {
        Route::get('/doctor/data', 'auth');
    });
    // Logout Route
    Route::controller(DoctorLogoutController::class)->group(function () {
        Route::post('/doctor/logout', 'logout');
    });
});


/*
|--------------------------------------------------------------------------
| API Routes for User
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\API\User\Auth\LoginController as UserLoginController;
use App\Http\Controllers\API\User\Auth\RegisterController as UserRegisterController;
use App\Http\Controllers\API\User\DashboardController as UserDashboardController;

// User Login Route
Route::controller(UserLoginController::class)->group(function () {
    Route::post('/user/login', 'login');
});

// User Register Route
Route::controller(UserRegisterController::class)->group(function () {
    Route::post('/user/register', 'register');
});

// Middleware Route API for User.
Route::middleware(['auth:user-api'])->group(function () {
    // User Data Route
    Route::controller(UserDashboardController::class)->group(function () {
        Route::get('/user/data', 'auth');
    });
});
