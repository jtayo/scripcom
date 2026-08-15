<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\Organization;
use App\Models\Sponsorship;
use App\Models\WifiSession;
use Illuminate\Support\Str;

class SessionManager
{
    public function startSession(array $data): WifiSession
    {
        $sponsorship = isset($data['sponsorship_id'])
            ? Sponsorship::find($data['sponsorship_id'])
            : null;

        if ($sponsorship && $sponsorship->status === 'active' && $sponsorship->remaining > 0) {
            $sponsorship->increment('quantity_used');
        }

        if (isset($data['campaign_id'])) {
            Campaign::where('id', $data['campaign_id'])->increment('current_plays');
        }

        $session = WifiSession::create(array_merge($data, [
            'session_id' => (string) Str::uuid(),
            'session_started_at' => $data['session_started_at'] ?? now(),
            'status' => 'active',
        ]));

        $this->clearAnalytics($data['organization_id'] ?? null);

        return $session;
    }

    public function endSession(WifiSession $session, string $reason = 'ended'): WifiSession
    {
        $session->update([
            'status' => 'completed',
            'ended_at' => now(),
            'end_reason' => $reason,
            'total_duration' => $session->total_duration ?: $session->session_started_at?->diffInSeconds(now()),
        ]);

        $this->clearAnalytics($session->organization_id);

        return $session;
    }

    private function clearAnalytics($organizationId): void
    {
        $organization = $organizationId ? Organization::find($organizationId) : null;

        app(AnalyticsService::class)->forget($organization);
    }

    public function completeVideo(WifiSession $session, int $watchDuration): WifiSession
    {
        $session->update([
            'video_completed' => true,
            'video_watch_duration' => $watchDuration,
        ]);

        return $session;
    }

    public function updateBandwidth(WifiSession $session, int $bytesUp, int $bytesDown): WifiSession
    {
        $session->update([
            'bandwidth_up' => $bytesUp,
            'bandwidth_down' => $bytesDown,
            'bandwidth_used' => $bytesUp + $bytesDown,
            'last_heartbeat_at' => now(),
        ]);

        return $session;
    }
}
