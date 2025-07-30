<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\UserAuthController;
use App\Http\Controllers\Auth\MemberAuthController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\DashboardSummaryController;
use Illuminate\Http\Request;

// routes/api.php


Route::post('/login-user', [UserAuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout-user', [UserAuthController::class, 'logout']);
Route::post('/login-member', [MemberAuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout-member', [MemberAuthController::class, 'logout']);
Route::post('/register-user', [RegisterController::class, 'registerUser']);
Route::middleware('auth:sanctum')->post('/register-member', [RegisterController::class, 'registerMember']);
Route::middleware('auth:sanctum')->get('/debug-user', function (Request $request) {
    return response()->json($request->user());
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user-profile', function (Request $request) {
        return $request->user();
    });

    Route::get('/member-profile', function (Request $request) {
        return $request->user();
    });
    

    Route::middleware('auth:sanctum')->get('/members', function (Request $request) {
        return $request->user()->members;
    });

    // Member management
    Route::apiResource('members', MemberController::class);

    // Transaction management
    Route::apiResource('transactions', TransactionController::class);
    
    // Category management
    Route::apiResource('categories', CategoryController::class);

    // Budget management
    Route::apiResource('/budgets', BudgetController::class);

    Route::middleware('auth:sanctum')->get('/dashboard/summary', [DashboardSummaryController::class, 'index']);
});
