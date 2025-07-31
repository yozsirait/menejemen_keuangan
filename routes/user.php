<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\UserAuthController;
use App\Http\Controllers\User\TransactionController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\User\MemberController;
use App\Http\Controllers\User\AccountController;
use App\Http\Controllers\User\CategoryController;
use App\Http\Controllers\User\BudgetController;
use App\Http\Controllers\User\DashboardSummaryController;
use Illuminate\Http\Request;

// routes/api.php
// Untuk user login (dashboard via Vue)
//Route::post('/login-user', [UserAuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout-user', [UserAuthController::class, 'logout']);
Route::post('/register-user', [RegisterController::class, 'registerUser']);
Route::middleware('auth:sanctum')->post('/register-member', [RegisterController::class, 'registerMember']);


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user-profile', function (Request $request) {
        return $request->user();
    });

    // Route::middleware('auth:sanctum')->get('/members', function (Request $request) {
    //     return $request->user()->members;
    // });

    // Member management
    Route::apiResource('members', MemberController::class);

    // Transaction management
    Route::apiResource('transactions', TransactionController::class);
    
    // Category management
    Route::apiResource('categories', CategoryController::class);

    // Budget management
    
    Route::get('categories/{category}/budgets', [BudgetController::class, 'index']);
    Route::post('categories/{category}/budgets', [BudgetController::class, 'store']);
    Route::get('categories/{category}/budgets/{id}', [BudgetController::class, 'show']);
    Route::put('categories/{category}/budgets/{id}', [BudgetController::class, 'update']);
    Route::delete('categories/{category}/budgets/{id}', [BudgetController::class, 'destroy']);
    


    // Account management
    Route::apiResource('accounts', AccountController::class);
    Route::get('accounts/{id}/balance', [AccountController::class, 'checkBalance']);
    Route::middleware('auth:sanctum')->get('/dashboard/summary', [DashboardSummaryController::class, 'index']);
});
