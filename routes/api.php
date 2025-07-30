<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\UserAuthController;
use App\Http\Controllers\Auth\MemberAuthController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Http\Request;


Route::post('/login-user', [UserAuthController::class, 'login']);
//Route::post('/login-member', [MemberAuthController::class, 'login']);
Route::post('/register-user', [RegisterController::class, 'registerUser']);
Route::middleware('auth:sanctum')->post('/register-member', [RegisterController::class, 'registerMember']);
// routes/api.php

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user-profile', function (Request $request) {
        return $request->user();
    });

    Route::get('/member-profile', function (Request $request) {
        return $request->user(); // ini bisa user atau member tergantung token
    });

    Route::get('/transactions', [TransactionController::class, 'index']);
    Route::post('/transactions', [TransactionController::class, 'store']);
    Route::get('/transactions/{id}', [TransactionController::class, 'show']);
    Route::put('/transactions/{id}', [TransactionController::class, 'update']);
    Route::delete('/transactions/{id}', [TransactionController::class, 'destroy']);
});
