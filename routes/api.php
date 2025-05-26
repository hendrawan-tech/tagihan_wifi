<?php

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\HistoryController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\OperationalController;
use App\Http\Controllers\Api\PackageController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VillageController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::get('/change-password', [AuthController::class, 'updatePassword']);

    Route::get('/invoices', [InvoiceController::class, 'getAll']);
    Route::get('/invoices/{id}', [InvoiceController::class, 'show']);
    Route::post('/invoices/create', [InvoiceController::class, 'createInvoice']);
    Route::post('/invoices/payment', [InvoiceController::class, 'payment']);
    Route::post('/invoices/confirm', [InvoiceController::class, 'confirm']);
    Route::post('/invoices/bulk-invoice/{token}', [InvoiceController::class, 'bulkInvoice']);

    Route::resource('villages', VillageController::class);
    Route::resource('packages', PackageController::class);
    Route::resource('operationals', OperationalController::class);
    Route::resource('histories', HistoryController::class);
    Route::resource('users', UserController::class);

    Route::resource('customers', CustomerController::class);
    Route::get('/dashboard', [ApiController::class, 'index']);
});
