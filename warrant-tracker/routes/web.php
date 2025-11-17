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

    // Session-backed JSON endpoints so the JS can call them using cookies
    Route::get('users/data', [WebUserController::class, 'index'])->name('users.data.index');
    Route::get('users/data/{user}', [WebUserController::class, 'show'])->name('users.data.show');
    Route::post('users/data', [WebUserController::class, 'store'])->name('users.data.store');
    Route::put('users/data/{user}', [WebUserController::class, 'update'])->name('users.data.update');
    Route::delete('users/data/{user}', [WebUserController::class, 'destroy'])->name('users.data.destroy');
});
