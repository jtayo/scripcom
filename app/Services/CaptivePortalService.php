<?php

namespace App\Services;

use App\Enums\PackageAccessType;
use App\Models\Campaign;
use App\Models\Event;
use App\Models\Hotspot;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\Voucher;
use App\Models\WifiPackage;
use App\Models\WifiSession;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CaptivePortalService
{
    public function detectHotspot(Request $request): ?Hotspot
    {
        $lookup = $request->input('hotspot');

        $query = Hotspot::query()
            ->with('organization')
            ->where('is_active', true);

        if ($lookup) {
            $query->where(function ($q) use ($lookup) {
                $q->where('id', $lookup)->orWhere('slug', $lookup);
            });
        }

        return $query->orderBy('id')->first()
            ?? Hotspot::query()->with('organization')->where('is_active', true)->orderBy('id')->first();
    }

    public function getPortalData(Hotspot $hotspot, ?Campaign $campaign = null): array
    {
        $organization = $hotspot->organization;
        $organizationId = $organization?->id;

        $packages = $this->packagesFor($hotspot);

        $branding = [
            'primary_color' => $organization?->primary_color ?? '#262B40',
            'logo' => $organization?->logo,
        ];

        if ($campaign && $campaign->sponsor) {
            $sponsor = $campaign->sponsor;

            if ($sponsor->brand_color) {
                $branding['primary_color'] = $sponsor->brand_color;
            }

            if ($sponsor->logo) {
                $branding['logo'] = $sponsor->logo;
            }

            $branding['sponsor_name'] = $sponsor->name;
        }

        return [
            'hotspot' => $hotspot,
            'organization' => $organization,
            'location' => $this->locationLabel($hotspot),
            'currency' => Setting::getValue('general.currency', 'KES', $organizationId),
            'default_session_minutes' => Setting::getValue('portal.default_session_minutes', 120, $organizationId),
            'default_bandwidth_mbps' => Setting::getValue('portal.default_bandwidth_mbps', 10, $organizationId),
            'vouchers_enabled' => Setting::getValue('portal.enable_vouchers', true, $organizationId),
            'welcome_message' => Setting::getValue('portal.welcome_message', 'Welcome to free public Wi-Fi.', $organizationId),
            'branding' => $branding,
            'free_package' => $packages->firstWhere('access_type', PackageAccessType::Free->value),
            'sponsored_package' => $packages->firstWhere('access_type', PackageAccessType::Sponsored->value),
            'paid_packages' => $packages->where('access_type', PackageAccessType::Paid->value)->values(),
            'featured_paid_package' => $packages->where('access_type', PackageAccessType::Paid->value)->sortBy('price')->first(),
            'has_sponsored' => $this->getActiveCampaign($hotspot) !== null,
        ];
    }

    public function packagesFor(?Hotspot $hotspot): Collection
    {
        $organizationId = $hotspot?->organization_id;

        return WifiPackage::query()
            ->active()
            ->when($organizationId, function ($q) use ($organizationId) {
                $q->where(fn ($w) => $w->whereNull('organization_id')->orWhere('organization_id', $organizationId));
            }, function ($q) {
                $q->whereNull('organization_id');
            })
            ->orderByRaw("FIELD(access_type, 'free', 'paid', 'sponsored'), price")
            ->get();
    }

    public function getActiveCampaign(Hotspot $hotspot): ?Campaign
    {
        return Campaign::query()
            ->active()
            ->with('sponsor')
            ->whereHas('hotspots', fn ($q) => $q->where('hotspots.id', $hotspot->id))
            ->orderByDesc('priority')
            ->orderBy('current_plays')
            ->first();
    }

    public function startWatch(Hotspot $hotspot, Campaign $campaign, Request $request): array
    {
        $session = WifiSession::create([
            'organization_id' => $hotspot->organization_id,
            'hotspot_id' => $hotspot->id,
            'campaign_id' => $campaign->id,
            'session_id' => (string) Str::uuid(),
            'mac_address' => $this->macAddress($request),
            'device_type' => $this->deviceType($request),
            'browser' => substr((string) $request->userAgent(), 0, 255),
            'ip_address' => $request->ip(),
            'auth_method' => 'sponsored',
            'video_completed' => false,
            'session_started_at' => now(),
            'status' => 'active',
        ]);

        $this->logEvent('video.started', $session, $campaign, $request, [
            'video_id' => $campaign->id,
            'duration_seconds' => $campaign->duration_seconds,
            'device_hash' => $request->input('device_hash'),
        ]);

        return [
            'session' => $session,
            'campaign' => $campaign,
        ];
    }

    public function logProgress(WifiSession $session, Campaign $campaign, float $progress, Request $request): void
    {
        $progress = max(0, min(100, (int) round($progress)));

        $this->logEvent('video.progress', $session, $campaign, $request, [
            'progress' => $progress,
            'device_hash' => $request->input('device_hash'),
        ]);
    }

    public function completeWatch(WifiSession $session, Campaign $campaign, int $watchedSeconds, Request $request): array
    {
        if ($session->video_completed) {
            return ['success' => true, 'session' => $session];
        }

        $required = (int) ceil($campaign->duration_seconds * ($campaign->skip_allowed ? 0.3 : 0.9));

        if ($watchedSeconds < max(1, $required)) {
            $this->logEvent('video.failed', $session, $campaign, $request, [
                'watched_seconds' => $watchedSeconds,
                'required_seconds' => $required,
            ]);

            return ['success' => false, 'message' => "Please watch the full advertisement ({$required}s required).", 'session' => $session];
        }

        $package = WifiPackage::query()
            ->active()
            ->ofType(PackageAccessType::Sponsored->value)
            ->first();

        $durationMinutes = $package?->duration_minutes
            ?? Setting::getValue('portal.default_session_minutes', 120, $campaign->organization_id);

        $session->update([
            'package_id' => $package?->id,
            'video_completed' => true,
            'video_watch_duration' => $watchedSeconds,
            'expires_at' => now()->addMinutes($durationMinutes),
            'status' => 'active',
        ]);

        $campaign->increment('current_plays');

        $this->logEvent('video.completed', $session, $campaign, $request, [
            'watched_seconds' => $watchedSeconds,
            'device_hash' => $request->input('device_hash'),
        ]);
        $this->grantSession($session, $campaign, $request);

        return ['success' => true, 'session' => $session->fresh()];
    }

    public function initiatePaid(WifiPackage $package, string $phone, Hotspot $hotspot, Request $request): array
    {
        if ($package->accessType() !== PackageAccessType::Paid || ! $package->is_active) {
            return ['success' => false, 'message' => 'This package is not available for purchase.'];
        }

        if (! $this->verifyPhone($phone)) {
            return ['success' => false, 'message' => 'Enter a valid Safaricom number (e.g. 0712345678).'];
        }

        $this->logEvent('package.selected', null, null, $request, [
            'package_id' => $package->id,
            'package' => $package->name,
            'price' => (float) $package->price,
            'hotspot_id' => $hotspot->id,
        ]);

        $payment = Payment::create([
            'organization_id' => $hotspot->organization_id,
            'package_id' => $package->id,
            'hotspot_id' => $hotspot->id,
            'phone' => $phone,
            'amount' => (float) $package->price,
            'currency' => Setting::getValue('general.currency', 'KES', $hotspot->organization_id),
            'status' => 'pending',
        ]);

        $this->logEvent('payment.started', null, null, $request, [
            'payment_id' => $payment->id,
            'package_id' => $package->id,
            'amount' => (float) $payment->amount,
            'phone' => Str::mask($phone, '*', 0, 6),
            'hotspot_id' => $hotspot->id,
        ]);

        $mpesa = app(MpesaService::class);
        $result = $mpesa->stkPush($phone, (float) $payment->amount);

        if (! ($result['success'] ?? false)) {
            $payment->update(['status' => 'failed', 'result_description' => $result['error'] ?? 'STK push failed']);
            $this->logEvent('payment.failed', null, null, $request, [
                'payment_id' => $payment->id,
                'error' => $result['error'] ?? 'STK push failed',
            ]);

            return ['success' => false, 'message' => $result['error'] ?? 'Payment could not be initiated.'];
        }

        $payment->update([
            'checkout_request_id' => $result['checkout_request_id'] ?? null,
            'transaction_id' => $result['merchant_request_id'] ?? null,
        ]);

        if (($result['simulated'] ?? false)) {
            return $this->completePaidPayment($payment, $request);
        }

        return [
            'success' => true,
            'payment_id' => $payment->id,
            'status' => 'pending',
            'message' => 'Check your phone and enter your M-Pesa PIN.',
        ];
    }

    public function completePaidPayment(Payment $payment, Request $request): array
    {
        if ($payment->status === 'completed') {
            return ['success' => true, 'payment' => $payment];
        }

        $payment->update([
            'status' => 'completed',
            'mpesa_receipt_number' => 'SIM-'.strtoupper(Str::random(8)),
            'transacted_at' => now(),
        ]);

        $this->logEvent('payment.successful', null, null, $request, [
            'payment_id' => $payment->id,
            'amount' => (float) $payment->amount,
            'receipt' => $payment->mpesa_receipt_number,
        ]);

        $session = $this->createSessionFromPayment($payment, $request);

        $payment->update(['wifi_session_id' => $session->id]);

        return ['success' => true, 'payment' => $payment, 'session' => $session];
    }

    public function redeemVoucher(string $code, Hotspot $hotspot, Request $request): array
    {
        $result = app(VoucherService::class)->redeem($code, $request->input('phone'), $hotspot->id);

        if (! ($result['success'] ?? false)) {
            return ['success' => false, 'message' => $result['message'] ?? 'Invalid voucher code.'];
        }

        $voucher = $result['voucher'] ?? Voucher::query()->where('code', $code)->first();

        $package = $voucher?->package_id ? $voucher->package : null;

        $minutes = $package?->duration_minutes ?? match ($voucher?->type) {
            'hours' => (int) $voucher->value * 60,
            'days' => (int) $voucher->value * 1440,
            default => Setting::getValue('portal.default_session_minutes', 120, $hotspot->organization_id),
        };

        $session = WifiSession::create([
            'organization_id' => $hotspot->organization_id,
            'hotspot_id' => $hotspot->id,
            'package_id' => $package?->id,
            'session_id' => (string) Str::uuid(),
            'mac_address' => $this->macAddress($request),
            'device_type' => $this->deviceType($request),
            'browser' => substr((string) $request->userAgent(), 0, 255),
            'ip_address' => $request->ip(),
            'phone' => $request->input('phone'),
            'auth_method' => 'voucher',
            'session_started_at' => now(),
            'expires_at' => now()->addMinutes($minutes),
            'status' => 'active',
        ]);

        $voucher?->update(['session_id' => $session->id]);

        $this->logEvent('voucher.redeemed', $session, null, $request, [
            'voucher_code' => Str::mask($code, '*', 4, -4),
            'minutes' => $minutes,
        ]);
        $this->grantSession($session, null, $request);

        return ['success' => true, 'session' => $session->fresh()];
    }

    public function verifyPhone(string $phone): bool
    {
        return preg_match('/^(\+?254|0)[17]\d{8}$/', $phone) === 1;
    }

    private function createSessionFromPayment(Payment $payment, Request $request): WifiSession
    {
        $package = $payment->package;

        $session = WifiSession::create([
            'organization_id' => $payment->organization_id,
            'hotspot_id' => $payment->hotspot_id,
            'package_id' => $payment->package_id,
            'session_id' => (string) Str::uuid(),
            'phone' => $payment->phone,
            'mac_address' => $this->macAddress($request),
            'device_type' => $this->deviceType($request),
            'browser' => substr((string) $request->userAgent(), 0, 255),
            'ip_address' => $request->ip(),
            'auth_method' => 'm-pesa',
            'session_started_at' => now(),
            'expires_at' => now()->addMinutes($package?->duration_minutes ?? 120),
            'status' => 'active',
        ]);

        $this->logEvent('session.started', $session, null, $request, [
            'payment_id' => $payment->id,
            'package_id' => $payment->package_id,
            'package' => $package?->name,
            'expires_at' => $session->expires_at?->toIso8601String(),
        ]);
        $this->grantSession($session, null, $request);

        return $session;
    }

    private function grantSession(WifiSession $session, ?Campaign $campaign, Request $request): void
    {
        $package = $session->package;
        $durationMinutes = $session->expires_at
            ? max(1, (int) now()->diffInMinutes($session->expires_at))
            : Setting::getValue('portal.default_session_minutes', 120, $session->organization_id);

        $bandwidthMbps = Setting::getValue('portal.default_bandwidth_mbps', 10, $session->organization_id);

        $this->logEvent('session.started', $session, $campaign, $request, [
            'expires_at' => $session->expires_at?->toIso8601String(),
            'duration_minutes' => $durationMinutes,
        ]);
        $this->logEvent('internet.granted', $session, $campaign, $request, [
            'duration_minutes' => $durationMinutes,
            'bandwidth_mbps' => $bandwidthMbps,
            'package_id' => $package?->id,
        ]);

        if (! $session->mac_address || ! config('services.tolclin.username')) {
            return;
        }

        try {
            app(TolclinApiService::class)->grantAccess($session->mac_address, $durationMinutes, (int) $bandwidthMbps);
        } catch (\Throwable $e) {
            Log::warning('Portal router access grant failed', ['session' => $session->id, 'error' => $e->getMessage()]);
        }
    }

    public function logEvent(string $type, ?WifiSession $session, ?Campaign $campaign, Request $request, array $payload = []): void
    {
        try {
            Event::create([
                'organization_id' => $session?->organization_id ?? $campaign?->organization_id ?? $request->input('organization_id'),
                'session_id' => $session?->id,
                'hotspot_id' => $session?->hotspot_id ?? $request->input('hotspot_id'),
                'campaign_id' => $session?->campaign_id ?? $campaign?->id,
                'event_type' => $type,
                'payload' => $payload,
                'ip_address' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 500),
                'occurred_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Portal event logging failed', ['type' => $type, 'error' => $e->getMessage()]);
        }
    }

    private function locationLabel(Hotspot $hotspot): string
    {
        $parts = array_filter([$hotspot->ward, $hotspot->sub_county, $hotspot->name]);

        return implode(', ', $parts) ?: $hotspot->name;
    }

    private function macAddress(Request $request): ?string
    {
        $mac = $request->input('mac') ?: $request->header('X-Mac-Address');

        return $mac ? strtolower(trim((string) $mac)) : null;
    }

    private function deviceType(Request $request): ?string
    {
        $agent = strtolower((string) $request->userAgent());

        return match (true) {
            str_contains($agent, 'android') => 'android',
            str_contains($agent, 'iphone') => 'iphone',
            str_contains($agent, 'ipad') => 'ipad',
            str_contains($agent, 'windows') => 'windows',
            str_contains($agent, 'macintosh') => 'mac',
            str_contains($agent, 'linux') => 'linux',
            default => 'unknown',
        };
    }
}
