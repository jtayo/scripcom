<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\Hotspot;
use App\Models\Setting;

class CaptivePortalService
{
    public function getPortalData(Hotspot $hotspot): array
    {
        $organization = $hotspot->organization;

        return [
            'hotspot' => $hotspot,
            'organization' => $organization,
            'default_session_minutes' => Setting::getValue('portal.default_session_minutes', 120, $organization?->id),
            'default_bandwidth_mbps' => Setting::getValue('portal.default_bandwidth_mbps', 10, $organization?->id),
            'vouchers_enabled' => Setting::getValue('portal.vouchers_enabled', true, $organization?->id),
            'branding' => [
                'primary_color' => $organization?->primary_color ?? '#262B40',
                'logo' => $organization?->logo,
            ],
        ];
    }

    public function getActiveCampaign(Hotspot $hotspot): ?Campaign
    {
        return Campaign::query()
            ->active()
            ->whereHas('hotspots', fn ($q) => $q->where('hotspots.id', $hotspot->id))
            ->orderByDesc('priority')
            ->orderBy('current_plays')
            ->first();
    }

    public function verifyPhone(string $phone): bool
    {
        return preg_match('/^(\+?254|0)[17]\d{8}$/', $phone) === 1;
    }
}
