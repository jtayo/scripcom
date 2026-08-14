<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
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

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth'])
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

        Route::resource('organizations', App\Http\Controllers\OrganizationController::class)
            ->middleware('can:view-any-organization');

        Route::resource('users', App\Http\Controllers\UserController::class)
            ->middleware('can:view-any-user');

        Route::resource('hotspots', App\Http\Controllers\HotspotController::class)
            ->middleware('can:view-any-hotspot');

        Route::resource('campaigns', App\Http\Controllers\CampaignController::class)
            ->middleware('can:view-any-campaign');

        Route::resource('sponsors', App\Http\Controllers\SponsorController::class)
            ->middleware('can:view-any-sponsor');

        Route::resource('sponsorships', App\Http\Controllers\SponsorshipController::class)
            ->middleware('can:view-any-sponsorship');

        Route::resource('sessions', App\Http\Controllers\WifiSessionController::class)
            ->only(['index', 'show', 'destroy'])
            ->middleware('can:view-any-session');

        Route::resource('events', App\Http\Controllers\EventController::class)
            ->only(['index', 'show'])
            ->middleware('can:view-any-event');

        Route::resource('vouchers', App\Http\Controllers\VoucherController::class)
            ->except(['edit', 'update'])
            ->middleware('can:view-any-voucher');

        Route::resource('payments', App\Http\Controllers\PaymentController::class)
            ->only(['index', 'show'])
            ->middleware('can:view-any-payment');

        Route::get('/buy-credits', [App\Http\Controllers\BuyCreditsController::class, 'index'])
            ->middleware('can:buy-credits')
            ->name('buy-credits');
        Route::post('/buy-credits', [App\Http\Controllers\BuyCreditsController::class, 'store'])
            ->middleware('can:buy-credits')
            ->name('buy-credits.store');

        Route::get('/settings', [App\Http\Controllers\SettingsController::class, 'index'])
            ->middleware('can:view-settings')
            ->name('settings');
        Route::post('/settings', [App\Http\Controllers\SettingsController::class, 'update'])
            ->middleware('can:update-settings')
            ->name('settings.update');
    });
