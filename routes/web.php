<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\Admin\PDFController as AdminPDFController;
Route::controller(AdminPDFController::class)->group(function () {
    Route::get('/admin/download/{id}', 'download');
});


Route::get('/', function () {
    return view('welcome');
});
Route::get('/{pathMatch}', function () {
    return view('welcome');
})->where('pathMatch', ".*");
