<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\Event;
use App\Models\Hotspot;
use App\Models\Organization;
use App\Models\WifiSession;
use Illuminate\Support\Facades\Cache;

class AnalyticsService
{
    private const CACHE_TTL = 300;

    /**
     * Invalidate all cached analytics for an organization (all scopes when null).
     */
    public function forget(?Organization $organization): void
    {
        Cache::increment("analytics.version.{$this->scopeKey($organization)}");
    }

    private function scopeKey(?Organization $organization): string
    {
        return $organization ? "org.{$organization->id}" : 'global';
    }

    private function cacheKey(string $bucket, ?Organization $organization, string $suffix = ''): string
    {
        $version = (int) Cache::get("analytics.version.{$this->scopeKey($organization)}", 0);
        $key = "analytics.{$bucket}.v{$version}.{$this->scopeKey($organization)}";

        return $suffix !== '' ? "{$key}.{$suffix}" : $key;
    }

    public function dashboardStats(?Organization $organization, $from = null, $to = null): array
    {
        return Cache::remember(
            $this->cacheKey('stats', $organization, "{$from}.{$to}"),
            self::CACHE_TTL,
            fn () => $this->computeDashboardStats($organization, $from, $to)
        );
    }

    private function computeDashboardStats(?Organization $organization, $from = null, $to = null): array
    {
        $sessions = WifiSession::query();
        $events = Event::query();
        $hotspots = Hotspot::query();

        if ($organization) {
            $sessions->where('organization_id', $organization->id);
            $events->where('organization_id', $organization->id);
            $hotspots->where('organization_id', $organization->id);
        }

        if ($from && $to) {
            $sessions->whereBetween('session_started_at', [$from, $to]);
        }

        $totalSessions = (clone $sessions)->count();
        $activeSessions = (clone $sessions)->where('status', 'active')->count();
        $onlineHotspots = (clone $hotspots)->where('status', 'online')->count();
        $totalHotspots = (clone $hotspots)->count();

        $seconds = (clone $sessions)->sum('total_duration');
        $hours = round($seconds / 3600, 1);

        $bandwidthMb = round((clone $sessions)->sum('bandwidth_used') / (1024 * 1024), 1);

        $completedEvents = (clone $events)->where('event_type', 'video.completed')->count();

        return [
            'total_sessions' => $totalSessions,
            'active_sessions' => $activeSessions,
            'online_hotspots' => $onlineHotspots,
            'total_hotspots' => $totalHotspots,
            'total_hours' => $hours,
            'bandwidth_mb' => $bandwidthMb,
            'video_completions' => $completedEvents,
        ];
    }

    public function sessionsByHour(?Organization $organization, $from = null, $to = null): array
    {
        return Cache::remember(
            $this->cacheKey('hours', $organization, "{$from}.{$to}"),
            self::CACHE_TTL,
            fn () => $this->computeSessionsByHour($organization, $from, $to)
        );
    }

    private function computeSessionsByHour(?Organization $organization, $from = null, $to = null): array
    {
        $sessions = WifiSession::query()
            ->selectRaw('HOUR(session_started_at) as hour, COUNT(*) as total')
            ->groupBy('hour')
            ->orderBy('hour');

        if ($organization) {
            $sessions->where('organization_id', $organization->id);
        }

        if ($from && $to) {
            $sessions->whereBetween('session_started_at', [$from, $to]);
        }

        $result = $sessions->pluck('total', 'hour');

        $labels = [];
        $values = [];

        for ($h = 0; $h < 24; $h++) {
            $labels[] = str_pad((string) $h, 2, '0', STR_PAD_LEFT).':00';
            $values[] = $result[$h] ?? 0;
        }

        return ['labels' => $labels, 'values' => $values];
    }

    public function sessionsPerDay(?Organization $organization, int $days = 14): array
    {
        return Cache::remember(
            $this->cacheKey('days', $organization, (string) $days),
            self::CACHE_TTL,
            fn () => $this->computeSessionsPerDay($organization, $days)
        );
    }

    private function computeSessionsPerDay(?Organization $organization, int $days): array
    {
        $sessions = WifiSession::query()
            ->selectRaw('DATE(session_started_at) as day, COUNT(*) as total')
            ->where('session_started_at', '>=', now()->subDays($days))
            ->groupBy('day')
            ->orderBy('day');

        if ($organization) {
            $sessions->where('organization_id', $organization->id);
        }

        $result = $sessions->pluck('total', 'day');

        $labels = [];
        $values = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $day = now()->subDays($i)->toDateString();
            $labels[] = now()->subDays($i)->format('M d');
            $values[] = $result[$day] ?? 0;
        }

        return ['labels' => $labels, 'values' => $values];
    }

    public function campaignsPerformance(?Organization $organization, int $limit = 5)
    {
        return Cache::remember(
            $this->cacheKey('campaigns', $organization, (string) $limit),
            self::CACHE_TTL,
            fn () => $this->computeCampaignsPerformance($organization, $limit)
        );
    }

    private function computeCampaignsPerformance(?Organization $organization, int $limit)
    {
        $query = Campaign::query()
            ->withCount('sessions')
            ->orderByDesc('current_plays')
            ->limit($limit);

        if ($organization) {
            $query->where('organization_id', $organization->id);
        }

        return $query->get();
    }

    public function topHotspots(?Organization $organization, int $limit = 5)
    {
        return Cache::remember(
            $this->cacheKey('hotspots', $organization, (string) $limit),
            self::CACHE_TTL,
            fn () => $this->computeTopHotspots($organization, $limit)
        );
    }

    private function computeTopHotspots(?Organization $organization, int $limit)
    {
        $query = Hotspot::query()
            ->withCount('sessions')
            ->withSum('sessions as total_bandwidth', 'bandwidth_used')
            ->orderByDesc('sessions_count')
            ->limit($limit);

        if ($organization) {
            $query->where('organization_id', $organization->id);
        }

        return $query->get();
    }

    public function recentEvents(?Organization $organization, int $limit = 8)
    {
        $query = Event::query()
            ->with(['hotspot:id,name', 'campaign:id,title'])
            ->latest('occurred_at')
            ->limit($limit);

        if ($organization) {
            $query->where('organization_id', $organization->id);
        }

        return $query->get();
    }

    public function geographyStats(?Organization $organization)
    {
        $query = Hotspot::query();

        if ($organization) {
            $query->where('organization_id', $organization->id);
        }

        return $query
            ->selectRaw('sub_county, COUNT(*) as total')
            ->whereNotNull('sub_county')
            ->groupBy('sub_county')
            ->get();
    }

    /**
     * Tenant analytics for the county dashboard (spec §54 / §64).
     *
     * @return array{sponsored_sessions: int, unique_users: int, internet_hours: float, video_plays: int, completion_rate: float, repeat_users: int, active_locations: int, total_locations: int}
     */
    public function countyDashboardStats(?Organization $organization, $from = null, $to = null): array
    {
        return Cache::remember(
            $this->cacheKey('county-stats', $organization, "{$from}.{$to}"),
            self::CACHE_TTL,
            fn () => $this->computeCountyDashboardStats($organization, $from, $to)
        );
    }

    private function computeCountyDashboardStats(?Organization $organization, $from = null, $to = null): array
    {
        $sessions = WifiSession::query();
        $events = Event::query();
        $hotspots = Hotspot::query();

        if ($organization) {
            $sessions->where('organization_id', $organization->id);
            $events->where('organization_id', $organization->id);
            $hotspots->where('organization_id', $organization->id);
        }

        if ($from && $to) {
            $sessions->whereBetween('session_started_at', [$from, $to]);
            $events->whereBetween('occurred_at', [$from, $to]);
        }

        $sponsoredSessions = (clone $sessions)->whereNotNull('campaign_id')->count();
        $uniqueUsers = (clone $sessions)->whereNotNull('phone')->distinct()->count('phone');
        $repeatUsers = (clone $sessions)
            ->whereNotNull('phone')
            ->selectRaw('phone')
            ->groupBy('phone')
            ->havingRaw('COUNT(*) > 1')
            ->get()
            ->count();

        $internetHours = round((clone $sessions)->sum('total_duration') / 3600, 1);

        $videoPlays = (clone $events)->where('event_type', 'video.started')->count();
        $videoCompletions = (clone $events)->where('event_type', 'video.completed')->count();
        $completionRate = $videoPlays > 0 ? round(($videoCompletions / $videoPlays) * 100, 1) : 0.0;

        $activeLocations = (clone $hotspots)->where('status', 'online')->count();
        $totalLocations = (clone $hotspots)->count();

        return [
            'sponsored_sessions' => $sponsoredSessions,
            'unique_users' => $uniqueUsers,
            'internet_hours' => $internetHours,
            'video_plays' => $videoPlays,
            'completion_rate' => $completionRate,
            'repeat_users' => $repeatUsers,
            'active_locations' => $activeLocations,
            'total_locations' => $totalLocations,
        ];
    }

    /**
     * Per-location performance for the county dashboard (spec §64).
     */
    public function locationPerformance(?Organization $organization, int $limit = 10)
    {
        $query = Hotspot::query()
            ->withCount('sessions')
            ->withSum('sessions as total_duration', 'total_duration')
            ->orderByDesc('sessions_count')
            ->limit($limit);

        if ($organization) {
            $query->where('organization_id', $organization->id);
        }

        return $query->get();
    }

    /**
     * Tenant analytics for the corporate/advertiser dashboard (spec §65).
     *
     * @return array<string, mixed>
     */
    public function advertiserDashboardStats(?Organization $organization, $from = null, $to = null): array
    {
        return Cache::remember(
            $this->cacheKey('advertiser-stats', $organization, "{$from}.{$to}"),
            self::CACHE_TTL,
            fn () => $this->computeAdvertiserDashboardStats($organization, $from, $to)
        );
    }

    private function computeAdvertiserDashboardStats(?Organization $organization, $from = null, $to = null): array
    {
        $campaigns = Campaign::query()
            ->with('sponsor:id,name')
            ->withCount('sessions')
            ->orderByDesc('current_plays');

        if ($organization) {
            $campaigns->where('organization_id', $organization->id);
        }

        $campaigns = $campaigns->get();

        $campaignIds = $campaigns->pluck('id')->all();

        $playsByCampaign = [];
        $completionsByCampaign = [];

        if ($campaignIds) {
            $playsByCampaign = Event::query()
                ->whereIn('campaign_id', $campaignIds)
                ->where('event_type', 'video.started')
                ->selectRaw('campaign_id, COUNT(*) as total')
                ->groupBy('campaign_id')
                ->pluck('total', 'campaign_id')
                ->all();

            $completionsByCampaign = Event::query()
                ->whereIn('campaign_id', $campaignIds)
                ->where('event_type', 'video.completed')
                ->selectRaw('campaign_id, COUNT(*) as total')
                ->groupBy('campaign_id')
                ->pluck('total', 'campaign_id')
                ->all();
        }

        $perCampaign = $campaigns->map(function (Campaign $campaign) use ($playsByCampaign, $completionsByCampaign) {
            $plays = (int) ($playsByCampaign[$campaign->id] ?? 0);
            $completions = (int) ($completionsByCampaign[$campaign->id] ?? 0);

            return [
                'campaign' => $campaign,
                'plays' => $plays,
                'completions' => $completions,
                'completion_rate' => $plays > 0 ? round(($completions / $plays) * 100, 1) : 0.0,
                'sessions' => (int) $campaign->sessions_count,
                'status' => $campaign->status,
                'is_active' => $campaign->is_active,
                'starts_at' => $campaign->starts_at,
                'ends_at' => $campaign->ends_at,
            ];
        });

        $totalPlays = array_sum($playsByCampaign);
        $totalCompletions = array_sum($completionsByCampaign);

        return [
            'campaigns' => $perCampaign,
            'total_plays' => $totalPlays,
            'total_completions' => $totalCompletions,
            'overall_completion_rate' => $totalPlays > 0 ? round(($totalCompletions / $totalPlays) * 100, 1) : 0.0,
            'total_campaigns' => $campaigns->count(),
            'active_campaigns' => $campaigns->where('is_active', true)->where('status', 'active')->count(),
        ];
    }

    /**
     * Sessions and plays trend for tenant dashboards.
     */
    public function tenantTrends(?Organization $organization, int $days = 14): array
    {
        $sessions = WifiSession::query()
            ->selectRaw('DATE(session_started_at) as day, COUNT(*) as total')
            ->where('session_started_at', '>=', now()->subDays($days))
            ->groupBy('day')
            ->orderBy('day');

        $events = Event::query()
            ->selectRaw('DATE(occurred_at) as day, COUNT(*) as total')
            ->where('event_type', 'video.started')
            ->where('occurred_at', '>=', now()->subDays($days))
            ->groupBy('day')
            ->orderBy('day');

        if ($organization) {
            $sessions->where('organization_id', $organization->id);
            $events->where('organization_id', $organization->id);
        }

        $sessionByDay = $sessions->pluck('total', 'day')->all();
        $playsByDay = $events->pluck('total', 'day')->all();

        $labels = [];
        $sessionValues = [];
        $playValues = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $day = now()->subDays($i)->toDateString();
            $labels[] = now()->subDays($i)->format('M d');
            $sessionValues[] = $sessionByDay[$day] ?? 0;
            $playValues[] = $playsByDay[$day] ?? 0;
        }

        return [
            'labels' => $labels,
            'sessions' => $sessionValues,
            'plays' => $playValues,
        ];
    }
}
