<?php

use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Controllers\CsrfCookieController;

// ✅ Route khusus untuk meng-handle request csrf-cookie (wajib untuk Sanctum + SPA)
Route::middleware('web')->get('/sanctum/csrf-cookie', [CsrfCookieController::class, 'show']);

// ✅ Route default Laravel
Route::get('/', function () {
    return view('welcome');
});
