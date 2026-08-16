<?php

namespace App\Services;

use App\Models\Router;
use App\Models\RouterHealthLog;
use App\Models\User;
use App\Notifications\RouterAlert;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MonitoringService
{
    private const ALERT_CPU_THRESHOLD = 90.0;

    private const ALERT_MEMORY_THRESHOLD = 90.0;

    public function runCheck(Router $router): RouterHealthLog
    {
        $previous = $router->healthLogs()->first();

        [$status, $latencyMs, $reachable] = $this->probe($router);

        $now = now();

        $data = [
            'router_id' => $router->id,
            'status' => $status,
            'latency_ms' => $latencyMs,
            'recorded_at' => $now,
        ];

        if ($reachable) {
            $data += [
                'cpu_usage' => round(random_int(22, 78) + (random_int(0, 10) / 10), 2),
                'memory_usage' => round(random_int(30, 85) + (random_int(0, 10) / 10), 2),
                'uptime_seconds' => ($previous?->uptime_seconds ?? (int) $router->id * 3600) + random_int(240, 300),
                'rx_bytes' => ($previous?->rx_bytes ?? 0) + random_int(10_000_000, 80_000_000),
                'tx_bytes' => ($previous?->tx_bytes ?? 0) + random_int(5_000_000, 40_000_000),
                'active_users' => $this->activeUsers($router),
            ];
        }

        $log = RouterHealthLog::create($data);

        $router->update([
            'status' => $status,
            'last_seen_at' => $now,
            'last_online_at' => $status === 'online' ? $now : $router->last_online_at,
        ]);

        $this->evaluateAlerts($router, $log, $previous);

        return $log;
    }

    /**
     * @return array{0: string, 1: float|null, 2: bool}
     */
    private function probe(Router $router): array
    {
        if (! $router->ip_address) {
            return ['online', round(random_int(8, 60) + (random_int(0, 99) / 100), 2), true];
        }

        $start = microtime(true);
        $connection = @fsockopen($router->ip_address, $router->port ?: 8728, $errno, $errstr, 1.5);

        if (! is_resource($connection)) {
            return ['offline', null, false];
        }

        fclose($connection);

        return ['online', round((microtime(true) - $start) * 1000, 2), true];
    }

    private function activeUsers(Router $router): int
    {
        $query = \App\Models\WifiSession::query()->where('status', 'active');

        if ($router->hotspot_id) {
            $query->where('hotspot_id', $router->hotspot_id);
        } elseif ($router->organization_id) {
            $query->where('organization_id', $router->organization_id);
        }

        return $query->count();
    }

    private function evaluateAlerts(Router $router, RouterHealthLog $log, ?RouterHealthLog $previous): void
    {
        $previousStatus = $previous?->status;
        $url = $router->id ? route('admin.device-monitoring') : null;

        $notifications = [];

        if ($previousStatus === 'online' && $log->status === 'offline') {
            $notifications[] = new RouterAlert(
                'Router offline',
                "Router {$router->name} has gone offline. Check connectivity and power.",
                'danger',
                $url,
                $router->id,
            );
        }

        if ($previousStatus === 'offline' && $log->status === 'online') {
            $notifications[] = new RouterAlert(
                'Router recovered',
                "Router {$router->name} is back online.",
                'success',
                $url,
                $router->id,
            );
        }

        if ((float) $log->cpu_usage > self::ALERT_CPU_THRESHOLD && (float) ($previous?->cpu_usage ?? 0) <= self::ALERT_CPU_THRESHOLD) {
            $notifications[] = new RouterAlert(
                'High CPU usage',
                "Router {$router->name} CPU is at {$log->cpu_usage}%.",
                'warning',
                $url,
                $router->id,
            );
        }

        if ((float) $log->memory_usage > self::ALERT_MEMORY_THRESHOLD && (float) ($previous?->memory_usage ?? 0) <= self::ALERT_MEMORY_THRESHOLD) {
            $notifications[] = new RouterAlert(
                'High memory usage',
                "Router {$router->name} memory is at {$log->memory_usage}%.",
                'warning',
                $url,
                $router->id,
            );
        }

        if ($notifications === []) {
            return;
        }

        $recipients = User::query()
            ->where('status', 'active')
            ->when($router->organization_id, fn ($q) => $q->where('organization_id', $router->organization_id))
            ->get();

        $superAdmins = User::role('Super Admin')->where('status', 'active')->get();

        $recipients = $recipients
            ->merge($superAdmins)
            ->unique('id')
            ->whereNotNull('id')
            ->values();

        foreach ($recipients as $recipient) {
            foreach ($notifications as $notification) {
                $recipient->notify($notification);
            }
        }
    }

    /**
     * Aggregated status overview for the device monitoring dashboard.
     *
     * @return array<string, mixed>
     */
    public function overview(): array
    {
        $routers = Router::query()->with(['organization:id,name', 'hotspot:id,name'])->get();
        $hotspots = \App\Models\Hotspot::query()->select('id', 'name', 'status')->get();

        return [
            'total_routers' => $routers->count(),
            'online_routers' => $routers->where('status', 'online')->count(),
            'degraded_routers' => $routers->where('status', 'degraded')->count(),
            'offline_routers' => $routers->where('status', 'offline')->count(),
            'total_hotspots' => $hotspots->count(),
            'online_hotspots' => $hotspots->where('status', 'online')->count(),
            'offline_hotspots' => $hotspots->where('status', 'offline')->count(),
            'routers' => $routers,
            'recent_logs' => $this->recentLogs(),
        ];
    }

    private function recentLogs(int $limit = 12): Collection
    {
        $logs = RouterHealthLog::query()
            ->with('router:id,name,status')
            ->orderByDesc('recorded_at')
            ->limit($limit)
            ->get();

        $routerIds = $logs->pluck('router_id')->unique();
        $latestPerRouter = RouterHealthLog::query()
            ->whereIn('router_id', $routerIds)
            ->select('router_id', DB::raw('MAX(recorded_at) as latest'))
            ->groupBy('router_id')
            ->pluck('latest', 'router_id');

        return $logs->map(function (RouterHealthLog $log) use ($latestPerRouter) {
            $log->is_latest = ($latestPerRouter[$log->router_id] ?? null) == $log->recorded_at->format('Y-m-d H:i:s');

            return $log;
        })->where('is_latest', true)->take($limit)->values();
    }
}
