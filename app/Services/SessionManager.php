<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\WifiSession;
use Illuminate\Support\Str;

class SessionManager
{
    public function startSession(array $data): WifiSession
    {
        $sponsorship = isset($data['sponsorship_id'])
            ? \App\Models\Sponsorship::find($data['sponsorship_id'])
            : null;

        if ($sponsorship && $sponsorship->status === 'active' && $sponsorship->remaining > 0) {
            $sponsorship->increment('quantity_used');
        }

        if (isset($data['campaign_id'])) {
            Campaign::where('id', $data['campaign_id'])->increment('current_plays');
        }

        return WifiSession::create(array_merge($data, [
            'session_id' => (string) Str::uuid(),
            'session_started_at' => $data['session_started_at'] ?? now(),
            'status' => 'active',
        ]));
    }

    public function endSession(WifiSession $session, string $reason = 'ended'): WifiSession
    {
        $session->update([
            'status' => 'completed',
            'ended_at' => now(),
            'end_reason' => $reason,
            'total_duration' => $session->total_duration ?: $session->session_started_at?->diffInSeconds(now()),
        ]);

        return $session;
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
