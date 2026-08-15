<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\BuyCreditsController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\HotspotController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SponsorController;
use App\Http\Controllers\SponsorshipController;
use App\Http\Controllers\TolclinWebhookController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WifiSessionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('admin.dashboard') : redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::post('webhooks/tolclin', TolclinWebhookController::class)
    ->name('webhooks.tolclin');

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth'])
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

        Route::resource('organizations', OrganizationController::class)
            ->middleware('can:view-any-organization');

        Route::resource('users', UserController::class)
            ->middleware('can:view-any-user');

        Route::resource('roles', RoleController::class)
            ->middleware('can:view-any-role');

        Route::resource('permissions', PermissionController::class)
            ->only(['index', 'show'])
            ->middleware('can:view-any-permission');

        Route::resource('hotspots', HotspotController::class)
            ->middleware('can:view-any-hotspot');

        Route::resource('campaigns', CampaignController::class)
            ->middleware('can:view-any-campaign');

        Route::resource('sponsors', SponsorController::class)
            ->middleware('can:view-any-sponsor');

        Route::resource('sponsorships', SponsorshipController::class)
            ->middleware('can:view-any-sponsorship');

        Route::resource('sessions', WifiSessionController::class)
            ->only(['index', 'show', 'destroy'])
            ->middleware('can:view-any-session');

        Route::resource('events', EventController::class)
            ->only(['index', 'show'])
            ->middleware('can:view-any-event');

        Route::resource('payments', PaymentController::class)
            ->only(['index', 'show'])
            ->middleware('can:view-any-payment');

        Route::get('/buy-credits', [BuyCreditsController::class, 'index'])
            ->middleware('can:buy-credits')
            ->name('buy-credits');
        Route::post('/buy-credits', [BuyCreditsController::class, 'store'])
            ->middleware('can:buy-credits')
            ->name('buy-credits.store');

        Route::get('/settings', [SettingsController::class, 'index'])
            ->middleware('can:view-settings')
            ->name('settings');
        Route::post('/settings', [SettingsController::class, 'update'])
            ->middleware('can:update-settings')
            ->name('settings.update');

        Route::prefix('reports')
            ->middleware('can:view-reports')
            ->group(function () {
                Route::get('/', [ReportController::class, 'index'])->name('reports.index');
                Route::get('{type}', [ReportController::class, 'show'])->name('reports.show');
                Route::get('{type}/export/{format}', [ReportController::class, 'export'])
                    ->middleware('can:export-reports')
                    ->name('reports.export');
            });
    });
