<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Api\UserController as WebUserController;

Route::get('/', function () {
    return view('welcome');
});

// Simple web auth + UI routes
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login'])->name('login.post');
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    // Simple user management UI (frontend hits the JSON endpoints below)
    Route::get('users', function () {
        return view('users.index');
    })->name('users.index');
});
