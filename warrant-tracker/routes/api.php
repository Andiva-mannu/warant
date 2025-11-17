<?php

use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\WarrantyController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// These routes are public (login, register, etc.)
// This file is automatically created when you install Breeze
// If you didn't install Breeze, you'll need to add your own auth routes
require __DIR__.'/auth.php';

// public token-based login
Route::post('login', [AuthController::class, 'login']);

// These routes require authentication
Route::middleware('auth:sanctum')->group(function () {
    
    // Get the currently logged-in user
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Logout (revoke current token)
    Route::post('logout', [AuthController::class, 'logout']);

    // CRUD for products (e.g., GET, POST /api/products)
    Route::apiResource('products', ProductController::class);

    // CRUD for the logged-in user's warranties
    Route::apiResource('warranties', WarrantyController::class);
    Route::patch('warranties/{warranty}/claim', [WarrantyController::class, 'claim']);
    // CRUD for managing users (admin-only)
    // Include index, show, store, update, destroy. Creating users can be done by admin via POST /api/users
    Route::apiResource('users', UserController::class)->only([
        'index', 'show', 'store', 'update', 'destroy'
    ]);
});

