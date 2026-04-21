<?php

use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\RequestController;
use Illuminate\Support\Facades\Route;

Route::name('admin.')->group(function () {

    // Auth
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'submit'])->name('login.submit');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Authenticated
    Route::middleware('admin.auth')->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Customers
        Route::prefix('customers')->name('customers.')->group(function () {
            Route::get('/', [CustomerController::class, 'index'])->name('index');
            Route::get('/{id}', [CustomerController::class, 'show'])->name('show');
        });

        // Requests
        Route::prefix('requests')->name('requests.')->group(function () {
            Route::get('/', [RequestController::class, 'index'])->name('index');
            Route::get('/{id}', [RequestController::class, 'show'])->name('show');
            Route::patch('/{id}/status', [RequestController::class, 'updateStatus'])->name('status');
            Route::patch('/candidates/{id}/status', [RequestController::class, 'updateCandidateStatus'])->name('candidate.status');
        });

    });
});
