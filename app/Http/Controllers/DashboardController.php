<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\HasOrganizationScoping;
use App\Models\Campaign;
use App\Models\Event;
use App\Models\Hotspot;
use App\Models\Organization;
use App\Models\Sponsorship;
use App\Services\AnalyticsService;
use App\Services\EventService;
use App\Services\KenyaWardLookup;
use App\Services\TolclinApiService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    use HasOrganizationScoping;

    public function index(AnalyticsService $analytics, EventService $eventService, TolclinApiService $tolclin): View
    {
        $organization = $this->organization();

        if ($organization?->isCounty()) {
            return $this->countyDashboard($analytics, $organization);
        }

        if ($organization?->isCorporate()) {
            return $this->corporateDashboard($analytics, $organization);
        }

        $stats = $analytics->dashboardStats($organization);
        $sessionsByHour = $analytics->sessionsByHour($organization);
        $sessionsPerDay = $analytics->sessionsPerDay($organization, 14);
        $campaigns = $analytics->campaignsPerformance($organization, 5);
        $topHotspots = $analytics->topHotspots($organization, 5);
        $recentEvents = $eventService->getEvents($organization, 8);
        $recentSessions = $tolclin->recentSessions(null, null, 10);

        $trends = $this->trends($analytics, $organization);

        $campaignCount = Campaign::query()->tap(fn ($q) => $this->scopeOrganization($q))->count();
        $activeSponsorships = Sponsorship::query()->tap(fn ($q) => $this->scopeOrganization($q))->where('status', 'active')->count();
        $eventCount = Event::query()->tap(fn ($q) => $this->scopeOrganization($q))->count();

        $liveSessions = $tolclin->sessionsSummary(
            now()->subDays(7)->toDateString(),
            now()->toDateString()
        );

        $activeSessionsToday = $tolclin->sessionsSummary(
            now()->toDateString(),
            now()->toDateString()
        );

        $hotspotMarkers = $this->hotspotMarkers($tolclin, $liveSessions, $organization);

        return view('dashboard', compact(
            'stats',
            'sessionsByHour',
            'sessionsPerDay',
            'campaigns',
            'topHotspots',
            'recentEvents',
            'recentSessions',
            'trends',
            'campaignCount',
            'activeSponsorships',
            'eventCount',
            'hotspotMarkers',
            'liveSessions',
            'activeSessionsToday',
            'organization'
        ));
    }

    /**
     * Build dashboard map markers from DB hotspots, enriched with live data
     * from the Tolclin API when the router matches and the API is reachable.
     *
     * @param  array{total: int, active: int, expired: int, failed: int, routers: array<int, array<string, mixed>>}  $liveSessions
     * @return array<int, array<string, mixed>>
     */
    protected function hotspotMarkers(TolclinApiService $tolclin, array $liveSessions, $organization, ?KenyaWardLookup $wardLookup = null): array
    {
        $hotspots = Hotspot::query()
            ->when($organization, fn ($q) => $q->where('organization_id', $organization->id))
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get(['id', 'slug', 'name', 'router_id', 'latitude', 'longitude', 'ward', 'sub_county', 'status', 'ssid', 'last_seen_at', 'is_active']);

        $routerStatus = collect($tolclin->normalizedRouters())
            ->keyBy('router_id');

        $activeByRouter = collect($liveSessions['routers'] ?? [])
            ->keyBy('router_id');

        $wardLookup ??= app(KenyaWardLookup::class);

        return $hotspots->map(function (Hotspot $hotspot) use ($routerStatus, $activeByRouter, $wardLookup) {
            $router = $routerStatus->get((int) $hotspot->router_id);
            $status = $router['status'] ?? null;
            $status = is_string($status) && $status !== '' ? $status : $hotspot->status;

            $lat = $router['latitude'] ?? (float) $hotspot->latitude;
            $lng = $router['longitude'] ?? (float) $hotspot->longitude;
            $location = $hotspot->ward ? null : $wardLookup->wardFor($lat, $lng);
            $active = (int) ($activeByRouter->get((int) $hotspot->router_id)['active'] ?? 0);

            return [
                'id' => $hotspot->id,
                'slug' => $hotspot->slug,
                'name' => $router['name'] ?? $hotspot->name,
                'latitude' => $lat,
                'longitude' => $lng,
                'ward' => $location['ward'] ?? $hotspot->ward,
                'sub_county' => $location['sub_county'] ?? $hotspot->sub_county,
                'ssid' => $hotspot->ssid,
                'status' => $status,
                'router_name' => $router['name'] ?? null,
                'last_seen_at' => $router['last_seen_at'] ?? $hotspot->last_seen_at,
                'active_sessions' => $active,
                'online' => in_array(strtolower((string) $status), ['online', 'active', 'up']),
            ];
        })->values()->all();
    }

    protected function trends(AnalyticsService $analytics, $organization): array
    {
        $current = $analytics->dashboardStats($organization, now()->subDays(7)->startOfDay(), now());
        $previous = $analytics->dashboardStats($organization, now()->subDays(14)->startOfDay(), now()->subDays(7)->endOfDay());

        return [
            'total_sessions' => $this->percentChange($current['total_sessions'], $previous['total_sessions']),
            'active_sessions' => $this->percentChange($current['active_sessions'], $previous['active_sessions']),
            'online_hotspots' => $this->percentChange($current['online_hotspots'], $previous['online_hotspots']),
            'total_hours' => $this->percentChange($current['total_hours'], $previous['total_hours']),
            'bandwidth_mb' => $this->percentChange($current['bandwidth_mb'], $previous['bandwidth_mb']),
            'video_completions' => $this->percentChange($current['video_completions'], $previous['video_completions']),
        ];
    }

    protected function percentChange(float|int $current, float|int $previous): ?float
    {
        if ((float) $previous <= 0) {
            return $current > 0 ? null : 0.0;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }

    protected function countyDashboard(AnalyticsService $analytics, Organization $organization): View
    {
        $county = $analytics->countyDashboardStats($organization);
        $trends = $analytics->tenantTrends($organization);
        $peakHours = $analytics->sessionsByHour($organization);
        $locations = $analytics->locationPerformance($organization, 10);

        return view('dashboards.county', compact(
            'organization',
            'county',
            'trends',
            'peakHours',
            'locations'
        ));
    }

    protected function corporateDashboard(AnalyticsService $analytics, Organization $organization): View
    {
        $advertiser = $analytics->advertiserDashboardStats($organization);
        $trends = $analytics->tenantTrends($organization);
        $peakHours = $analytics->sessionsByHour($organization);

        return view('dashboards.corporate', compact(
            'organization',
            'advertiser',
            'trends',
            'peakHours'
        ));
    }
}
