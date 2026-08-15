<?php

namespace App\Services;

use App\Enums\EventType;
use App\Models\Hotspot;
use App\Models\Organization;
use App\Models\WifiSession;
use Illuminate\Support\Facades\Log;

/**
 * Processes Tolclin webhook payloads into local session/event records.
 *
 * The processor tolerates a range of payload shapes (single event, event list,
 * or a router-session dump) and maps Tolclin statuses to EventType values.
 */
class TolclinWebhookService
{
    public function handle(string $rawBody): void
    {
        $payload = json_decode($rawBody, true);

        if (! is_array($payload)) {
            Log::warning('Tolclin webhook: non-JSON payload received');

            return;
        }

        foreach ($this->extractEvents($payload) as $item) {
            try {
                $this->processItem($item);
            } catch (\Throwable $e) {
                Log::warning('Tolclin webhook: failed to process item', ['error' => $e->getMessage()]);
            }
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function extractEvents(array $payload): array
    {
        if (isset($payload['routers']) && is_array($payload['routers'])) {
            return $this->flattenRouters($payload['routers']);
        }

        if (isset($payload['sessions']) && is_array($payload['sessions'])) {
            return $this->flattenSessions(
                $payload['sessions'],
                (int) ($payload['routerId'] ?? 0),
                (string) ($payload['routerName'] ?? '')
            );
        }

        if (isset($payload['events']) && is_array($payload['events'])) {
            return $payload['events'];
        }

        if (array_is_list($payload)) {
            return $payload;
        }

        return [$payload];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function flattenRouters(array $routers): array
    {
        $items = [];

        foreach ($routers as $router) {
            if (! is_array($router)) {
                continue;
            }

            $routerId = (int) ($router['routerId'] ?? 0);
            $routerName = (string) ($router['routerName'] ?? '');

            $items = array_merge(
                $items,
                $this->flattenSessions($router['sessions'] ?? [], $routerId, $routerName)
            );
        }

        return $items;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function flattenSessions(array $sessions, int $routerId, string $routerName): array
    {
        $items = [];

        foreach ($sessions as $session) {
            if (! is_array($session)) {
                continue;
            }

            $items[] = array_merge(['router_id' => $routerId, 'router_name' => $routerName], $session);
        }

        return $items;
    }

    private function processItem(array $item): void
    {
        $type = $this->mapEventType($item);
        $data = $this->snakeCase($this->sessionData($item));
        $organization = $this->resolveOrganization($data);
        $session = $this->resolveSession($data);

        if ($type === EventType::HotspotUp->value || $type === EventType::HotspotDown->value) {
            $this->updateHotspot($data, $type);
        }

        if ($session) {
            $this->applyToSession($session, $type, $data);
        } elseif ($type === EventType::SessionStarted->value || ($data['status'] ?? null) === 'active') {
            $session = $this->createSession($data, $organization);
        }

        app(EventService::class)->track(
            $type,
            $organization,
            $session?->id,
            $session?->hotspot_id ?? $data['hotspot_id'] ?? null,
            $data['campaign_id'] ?? null,
            ['webhook' => true, 'payload' => $item]
        );
    }

    private function mapEventType(array $item): string
    {
        $type = strtolower(trim((string) ($item['event_type'] ?? $item['type'] ?? $item['event'] ?? '')));

        if (EventType::tryFrom($type) !== null) {
            return $type;
        }

        if ($type !== '') {
            return $type;
        }

        return match (strtoupper((string) ($item['status'] ?? ''))) {
            'ACTIVE' => EventType::SessionStarted->value,
            'EXPIRED', 'COMPLETED', 'TERMINATED', 'ENDED', 'REVOKED' => EventType::SessionEnded->value,
            'FAILED' => EventType::ErrorOccurred->value,
            default => EventType::ErrorOccurred->value,
        };
    }

    private function sessionData(array $item): array
    {
        if (isset($item['session']) && is_array($item['session'])) {
            return $item['session'];
        }

        if (isset($item['data']) && is_array($item['data'])) {
            return $item['data'];
        }

        return $item;
    }

    private function snakeCase(array $data): array
    {
        $out = [];

        foreach ($data as $key => $value) {
            $out[is_string($key) ? str($key)->snake()->toString() : $key] = $value;
        }

        return $out;
    }

    private function resolveSession(array $data): ?WifiSession
    {
        foreach (['session_id', 'provider_session_id', 'mac_address'] as $field) {
            $value = $data[$field] ?? null;

            if (! is_string($value) || $value === '') {
                continue;
            }

            $session = WifiSession::query()->where($field, $value)->first();

            if ($session) {
                return $session;
            }
        }

        return null;
    }

    private function resolveOrganization(array $data): ?Organization
    {
        if (! empty($data['organization_id'])) {
            return Organization::find($data['organization_id']);
        }

        return Organization::find(config('services.tolclin.organization_id'));
    }

    private function createSession(array $data, ?Organization $organization): ?WifiSession
    {
        $providerSessionId = $data['session_id'] ?? $data['provider_session_id'] ?? null;

        return app(SessionManager::class)->startSession([
            'provider_session_id' => is_string($providerSessionId) ? $providerSessionId : null,
            'organization_id' => $organization?->id,
            'hotspot_id' => $data['hotspot_id'] ?? $this->hotspotIdForRouter($data['router_id'] ?? null),
            'campaign_id' => $data['campaign_id'] ?? null,
            'sponsorship_id' => $data['sponsorship_id'] ?? null,
            'phone' => $data['phone'] ?? null,
            'mac_address' => $data['mac_address'] ?? null,
            'ip_address' => $data['ip_address'] ?? null,
            'session_started_at' => $data['session_started_at'] ?? $data['started_at'] ?? now(),
            'status' => 'active',
        ]);
    }

    private function applyToSession(WifiSession $session, string $type, array $data): void
    {
        $status = strtolower((string) ($data['status'] ?? ''));

        if ($type === EventType::SessionEnded->value || in_array($status, ['expired', 'completed', 'terminated', 'ended', 'revoked'], true)) {
            app(SessionManager::class)->endSession($session, $data['end_reason'] ?? ($status ?: 'ended'));

            return;
        }

        if ($type === EventType::BandwidthUpdated->value) {
            app(SessionManager::class)->updateBandwidth(
                $session,
                (int) ($data['bytes_up'] ?? $data['bandwidth_up'] ?? 0),
                (int) ($data['bytes_down'] ?? $data['bandwidth_down'] ?? 0)
            );

            return;
        }

        if ($type === EventType::VideoCompleted->value) {
            app(SessionManager::class)->completeVideo($session, (int) ($data['video_watch_duration'] ?? 0));

            return;
        }

        $updates = [];

        if ($type === EventType::SessionStarted->value || $status === 'active') {
            $updates['status'] = 'active';
            $updates['session_started_at'] = $data['session_started_at'] ?? $data['started_at'] ?? $session->session_started_at;
        }

        foreach (['mac_address', 'phone', 'total_duration', 'bandwidth_used', 'device_type', 'browser', 'auth_method'] as $field) {
            if (array_key_exists($field, $data)) {
                $updates[$field] = $data[$field];
            }
        }

        if ($updates !== []) {
            $session->update($updates);
        }
    }

    private function updateHotspot(array $data, string $type): void
    {
        $routerId = (int) ($data['router_id'] ?? 0);

        if (! $routerId) {
            return;
        }

        $hotspot = Hotspot::withTrashed()->where('router_id', $routerId)->first();

        if (! $hotspot) {
            return;
        }

        $hotspot->update([
            'status' => $type === EventType::HotspotUp->value ? 'online' : 'offline',
            'last_seen_at' => now(),
        ]);
    }

    private function hotspotIdForRouter(mixed $routerId): ?int
    {
        if (! $routerId) {
            return null;
        }

        return Hotspot::where('router_id', (int) $routerId)->value('id');
    }
}
