<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\DashboardController;

Route::prefix('v1')->group(function () {
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        // Auth
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::put('/auth/profile', [AuthController::class, 'profile']);
        Route::put('/auth/password', [AuthController::class, 'password']);

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index']);

        // Transactions
        Route::get('/transactions/export', [TransactionController::class, 'export']);
        Route::apiResource('transactions', TransactionController::class);

        // Categories
        Route::apiResource('categories', CategoryController::class);

        // Payment Methods
        Route::apiResource('payment-methods', PaymentMethodController::class);

        // Receipts
        Route::post('/receipts', [ReceiptController::class, 'store']);
        Route::get('/receipts/{receipt}', [ReceiptController::class, 'show']);
        Route::get('/receipts/{receipt}/status', [ReceiptController::class, 'status']);
        Route::get('/receipts/{receipt}/image', [ReceiptController::class, 'image']);
        Route::post('/receipts/{receipt}/process', [ReceiptController::class, 'process']);
        Route::post('/receipts/{receipt}/confirm', [ReceiptController::class, 'confirm']);
        Route::post('/receipts/{receipt}/retry', [ReceiptController::class, 'retry']);
        Route::delete('/receipts/{receipt}', [ReceiptController::class, 'destroy']);

        // Budgets
        Route::apiResource('budgets', BudgetController::class);

        // Reports
        Route::get('/reports/monthly', [ReportController::class, 'monthly']);
    });
});
