<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\UserAuthController;
use App\Http\Controllers\Auth\MemberAuthController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\DashboardSummaryController;
use Illuminate\Http\Request;

// routes/api.php
Route::post('/login-member', [MemberAuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout-member', [MemberAuthController::class, 'logout']);


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/member-profile', function (Request $request) {
        return $request->user();
    });
    
    // Transaction management
    Route::apiResource('transactions', TransactionController::class);
    
    // Category management
    Route::apiResource('categories', CategoryController::class);

    // Budget management
    Route::apiResource('/budgets', BudgetController::class);

    // Account management
    Route::apiResource('/accounts', AccountController::class);

    Route::middleware('auth:sanctum')->get('/dashboard/summary', [DashboardSummaryController::class, 'index']);
});
