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
use App\Http\Controllers\API\Admin\AppointmentController as AdminAppointmentController;
use App\Http\Controllers\API\Admin\DoctorController as AdminDoctorController;
use App\Http\Controllers\API\Admin\UserController as AdminUserController;
use App\Http\Controllers\API\Admin\QueueController as AdminQueuingController;
use App\Http\Controllers\API\Admin\PostController as AdminPostController;
use App\Http\Controllers\API\Admin\Auth\LogoutController as AdminLogoutController;

use App\Http\Controllers\API\Admin\PDFController as AdminPDFController;
Route::controller(AdminPDFController::class)->group(function () {
    Route::get('/admin/download/{id}', 'download');
});


// Admin Login Route
Route::controller(AdminLoginController::class)->group(function () {
    Route::post('/admin/login', 'login');
});

// Middleware Route API for Admin.
Route::middleware(['auth:admin-api'])->group(function () {
    // Admin Data Route
    Route::controller(AdminDashboardController::class)->group(function () {
        Route::get('/admin/data', 'auth');
        Route::get('/admin/count', 'index');
    });
    Route::controller(AdminAppointmentController::class)->group(function () {
        Route::get('/admin/appointment', 'index');
    });
    // Admin Doctor Route
    Route::controller(AdminDoctorController::class)->group(function () {
        Route::get('/admin/doctor', 'index');
        Route::put('/admin/doctor/{id}', 'update');
        Route::delete('/admin/doctor/{id}', 'destroy');
    });
    // Admin Data Route
    Route::controller(AdminUserController::class)->group(function () {
        Route::get('/admin/user', 'index');
        Route::put('/admin/user/{id}', 'update');
        Route::delete('/admin/user/{id}', 'destroy');
    });
    // Admin Queuing Controller
    Route::controller(AdminQueuingController::class)->group(function () {
        Route::get('/admin/queue', 'index');
    });
    // Admin Post Controller
    Route::controller(AdminPostController::class)->group(function () {
        Route::get('/admin/post', 'index');
        Route::post('/admin/post', 'store');
        Route::delete('/admin/post/{id}', 'destroy');
    });
    // Logout Route
    Route::controller(AdminLogoutController::class)->group(function () {
        Route::post('/admin/logout', 'logout');
    });
});


/*
|--------------------------------------------------------------------------
| API Routes for Doctor
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\API\Doctor\Auth\LoginController as DoctorLoginController;
use App\Http\Controllers\API\Doctor\Auth\RegisterController as DoctorRegisterController;
use App\Http\Controllers\API\Doctor\DashboardController as DoctorDashboardController;
use App\Http\Controllers\API\Doctor\ScheduleController as DoctorScheduleController;
use App\Http\Controllers\API\Doctor\AppointmentController as DoctorAppointmentController;
use App\Http\Controllers\API\Doctor\QueuingController as DoctorQueuingController;
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
        Route::get('/doctor/count', 'index');
    });
    // Doctor Schedule Controller
    Route::controller(DoctorScheduleController::class)->group(function () {
        Route::get('/doctor/schedule', 'index');
        Route::post('/doctor/schedule', 'store');
        Route::delete('/doctor/schedule/{id}', 'destroy');
    });
    // Doctor Appointment Controller
    Route::controller(DoctorAppointmentController::class)->group(function () {
        Route::get('/doctor/appointment', 'index');
    });
    // Doctor Queuing Controller
    Route::controller(DoctorQueuingController::class)->group(function () {
        Route::get('/doctor/queue', 'index');
        Route::post('/doctor/queue', 'store');
        Route::delete('/doctor/queue/{id}', 'destroy');
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
use App\Http\Controllers\API\User\AppointmentController as UserAppointmentController;
use App\Http\Controllers\API\User\DoctorController as UserDoctorController;
use App\Http\Controllers\API\User\PostController as UserPostController;
use App\Http\Controllers\API\User\Auth\LogoutController as UserLogoutController;

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
        Route::get('/user/count', 'index');
    });
    // User Doctor Route
    Route::controller(UserDoctorController::class)->group(function () {
        Route::get('/user/doctor', 'index');
        Route::get('/user/doctor/{id}', 'show');
    });
    // User Appointment Route
    Route::controller(UserAppointmentController::class)->group(function () {
        Route::get('/user/appointment', 'index');
        Route::get('/user/appointment/{id}', 'show');
        Route::post('/user/appointment', 'store');
    });
    // User Post Controller
    Route::controller(UserPostController::class)->group(function () {
        Route::get('/user/post', 'index');
    });
    // User Logout Route
    Route::controller(UserLogoutController::class)->group(function () {
        Route::post('/user/logout', 'logout');
    });
});
