<?php

namespace App\Providers;

use App\Services\AnalyticsService;
use App\Services\CaptivePortalService;
use App\Services\EventService;
use App\Services\KenyaWardLookup;
use App\Services\MpesaService;
use App\Services\OtpService;
use App\Services\SessionManager;
use App\Services\TolclinApiService;
use App\Services\VoucherService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(TolclinApiService::class);
        $this->app->singleton(MpesaService::class);
        $this->app->singleton(AnalyticsService::class);
        $this->app->singleton(EventService::class);
        $this->app->singleton(OtpService::class);
        $this->app->singleton(VoucherService::class);
        $this->app->singleton(SessionManager::class);
        $this->app->singleton(CaptivePortalService::class);
        $this->app->singleton(KenyaWardLookup::class);
    }

    public function boot(): void
    {
        Model::unguard();

        if (! app()->isProduction()) {
            Model::shouldBeStrict();
        }
    }
}
