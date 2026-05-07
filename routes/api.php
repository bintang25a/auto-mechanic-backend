<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\DamageController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\RuleController;
use App\Http\Controllers\SymptomController;
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

    // Routes
    Route::apiResource('damages', DamageController::class)->only(['index']);
    Route::apiResource('symptoms', SymptomController::class)->only(['index']);
    Route::apiResource('rules', RuleController::class)->only(['index']);
    Route::apiResource('complaints', ComplaintController::class)->except(['index']);

    // Admin and Staff Routes
    Route::middleware('role:admin,staff')->group(function () {
        Route::apiResource('users', UserController::class)->only(['show']);
        Route::apiResource('damages', DamageController::class)->except(['index']);
        Route::apiResource('symptoms', SymptomController::class)->except(['index']);
        Route::apiResource('complaints', ComplaintController::class)->only(['index']);
    });

    // Admin Routes
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin-page-rules', [PageController::class, 'adminPageRules']);
        Route::apiResource('users', UserController::class)->except(['show']);
        Route::apiResource('rules', RuleController::class)->only(['store', 'destroy']);
    });

    // Staff Routes
    Route::middleware('role:staff')->group(function () {
        // Route::apiResource('users', UserController::class)->only(['show']);
    });
});
