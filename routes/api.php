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

// Admin Login Route
Route::controller(AdminLoginController::class)->group(function () {
    Route::post('/admin/login', 'login');
});

// Middleware Route API for Admin.
Route::middleware(['auth:admin-api', 'throttle:1000,1'])->group(function () {
    // Admin Data Route
    Route::controller(AdminDashboardController::class)->group(function () {
        Route::get('/admin/data', 'auth');
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
Route::middleware(['auth:user-api', 'throttle:1000,1'])->group(function () {
    // User Data Route
    Route::controller(UserDashboardController::class)->group(function () {
        Route::get('/user/data', 'auth');
    });
});
