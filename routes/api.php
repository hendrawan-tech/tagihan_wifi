<?php

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\PackageController;
use App\Http\Controllers\Api\VillageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::get('/change-password', [AuthController::class, 'updatePassword']);

    Route::get('/invoces', [InvoiceController::class, 'getAll']);
    Route::get('/invoces/{customer}', [InvoiceController::class, 'getByCustomer']);
    Route::get('/invoces/generate', [InvoiceController::class, 'generateInvoice']);
    Route::post('/invoces/payment', [InvoiceController::class, 'payment']);
    Route::post('/invoces/bulk-invoice/{token}', [InvoiceController::class, 'bulkInvoice']);

    Route::resource('villages', VillageController::class);
    Route::resource('packages', PackageController::class);

    Route::get('/customer-village/{village}', [ApiController::class, 'getByVillage']);
    Route::resource('customers', CustomerController::class);
});
