<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\UserAuthController;
use App\Http\Controllers\Auth\MemberAuthController;
use App\Http\Controllers\Member\TransactionController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\Member\AccountController;
use App\Http\Controllers\Member\CategoryController;
use App\Http\Controllers\Member\BudgetController;
use App\Http\Controllers\Member\DashboardSummaryController;
use Illuminate\Http\Request;

// routes/api.php
Route::post('/login-member', [MemberAuthController::class, 'login']);
Route::middleware('auth:member')->post('/logout-member', [MemberAuthController::class, 'logout']);


Route::middleware('auth:member')->group(function () {
    Route::get('/member-profile', function (Request $request) {
        return $request->user();
    });
    
    // Transaction management
    Route::apiResource('transactions', TransactionController::class);
    
    // Account management
    Route::apiResource('/accounts', AccountController::class);
});
