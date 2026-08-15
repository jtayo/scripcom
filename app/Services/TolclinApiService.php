<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TolclinApiService
{
    private string $baseUrl;
    private ?string $username;
    private ?string $password;
    private bool $grantAccess;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.tolclin.base_url', 'https://api.tolclin.com'), '/');
        $this->username = config('services.tolclin.username');
        $this->password = config('services.tolclin.password');
        $this->grantAccess = (bool) config('services.tolclin.grant_access', true);
    }

    private function headers(): array
    {
        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        if ($this->username && $this->password) {
            $headers['Authorization'] = 'Basic ' . base64_encode($this->username . ':' . $this->password);
        }

        return $headers;
    }

    public function routers()
    {
        return $this->get('/routers');
    }

    /**
     * Fetch router details (id, name, latitude, longitude) for the given IDs.
     * Note: the API rejects comma-separated query params passed via the query
     * array (they get URL-encoded), so the ids are appended raw to the path.
     *
     * @return array<int, array<string, mixed>>
     */
    public function routersByIds(array $ids): array
    {
        $ids = array_values(array_filter(array_map('intval', $ids)));
        if (empty($ids)) {
            return [];
        }

        try {
            $response = Http::withHeaders($this->headers())
                ->timeout(20)
                ->get($this->baseUrl . '/tolclin/captivity/admin/routers/by-ids?ids=' . implode(',', $ids));

            return $response->successful() ? $response->json() : [];
        } catch (\Throwable $e) {
            Log::warning('Tolclin API request failed', [
                'path' => '/tolclin/captivity/admin/routers/by-ids',
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Router IDs configured for this deployment (TOLCLIN_ROUTER_IDS).
     *
     * @return array<int, int>
     */
    public function configuredRouterIds(): array
    {
        $raw = config('services.tolclin.router_ids', '');

        return array_values(array_filter(array_map('intval', explode(',', (string) $raw))));
    }

    /**
     * Fetch routers from the Tolclin API and normalize them into a consistent
     * structure tolerant of unknown response shapes.
     *
     * @return array<int, array<string, mixed>>
     */
    public function normalizedRouters(): array
    {
        $items = $this->routersByIds($this->configuredRouterIds());

        $normalized = [];
        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }

            $latitude = $this->firstValue($item, ['latitude', 'lat', 'lat_y', 'geo.latitude', 'location.latitude']);
            $longitude = $this->firstValue($item, ['longitude', 'lng', 'lon', 'lat_x', 'geo.longitude', 'location.longitude']);

            if ($latitude === null || $longitude === null) {
                continue;
            }

            $normalized[] = [
                'router_id' => (int) ($this->firstValue($item, ['router_id', 'id', 'routerId', 'device_id']) ?? 0),
                'name' => (string) ($this->firstValue($item, ['name', 'router_name', 'device_name']) ?? ''),
                'latitude' => (float) $latitude,
                'longitude' => (float) $longitude,
                'status' => ($status = $this->firstValue($item, ['status', 'state'])) ? (string) $status : null,
                'last_seen_at' => $this->firstValue($item, ['last_seen_at', 'last_seen', 'last_online_at', 'updated_at']),
                'raw' => $item,
            ];
        }

        return $normalized;
    }

    /**
     * Pull the first non-null value from $item for any of the given keys,
     * supporting dot-notation for nested arrays.
     */
    private function firstValue(array $item, array $keys): mixed
    {
        foreach ($keys as $key) {
            $value = data_get($item, $key);
            if ($value !== null && $value !== '') {
                return $value;
            }
        }

        return null;
    }

    public function sessionsByRouter(int $routerId, $from = null, $to = null)
    {
        return $this->get("/routers/{$routerId}/sessions", array_filter([
            'from' => $from,
            'to' => $to,
        ]));
    }

    /**
     * Fetch a session summary (by status) for all configured routers from the
     * Tolclin sessions endpoint, for the given date range.
     *
     * @return array{total: int, active: int, expired: int, failed: int, routers: array<int, array<string, mixed>>}
     */
    public function sessionsSummary(?string $from = null, ?string $to = null): array
    {
        try {
            $response = Http::withHeaders($this->headers())
                ->timeout(60)
                ->post($this->baseUrl . '/tolclin/captivity/hotspot/sessions/by-router', [
                    'from' => $from ?? now()->subDays(7)->toDateString(),
                    'to' => $to ?? now()->toDateString(),
                ]);
        } catch (\Throwable $e) {
            Log::warning('Tolclin sessions summary failed', ['error' => $e->getMessage()]);

            return $this->emptySummary();
        }

        $json = $response->successful() ? $response->json() : null;
        if (! is_array($json) || ! isset($json['routers'])) {
            return $this->emptySummary();
        }

        $summary = $this->emptySummary();

        foreach ($json['routers'] as $router) {
            $counts = ['ACTIVE' => 0, 'EXPIRED' => 0, 'FAILED' => 0];

            foreach (($router['sessions'] ?? []) as $session) {
                $status = strtoupper((string) ($session['status'] ?? ''));
                if (isset($counts[$status])) {
                    $counts[$status]++;
                }
            }

            $entry = [
                'router_id' => (int) ($router['routerId'] ?? 0),
                'name' => (string) ($router['routerName'] ?? ''),
                'total' => array_sum($counts),
                'active' => $counts['ACTIVE'],
                'expired' => $counts['EXPIRED'],
                'failed' => $counts['FAILED'],
            ];

            $summary['routers'][] = $entry;
            $summary['total'] += $entry['total'];
            $summary['active'] += $entry['active'];
            $summary['expired'] += $entry['expired'];
            $summary['failed'] += $entry['failed'];
        }

        return $summary;
    }

    /**
     * @return array{total: int, active: int, expired: int, failed: int, routers: array<int, array<string, mixed>>}
     */
    private function emptySummary(): array
    {
        return ['total' => 0, 'active' => 0, 'expired' => 0, 'failed' => 0, 'routers' => []];
    }

    /**
     * Fetch the most recent sessions from the Tolclin API, flattened across all
     * routers with their MAC address, status, and router name. Active sessions
     * are sorted first so they appear at the top of a "recent" list.
     *
     * @return array<int, array{mac_address: string, status: string, router_id: int, router_name: string}>
     */
    public function recentSessions(?string $from = null, ?string $to = null, int $limit = 10): array
    {
        try {
            $response = Http::withHeaders($this->headers())
                ->timeout(60)
                ->post($this->baseUrl . '/tolclin/captivity/hotspot/sessions/by-router', [
                    'from' => $from ?? now()->subDays(1)->toDateString(),
                    'to' => $to ?? now()->toDateString(),
                ]);
        } catch (\Throwable $e) {
            Log::warning('Tolclin recent sessions failed', ['error' => $e->getMessage()]);

            return [];
        }

        $json = $response->successful() ? $response->json() : null;
        if (! is_array($json) || ! isset($json['routers'])) {
            return [];
        }

        $sessions = [];
        foreach ($json['routers'] as $router) {
            $routerId = (int) ($router['routerId'] ?? 0);
            $routerName = (string) ($router['routerName'] ?? '');

            foreach (($router['sessions'] ?? []) as $session) {
                $status = strtoupper((string) ($session['status'] ?? ''));

                $sessions[] = [
                    'mac_address' => (string) ($session['macAddress'] ?? ''),
                    'status' => $status,
                    'router_id' => $routerId,
                    'router_name' => $routerName,
                ];
            }
        }

        usort($sessions, fn ($a, $b) => ($a['status'] === 'ACTIVE' ? 0 : 1) <=> ($b['status'] === 'ACTIVE' ? 0 : 1));

        return array_slice($sessions, 0, $limit);
    }

    public function exportSessions($from = null, $to = null)
    {
        return $this->get('/sessions/export', array_filter([
            'from' => $from,
            'to' => $to,
        ]));
    }

    public function grantAccess(string $macAddress, int $durationMinutes = 120, int $bandwidthMbps = 10): array
    {
        if (! $this->grantAccess) {
            return ['success' => true, 'simulated' => true, 'mac' => $macAddress];
        }

        $response = Http::withHeaders($this->headers())
            ->timeout(15)
            ->post($this->baseUrl . '/access/grant', [
                'mac_address' => $macAddress,
                'duration_minutes' => $durationMinutes,
                'bandwidth_mbps' => $bandwidthMbps,
            ]);

        return $response->successful() ? $response->json() : $response->json();
    }

    public function revokeAccess(string $macAddress): array
    {
        if (! $this->grantAccess) {
            return ['success' => true, 'simulated' => true, 'mac' => $macAddress];
        }

        $response = Http::withHeaders($this->headers())
            ->timeout(15)
            ->post($this->baseUrl . '/access/revoke', [
                'mac_address' => $macAddress,
            ]);

        return $response->successful() ? $response->json() : $response->json();
    }

    private function get(string $path, array $query = [])
    {
        try {
            $response = Http::withHeaders($this->headers())
                ->timeout(20)
                ->get($this->baseUrl . $path, $query);

            return $response->successful() ? $response->json() : $response->json();
        } catch (\Throwable $e) {
            Log::warning('Tolclin API request failed', [
                'path' => $path,
                'error' => $e->getMessage(),
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
