<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\Event;
use App\Models\Hotspot;
use App\Models\Organization;
use App\Models\WifiSession;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
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
    /**
     * Comprehensive KPI summary for the analytics module.
     *
     * @return array<string, mixed>
     */
    public function summaryStats(?Organization $organization, $from = null, $to = null): array
    {
        return Cache::remember(
            $this->cacheKey('summary', $organization, "{$from}.{$to}"),
            self::CACHE_TTL,
            fn () => $this->computeSummaryStats($organization, $from, $to)
        );
    }

    private function computeSummaryStats(?Organization $organization, $from = null, $to = null): array
    {
        $sessions = WifiSession::query();

        if ($organization) {
            $sessions->where('organization_id', $organization->id);
        }

        if ($from && $to) {
            $sessions->whereBetween('session_started_at', [$from, $to]);
        }

        $totalSessions = (clone $sessions)->count();
        $activeSessions = (clone $sessions)->where('status', 'active')->count();
        $sponsoredSessions = (clone $sessions)->whereNotNull('campaign_id')->count();
        $paidSessions = (clone $sessions)->where('auth_method', 'paid')->count();

        $uniqueUsers = (clone $sessions)->whereNotNull('phone')->distinct()->count('phone');
        $repeatUsers = (clone $sessions)
            ->whereNotNull('phone')
            ->selectRaw('phone')
            ->groupBy('phone')
            ->havingRaw('COUNT(*) > 1')
            ->get()
            ->count();

        $totalSeconds = (clone $sessions)->sum('total_duration');
        $totalHours = round($totalSeconds / 3600, 1);
        $avgMinutes = $totalSessions > 0 ? round($totalSeconds / 60 / $totalSessions, 1) : 0.0;

        $bandwidthMb = round((clone $sessions)->sum('bandwidth_used') / (1024 * 1024), 1);
        $bandwidthUpMb = round((clone $sessions)->sum('bandwidth_up') / (1024 * 1024), 1);
        $bandwidthDownMb = round((clone $sessions)->sum('bandwidth_down') / (1024 * 1024), 1);

        $events = Event::query();

        if ($organization) {
            $events->where('organization_id', $organization->id);
        }

        if ($from && $to) {
            $events->whereBetween('occurred_at', [$from, $to]);
        }

        $videoPlays = (clone $events)->where('event_type', 'video.started')->count();
        $videoCompletions = (clone $events)->where('event_type', 'video.completed')->count();
        $completionRate = $this->completionRate($videoPlays, $videoCompletions);

        return [
            'total_sessions' => $totalSessions,
            'active_sessions' => $activeSessions,
            'sponsored_sessions' => $sponsoredSessions,
            'paid_sessions' => $paidSessions,
            'unique_users' => $uniqueUsers,
            'repeat_users' => $repeatUsers,
            'total_hours' => $totalHours,
            'avg_session_minutes' => $avgMinutes,
            'bandwidth_mb' => $bandwidthMb,
            'bandwidth_up_mb' => $bandwidthUpMb,
            'bandwidth_down_mb' => $bandwidthDownMb,
            'video_plays' => $videoPlays,
            'video_completions' => $videoCompletions,
            'completion_rate' => $completionRate,
        ];
    }

    /**
     * Daily sessions, video plays and bandwidth over a date range (inclusive).
     *
     * @return array{labels: array<int, string>, sessions: array<int, int>, plays: array<int, int>, bandwidth_mb: array<int, float>}
     */
    public function usageTrend(?Organization $organization, $from, $to): array
    {
        return Cache::remember(
            $this->cacheKey('usage-trend', $organization, "{$from}.{$to}"),
            self::CACHE_TTL,
            fn () => $this->computeUsageTrend($organization, $from, $to)
        );
    }

    private function computeUsageTrend(?Organization $organization, $from, $to): array
    {
        $fromDate = Carbon::parse($from)->toDateString();
        $toDate = Carbon::parse($to)->toDateString();

        $sessions = WifiSession::query()
            ->selectRaw('DATE(session_started_at) as day, COUNT(*) as total, SUM(bandwidth_used) as bandwidth')
            ->where('session_started_at', '>=', $fromDate.' 00:00:00')
            ->where('session_started_at', '<=', $toDate.' 23:59:59')
            ->groupBy('day')
            ->orderBy('day');

        $events = Event::query()
            ->selectRaw('DATE(occurred_at) as day, COUNT(*) as total')
            ->where('event_type', 'video.started')
            ->where('occurred_at', '>=', $fromDate.' 00:00:00')
            ->where('occurred_at', '<=', $toDate.' 23:59:59')
            ->groupBy('day')
            ->orderBy('day');

        if ($organization) {
            $sessions->where('organization_id', $organization->id);
            $events->where('organization_id', $organization->id);
        }

        $sessionByDay = $sessions->pluck('total', 'day')->all();
        $bandwidthByDay = $sessions->get()->keyBy('day')->map(fn ($row) => round($row->bandwidth / (1024 * 1024), 1))->all();
        $playsByDay = $events->pluck('total', 'day')->all();

        $labels = [];
        $sessionValues = [];
        $playValues = [];
        $bandwidthValues = [];

        $cursor = Carbon::parse($fromDate);
        $end = Carbon::parse($toDate);

        while ($cursor->lte($end)) {
            $day = $cursor->toDateString();
            $labels[] = $cursor->format('M d');
            $sessionValues[] = (int) ($sessionByDay[$day] ?? 0);
            $playValues[] = (int) ($playsByDay[$day] ?? 0);
            $bandwidthValues[] = (float) ($bandwidthByDay[$day] ?? 0);
            $cursor->addDay();
        }

        return [
            'labels' => $labels,
            'sessions' => $sessionValues,
            'plays' => $playValues,
            'bandwidth_mb' => $bandwidthValues,
        ];
    }

    /**
     * Per-location session, user, usage and bandwidth stats for a date range.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function geoBreakdown(?Organization $organization, $from, $to, int $limit = 10)
    {
        return Cache::remember(
            $this->cacheKey('geo', $organization, "{$from}.{$to}.{$limit}"),
            self::CACHE_TTL,
            fn () => $this->computeGeoBreakdown($organization, $from, $to, $limit)
        );
    }

    private function computeGeoBreakdown(?Organization $organization, $from, $to, int $limit)
    {
        $sessions = WifiSession::query()
            ->where('session_started_at', '>=', Carbon::parse($from)->toDateString().' 00:00:00')
            ->where('session_started_at', '<=', Carbon::parse($to)->toDateString().' 23:59:59')
            ->selectRaw('hotspot_id, COUNT(*) as total, COUNT(DISTINCT phone) as unique_users, SUM(total_duration) as total_seconds, SUM(bandwidth_used) as total_bandwidth')
            ->whereNotNull('hotspot_id')
            ->groupBy('hotspot_id');

        if ($organization) {
            $sessions->where('organization_id', $organization->id);
        }

        $rows = $sessions->get()->keyBy('hotspot_id');

        return Hotspot::query()
            ->when($organization, fn ($q) => $q->where('organization_id', $organization->id))
            ->whereIn('id', $rows->keys())
            ->get(['id', 'name', 'sub_county', 'status'])
            ->map(function (Hotspot $hotspot) use ($rows) {
                $row = $rows->get($hotspot->id);

                return [
                    'hotspot' => $hotspot,
                    'sessions' => (int) $row->total,
                    'unique_users' => (int) $row->unique_users,
                    'internet_hours' => round($row->total_seconds / 3600, 1),
                    'bandwidth_mb' => round($row->total_bandwidth / (1024 * 1024), 1),
                ];
            })
            ->sortByDesc('sessions')
            ->values();
    }

    /**
     * Sessions grouped by device type for a date range.
     *
     * @return array<int, array{device: string, sessions: int}>
     */
    public function deviceBreakdown(?Organization $organization, $from, $to): array
    {
        return Cache::remember(
            $this->cacheKey('devices', $organization, "{$from}.{$to}"),
            self::CACHE_TTL,
            fn () => $this->computeDeviceBreakdown($organization, $from, $to)
        );
    }

    private function computeDeviceBreakdown(?Organization $organization, $from, $to): array
    {
        $sessions = WifiSession::query()
            ->where('session_started_at', '>=', Carbon::parse($from)->toDateString().' 00:00:00')
            ->where('session_started_at', '<=', Carbon::parse($to)->toDateString().' 23:59:59')
            ->selectRaw('COALESCE(device_type, "unknown") as device, COUNT(*) as total')
            ->groupBy('device')
            ->orderByDesc('total');

        if ($organization) {
            $sessions->where('organization_id', $organization->id);
        }

        return $sessions
            ->get()
            ->map(fn ($row) => ['device' => $row->device, 'sessions' => (int) $row->total])
            ->all();
    }

    /**
     * Per-campaign plays, completions and completion rate for a date range.
     *
     * @return array<string, mixed>
     */
    public function campaignAnalytics(?Organization $organization, $from, $to, int $limit = 10): array
    {
        return Cache::remember(
            $this->cacheKey('campaign-analytics', $organization, "{$from}.{$to}.{$limit}"),
            self::CACHE_TTL,
            fn () => $this->computeCampaignAnalytics($organization, $from, $to, $limit)
        );
    }

    private function computeCampaignAnalytics(?Organization $organization, $from, $to, int $limit): array
    {
        $campaigns = Campaign::query()
            ->with('sponsor:id,name')
            ->orderByDesc('current_plays');

        if ($organization) {
            $campaigns->where('organization_id', $organization->id);
        }

        $campaigns = $campaigns->limit($limit)->get();

        $campaignIds = $campaigns->pluck('id')->all();
        $playsByCampaign = [];
        $completionsByCampaign = [];

        if ($campaignIds) {
            $events = Event::query()->whereIn('campaign_id', $campaignIds);

            if ($organization) {
                $events->where('organization_id', $organization->id);
            }

            if ($from && $to) {
                $events->whereBetween('occurred_at', [$from, $to]);
            }

            $playsByCampaign = (clone $events)
                ->where('event_type', 'video.started')
                ->selectRaw('campaign_id, COUNT(*) as total')
                ->groupBy('campaign_id')
                ->pluck('total', 'campaign_id')
                ->all();

            $completionsByCampaign = (clone $events)
                ->where('event_type', 'video.completed')
                ->selectRaw('campaign_id, COUNT(*) as total')
                ->groupBy('campaign_id')
                ->pluck('total', 'campaign_id')
                ->all();
        }

        $rows = $campaigns->map(function (Campaign $campaign) use ($playsByCampaign, $completionsByCampaign) {
            $plays = (int) ($playsByCampaign[$campaign->id] ?? 0);
            $completions = (int) ($completionsByCampaign[$campaign->id] ?? 0);

            return [
                'campaign' => $campaign,
                'sponsor' => $campaign->sponsor?->name,
                'plays' => $plays,
                'completions' => $completions,
                'completion_rate' => $this->completionRate($plays, $completions),
                'status' => $campaign->status,
            ];
        })->sortByDesc('plays')->values();

        $totalPlays = array_sum($playsByCampaign);
        $totalCompletions = array_sum($completionsByCampaign);

        return [
            'rows' => $rows,
            'total_plays' => $totalPlays,
            'total_completions' => $totalCompletions,
            'completion_rate' => $this->completionRate($totalPlays, $totalCompletions),
        ];
    }

    private function completionRate(int $plays, int $completions): float
    {
        if ($plays <= 0) {
            return 0.0;
        }

        return round(min(($completions / $plays) * 100, 100.0), 1);
    }

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
