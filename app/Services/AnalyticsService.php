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
}
