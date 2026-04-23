<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StaffAuthController;
use App\Http\Controllers\Api\StaffInvoiceController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\TemplateController;

// Public routes
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/staff/auth', [StaffAuthController::class, 'login']);

// Public reference data
Route::get('/cities', [CityController::class, 'index']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/profile', [AuthController::class, 'profile']);

    // Staff
    Route::post('/staff/logout', [StaffAuthController::class, 'logout']);
    Route::get('/staff/profile', [StaffAuthController::class, 'profile']);
    Route::get('/staff/dashboard', [StaffInvoiceController::class, 'dashboard']);
    Route::get('/staff/history', [StaffInvoiceController::class, 'history']);
    Route::get('/staff/invoices', [StaffInvoiceController::class, 'index']);
    Route::get('/staff/invoices/by-number/{number}', [StaffInvoiceController::class, 'findByNumber']);
    Route::get('/staff/invoices/{id}', [StaffInvoiceController::class, 'show'])->whereNumber('id');
    Route::post('/staff/invoices/{id}/pickup', [StaffInvoiceController::class, 'pickup'])->whereNumber('id');
    Route::post('/staff/invoices/{id}/receive', [StaffInvoiceController::class, 'receive'])->whereNumber('id');
    Route::post('/staff/invoices/{id}/ship', [StaffInvoiceController::class, 'ship'])->whereNumber('id');
    Route::post('/staff/invoices/{id}/destination-pickup', [StaffInvoiceController::class, 'destinationPickup'])->whereNumber('id');

    // Invoices
    Route::get('/invoices', [InvoiceController::class, 'index']);
    Route::post('/invoices', [InvoiceController::class, 'store']);
    Route::get('/invoices/stats', [InvoiceController::class, 'stats']);
    Route::get('/invoices/{id}', [InvoiceController::class, 'show']);
    Route::get('/invoices/{id}/pdf', [InvoiceController::class, 'pdf']);

    // Templates
    Route::get('/templates/recipients', [TemplateController::class, 'recipientTemplates']);
    Route::get('/templates/descriptions', [TemplateController::class, 'descriptionTemplates']);
});
