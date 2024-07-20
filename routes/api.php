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
