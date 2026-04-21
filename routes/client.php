<?php

use App\Http\Controllers\Client\Auth\LoginController;
use App\Http\Controllers\Client\Auth\RegistrationController;
use App\Http\Controllers\Client\Billing\InvoiceController;
use App\Http\Controllers\Client\Billing\TransactionController;
use App\Http\Controllers\Client\CandidatesController;
use App\Http\Controllers\Client\DashboardController;
use App\Http\Controllers\Client\NotificationController;
use App\Http\Controllers\Client\Request\CreateRequestController;
use App\Http\Controllers\Client\Request\OldRequestController;
use App\Http\Controllers\Client\Request\TrackRequestController;
use App\Http\Controllers\Client\Request\ViewRequestController;
use App\Http\Controllers\Client\Settings\AccountController;
use App\Http\Controllers\Client\Settings\AgreementController;
use App\Http\Controllers\Client\Settings\PackageController;
use App\Http\Controllers\Client\Settings\ProfileController;
use App\Http\Controllers\Client\Settings\SecurityController;
use App\Http\Controllers\Client\Settings\UserController;
use Illuminate\Support\Facades\Route;

Route::name('client.')->group(function () {

    // Auth (unauthenticated)
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'submit'])->name('login.submit');
    Route::get('/verification', [LoginController::class, 'verification'])->name('verification');
    Route::post('/verification', [LoginController::class, 'verifyCode'])->name('verification.submit');
    Route::post('/verification/resend', [LoginController::class, 'resend'])->name('verification.resend');
    Route::get('/forgot-password', [LoginController::class, 'forgot'])->name('forgot');
    Route::post('/forgot-password', [LoginController::class, 'sendReset'])->name('forgot.submit');
    Route::get('/reset-password/{token}', [LoginController::class, 'reset'])->name('reset');
    Route::post('/reset-password', [LoginController::class, 'processReset'])->name('reset.process');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Registration
    Route::get('/register', [RegistrationController::class, 'index'])->name('register');
    Route::post('/register', [RegistrationController::class, 'submit'])->name('register.submit');
    Route::get('/register/success', [RegistrationController::class, 'success'])->name('register.success');

    // Authenticated routes
    Route::middleware('client.auth')->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Candidates
        Route::get('/candidates', [CandidatesController::class, 'index'])->name('candidates');
        Route::get('/candidates/{id}', [CandidatesController::class, 'show'])->name('candidates.show');

        // Request — Create
        Route::prefix('request')->name('request.')->group(function () {
            Route::get('/new', [CreateRequestController::class, 'index'])->name('new');
            Route::get('/malaysia', [CreateRequestController::class, 'malaysia'])->name('malaysia');
            Route::get('/global', [CreateRequestController::class, 'global'])->name('global');
            Route::get('/kyc', [CreateRequestController::class, 'kyc'])->name('kyc');
            Route::get('/kyb', [CreateRequestController::class, 'kyb'])->name('kyb');
            Route::get('/kys', [CreateRequestController::class, 'kys'])->name('kys');
            Route::post('/submit', [CreateRequestController::class, 'submit'])->name('submit');
            Route::post('/due-diligence/submit', [CreateRequestController::class, 'submitDueDiligence'])->name('due-diligence.submit');
            Route::get('/success', [CreateRequestController::class, 'successful'])->name('success');
        });

        // Request — View (active)
        Route::prefix('requests')->name('requests.')->group(function () {
            Route::get('/', [ViewRequestController::class, 'index'])->name('index');
            Route::get('/search', [ViewRequestController::class, 'index'])->name('search');
            Route::get('/track', [TrackRequestController::class, 'index'])->name('track');
            Route::post('/track', [TrackRequestController::class, 'search'])->name('track.search');
            Route::get('/{id}', [ViewRequestController::class, 'details'])->name('details');
        });

        // History
        Route::prefix('history')->name('history.')->group(function () {
            Route::get('/', [OldRequestController::class, 'index'])->name('index');
            Route::get('/{id}', [OldRequestController::class, 'details'])->name('details');
        });

        // Billing
        Route::prefix('billing')->name('billing.')->group(function () {
            Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions');
            Route::get('/transactions/{id}/receipt', [TransactionController::class, 'receipt'])->name('transactions.receipt');
            Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices');
            Route::get('/invoices/{id}', [InvoiceController::class, 'show'])->name('invoices.show');
            Route::get('/invoices/{id}/download', [InvoiceController::class, 'download'])->name('invoices.download');
        });

        // Notifications
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');

        // Settings
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/account', [AccountController::class, 'index'])->name('account');
            Route::post('/account', [AccountController::class, 'update'])->name('account.update');
            Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
            Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
            Route::get('/users', [UserController::class, 'index'])->name('users');
            Route::get('/packages', [PackageController::class, 'index'])->name('packages');
            Route::get('/security', [SecurityController::class, 'index'])->name('security');
            Route::post('/security', [SecurityController::class, 'update'])->name('security.update');
            Route::get('/agreement', [AgreementController::class, 'index'])->name('agreement');
        });
    });
});
