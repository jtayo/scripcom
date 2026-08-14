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

    public function sessionsByRouter(int $routerId, $from = null, $to = null)
    {
        return $this->get("/routers/{$routerId}/sessions", array_filter([
            'from' => $from,
            'to' => $to,
        ]));
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
