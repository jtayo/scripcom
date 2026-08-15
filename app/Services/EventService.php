<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Organization;

class EventService
{
    public function track(
        string $eventType,
        ?Organization $organization = null,
        ?int $sessionId = null,
        ?int $hotspotId = null,
        ?int $campaignId = null,
        array $payload = []
    ): Event {
        $event = Event::create([
            'organization_id' => $organization?->id,
            'session_id' => $sessionId,
            'hotspot_id' => $hotspotId,
            'campaign_id' => $campaignId,
            'event_type' => $eventType,
            'payload' => $payload,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'occurred_at' => now(),
        ]);

        app(AnalyticsService::class)->forget($organization);

        return $event;
    }

    public function getEvents(?Organization $organization, int $limit = 50)
    {
        $query = Event::query()
            ->with(['hotspot:id,name', 'campaign:id,title'])
            ->latest('occurred_at');

        if ($organization) {
            $query->where('organization_id', $organization->id);
        }

        return $query->limit($limit)->get();
    }

    public function getEventCountByType(?Organization $organization, string $type, $since = null): int
    {
        $query = Event::query()->where('event_type', $type);

        if ($organization) {
            $query->where('organization_id', $organization->id);
        }

        if ($since) {
            $query->where('occurred_at', '>=', $since);
        }

        return $query->count();
    }
}
