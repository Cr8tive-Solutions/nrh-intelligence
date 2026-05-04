<?php

use App\Http\Controllers\Client\Auth\InvitationController;
use App\Http\Controllers\Client\Auth\LoginController;
use App\Http\Controllers\Client\Auth\RegistrationController;
use App\Http\Controllers\Client\Billing\InvoiceController;
use App\Http\Controllers\Client\Billing\TransactionController;
use App\Http\Controllers\Client\CandidatesController;
use App\Http\Controllers\Client\DashboardController;
use App\Http\Controllers\Client\NotificationController;
use App\Http\Controllers\Client\Request\CreateRequestController;
use App\Http\Controllers\Client\Request\OldRequestController;
use App\Http\Controllers\Client\Request\ReportDownloadController;
use App\Http\Controllers\Client\Request\TrackRequestController;
use App\Http\Controllers\Client\Request\ViewRequestController;
use App\Http\Controllers\Client\Settings\AccountController;
use App\Http\Controllers\Client\Settings\AgreementController;
use App\Http\Controllers\Client\Settings\AuditLogController;
use App\Http\Controllers\Client\Settings\PackageController;
use App\Http\Controllers\Client\Settings\ProfileController;
use App\Http\Controllers\Client\Settings\SecurityController;
use App\Http\Controllers\Client\Settings\UserController;
use App\Http\Controllers\Client\SystemUpdateController;
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

    // Invitation acceptance (admin portal sends activation links here)
    Route::get('/invitation/{token}', [InvitationController::class, 'show'])->name('invitation.show');
    Route::post('/invitation/{token}', [InvitationController::class, 'accept'])
        ->middleware('throttle:5,1')
        ->name('invitation.accept');

    // Authenticated routes
    Route::middleware('client.auth')->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('permission:view-dashboard')->name('dashboard');

        // Candidates
        Route::middleware('permission:view-candidates')->group(function () {
            Route::get('/candidates', [CandidatesController::class, 'index'])->name('candidates');
            Route::get('/candidates/{id}', [CandidatesController::class, 'show'])->name('candidates.show');
        });

        // Request — Create
        Route::prefix('request')->name('request.')->group(function () {
            Route::middleware('permission:create-requests')->group(function () {
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
        });

        // Request — View (active)
        Route::prefix('requests')->name('requests.')->middleware('permission:view-requests')->group(function () {
            Route::get('/', [ViewRequestController::class, 'index'])->name('index');
            Route::get('/search', [ViewRequestController::class, 'index'])->name('search');
            Route::get('/track', [TrackRequestController::class, 'index'])->name('track');
            Route::get('/track/search', [TrackRequestController::class, 'search'])->name('track.search.get');
            Route::post('/track', [TrackRequestController::class, 'search'])->name('track.search');
            Route::get('/{request}/reports/{version}/download', [ReportDownloadController::class, 'download'])->name('reports.download');
            Route::get('/{id}', [ViewRequestController::class, 'details'])->name('details');
        });

        // History
        Route::prefix('history')->name('history.')->middleware('permission:view-reports')->group(function () {
            Route::get('/', [OldRequestController::class, 'index'])->name('index');
            Route::get('/{id}', [OldRequestController::class, 'details'])->name('details');
        });

        // Billing
        Route::prefix('billing')->name('billing.')->middleware('permission:view-billing')->group(function () {
            Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions');
            Route::get('/transactions/{id}/receipt', [TransactionController::class, 'receipt'])->name('transactions.receipt');
            Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices');
            Route::get('/invoices/{id}', [InvoiceController::class, 'show'])->name('invoices.show');
            Route::get('/invoices/{id}/download', [InvoiceController::class, 'download'])->name('invoices.download');
        });

        // Notifications
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');

        // System updates
        Route::get('/updates', [SystemUpdateController::class, 'index'])->name('updates');

        // Settings
        Route::prefix('settings')->name('settings.')->group(function () {
            // Profile is per-user — always available
            Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
            Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
            Route::delete('/profile/avatar', [ProfileController::class, 'removeAvatar'])->name('profile.avatar.remove');

            // Account / Security / Agreement — manage-settings
            Route::middleware('permission:manage-settings')->group(function () {
                Route::get('/account', [AccountController::class, 'index'])->name('account');
                Route::post('/account', [AccountController::class, 'update'])->name('account.update');
                Route::get('/security', [SecurityController::class, 'index'])->name('security');
                Route::post('/security', [SecurityController::class, 'update'])->name('security.update');
                Route::get('/agreement', [AgreementController::class, 'index'])->name('agreement');
            });

            // Team management — manage-team
            Route::middleware('permission:manage-team')->group(function () {
                Route::get('/users', [UserController::class, 'index'])->name('users');
                Route::post('/users', [UserController::class, 'store'])->name('users.store');
                Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
                Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
                Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
                Route::post('/users/{user}/resend-invitation', [UserController::class, 'resend'])->name('users.resend-invitation');
            });

            // Packages — manage-packages
            Route::get('/packages', [PackageController::class, 'index'])->middleware('permission:manage-packages')->name('packages');

            // Audit log — view-audit-log
            Route::get('/audit-log', [AuditLogController::class, 'index'])->middleware('permission:view-audit-log')->name('audit-log');
        });
    });
});
