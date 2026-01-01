<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\OutletController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\TransactionController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/login', [AuthController::class, 'login']);

// Public routes for QR Order (customer)
Route::get('/public/categories', [CategoryController::class, 'index']);
Route::get('/public/products', [ProductController::class, 'index']);
Route::post('/public/orders', [TransactionController::class, 'store']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/reports/sales', [DashboardController::class, 'salesReport']);

    // Outlets (Owner only)
    Route::apiResource('outlets', OutletController::class);
    Route::post('/outlets/{outlet}/generate-qr', [OutletController::class, 'generateQrCodes']);

    // Categories
    Route::apiResource('categories', CategoryController::class);

    // Products
    Route::apiResource('products', ProductController::class);
    Route::post('/products/find-barcode', [ProductController::class, 'findByBarcode']);
    Route::post('/products/{product}/generate-barcode', [ProductController::class, 'generateBarcode']);

    // Transactions
    Route::apiResource('transactions', TransactionController::class);
    Route::post('/transactions/{transaction}/void', [TransactionController::class, 'void']);
});
