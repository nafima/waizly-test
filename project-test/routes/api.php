<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\v1;

Route::prefix('v1')->group(function () {
    // User Routes
    Route::prefix('users')->group(function () {
        Route::middleware('App\Http\Middleware\Authorization')->group(function () {
            Route::get('/', [v1\UsersController::class, 'index']);
            Route::post('/create', [v1\UsersController::class, 'create']);
            Route::put('/update/{id}', [v1\UsersController::class, 'update']);
            Route::post('/logout', [v1\UsersController::class, 'logout']);
        });
        // Route untuk login yang tidak menggunakan middleware Authorization
        Route::post('/login', [v1\UsersController::class, 'login'])->name('login');
    });
});