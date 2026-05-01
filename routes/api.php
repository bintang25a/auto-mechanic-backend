<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Email verify
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->middleware('signed')->name('verification.verify');

// Email resend
Route::post('/email/resend', [AuthController::class, 'resendVerifyEmail'])->middleware('auth:api');

// Auth Routes
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register']);

// Basic Routes
Route::middleware('auth:api')->group(function () {
    // Auth Routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Admin Routes
    Route::middleware('role:admin')->group(function () {
        Route::apiResource('users', UserController::class);
    });

    // Staff Routes
    Route::middleware('role:staff')->group(function () {
        Route::apiResource('users', UserController::class)->only(['show']);
    });
});
