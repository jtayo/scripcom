<?php

namespace App\Providers;

use App\Models\Campaign;
use App\Models\Contract;
use App\Models\ContractCampaign;
use App\Models\Event;
use App\Models\Hotspot;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Organization;
use App\Models\Payment;
use App\Models\RevenueRecord;
use App\Models\Router;
use App\Models\RouterHealthLog;
use App\Models\Setting;
use App\Models\Sponsor;
use App\Models\Sponsorship;
use App\Models\TolclinEvent;
use App\Models\User;
use App\Models\Voucher;
use App\Models\WifiPackage;
use App\Models\WifiSession;
use App\Observers\AuditObserver;
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
use Illuminate\Pagination\Paginator;
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

        Paginator::useBootstrapFive();

        $auditObserver = new AuditObserver;

        foreach ([
            Organization::class,
            User::class,
            Hotspot::class,
            Router::class,
            RouterHealthLog::class,
            Campaign::class,
            Sponsor::class,
            Sponsorship::class,
            WifiPackage::class,
            WifiSession::class,
            Event::class,
            TolclinEvent::class,
            Contract::class,
            ContractCampaign::class,
            Invoice::class,
            InvoiceItem::class,
            Payment::class,
            Voucher::class,
            Setting::class,
            RevenueRecord::class,
        ] as $model) {
            $model::observe($auditObserver);
        }
    }
}
