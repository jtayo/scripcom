<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\Event;
use App\Models\Hotspot;
use App\Models\Payment;
use App\Models\Sponsorship;
use App\Models\WifiSession;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ReportService
{
    /**
     * Definitions for every available report type. Columns are ordered; the
     * order also drives the export headings.
     *
     * @return array<string, array{title: string, description: string, icon: string, filters: array<int, string>, columns: array<string, string>}>
     */
    public function definitions(): array
    {
        return [
            'usage' => [
                'title' => 'Usage Summary',
                'description' => 'Aggregated daily usage: sessions, active sessions, hours online, bandwidth and video completions.',
                'icon' => 'ti ti-chart-bar',
                'filters' => ['from', 'to'],
                'columns' => [
                    'date' => 'Date',
                    'sessions' => 'Sessions',
                    'active_sessions' => 'Active Sessions',
                    'total_hours' => 'Hours',
                    'bandwidth_mb' => 'Bandwidth (MB)',
                    'video_completions' => 'Video Completions',
                ],
            ],
            'sessions' => [
                'title' => 'Session Details',
                'description' => 'Individual Wi-Fi sessions with duration, bandwidth and campaign attribution.',
                'icon' => 'ti ti-link',
                'filters' => ['from', 'to', 'status'],
                'columns' => [
                    'session_id' => 'Session ID',
                    'started_at' => 'Started At',
                    'ended_at' => 'Ended At',
                    'status' => 'Status',
                    'hotspot' => 'Hotspot',
                    'campaign' => 'Campaign',
                    'phone' => 'Phone',
                    'mac_address' => 'MAC Address',
                    'device_type' => 'Device',
                    'duration_hours' => 'Duration (h)',
                    'bandwidth_mb' => 'Bandwidth (MB)',
                    'video_completed' => 'Video Completed',
                ],
            ],
            'events' => [
                'title' => 'Event Log',
                'description' => 'Captive-portal and system events by type over a date range.',
                'icon' => 'ti ti-list-check',
                'filters' => ['from', 'to', 'event_type'],
                'columns' => [
                    'occurred_at' => 'Occurred At',
                    'event_type' => 'Event Type',
                    'hotspot' => 'Hotspot',
                    'campaign' => 'Campaign',
                    'ip_address' => 'IP Address',
                    'user_agent' => 'User Agent',
                ],
            ],
            'payments' => [
                'title' => 'Payments',
                'description' => 'M-Pesa transaction records: amounts, receipts, statuses and dates.',
                'icon' => 'ti ti-cash',
                'filters' => ['from', 'to', 'status'],
                'columns' => [
                    'created_at' => 'Created At',
                    'transacted_at' => 'Transacted At',
                    'phone' => 'Phone',
                    'amount' => 'Amount',
                    'currency' => 'Currency',
                    'status' => 'Status',
                    'receipt' => 'Receipt',
                    'result_description' => 'Result Description',
                ],
            ],
            'hotspots' => [
                'title' => 'Hotspots',
                'description' => 'Hotspot inventory and current state: location, status, activity and capacity.',
                'icon' => 'ti ti-map-pin',
                'filters' => ['status'],
                'columns' => [
                    'name' => 'Name',
                    'router_id' => 'Router ID',
                    'ssid' => 'SSID',
                    'ward' => 'Ward',
                    'sub_county' => 'Sub County',
                    'status' => 'Status',
                    'is_active' => 'Active',
                    'max_clients' => 'Max Clients',
                    'last_seen_at' => 'Last Seen At',
                    'sessions_count' => 'Sessions',
                ],
            ],
            'campaigns' => [
                'title' => 'Campaign Performance',
                'description' => 'Advertising campaign plays, session conversions and scheduling.',
                'icon' => 'ti ti-megaphone',
                'filters' => ['status'],
                'columns' => [
                    'title' => 'Title',
                    'sponsor' => 'Sponsor',
                    'type' => 'Type',
                    'status' => 'Status',
                    'current_plays' => 'Plays',
                    'max_plays' => 'Max Plays',
                    'sessions_count' => 'Sessions',
                    'starts_at' => 'Starts At',
                    'ends_at' => 'Ends At',
                ],
            ],
            'sponsorships' => [
                'title' => 'Sponsorship Utilization',
                'description' => 'Sponsorship credit purchases, consumption, remaining balance and value.',
                'icon' => 'ti ti-heart-handshake',
                'filters' => ['status'],
                'columns' => [
                    'reference' => 'Reference',
                    'sponsor' => 'Sponsor',
                    'type' => 'Type',
                    'quantity_purchased' => 'Purchased',
                    'quantity_used' => 'Used',
                    'remaining' => 'Remaining',
                    'utilization_rate' => 'Utilization (%)',
                    'sessions_count' => 'Sessions',
                    'total_amount' => 'Total Amount',
                    'currency' => 'Currency',
                    'status' => 'Status',
                    'starts_at' => 'Starts At',
                    'expires_at' => 'Expires At',
                ],
            ],
        ];
    }

    public function definition(string $type): ?array
    {
        return $this->definitions()[$type] ?? null;
    }

    /**
     * Build the rows for a report type. Each row is an associative array whose
     * keys match the report's column keys.
     *
     * @param  array<string, mixed>  $filters
     * @return array<int, array<string, mixed>>
     */
    public function data(string $type, array $filters = []): array
    {
        return match ($type) {
            'usage' => $this->usage($filters),
            'sessions' => $this->sessions($filters),
            'events' => $this->events($filters),
            'payments' => $this->payments($filters),
            'hotspots' => $this->hotspots($filters),
            'campaigns' => $this->campaigns($filters),
            'sponsorships' => $this->sponsorships($filters),
            default => [],
        };
    }

    /**
     * Distinct values used to populate a report's filter selects.
     *
     * @return array<string, array<int, string>>
     */
    public function options(string $type): array
    {
        $statuses = fn (string $model): array => $model::query()
            ->tap(fn (Builder $q) => $this->scopeOrganization($q))
            ->whereNotNull('status')
            ->distinct()
            ->orderBy('status')
            ->pluck('status')
            ->values()
            ->all();

        return match ($type) {
            'sessions' => ['status' => $statuses(WifiSession::class)],
            'payments' => ['status' => $statuses(Payment::class)],
            'hotspots' => ['status' => $statuses(Hotspot::class)],
            'campaigns' => ['status' => $statuses(Campaign::class)],
            'sponsorships' => ['status' => $statuses(Sponsorship::class)],
            'events' => ['event_type' => Event::query()
                ->tap(fn (Builder $q) => $this->scopeOrganization($q))
                ->whereNotNull('event_type')
                ->distinct()
                ->orderBy('event_type')
                ->pluck('event_type')
                ->values()
                ->all()],
            default => [],
        };
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array<int, array<string, mixed>>
     */
    private function usage(array $filters): array
    {
        [$from, $to] = $this->range($filters);
        $from ??= now()->subDays(14)->toDateString();
        $to ??= now()->toDateString();

        $sessions = WifiSession::query()
            ->tap(fn (Builder $q) => $this->scopeOrganization($q))
            ->whereBetween('session_started_at', [$from.' 00:00:00', $to.' 23:59:59'])
            ->selectRaw('DATE(session_started_at) as date, COUNT(*) as total, SUM(total_duration) as seconds, SUM(bandwidth_used) as bandwidth, SUM(status = "active") as active')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $videoCompletions = Event::query()
            ->tap(fn (Builder $q) => $this->scopeOrganization($q))
            ->where('event_type', 'video.completed')
            ->whereBetween('occurred_at', [$from.' 00:00:00', $to.' 23:59:59'])
            ->selectRaw('DATE(occurred_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        $rows = [];
        $cursor = Carbon::parse($from);
        $end = Carbon::parse($to);

        while ($cursor->lte($end)) {
            $key = $cursor->toDateString();
            $day = $sessions->get($key);

            $rows[] = [
                'date' => $key,
                'sessions' => (int) ($day->total ?? 0),
                'active_sessions' => (int) ($day->active ?? 0),
                'total_hours' => round(($day->seconds ?? 0) / 3600, 2),
                'bandwidth_mb' => round(($day->bandwidth ?? 0) / (1024 * 1024), 2),
                'video_completions' => (int) ($videoCompletions->get($key)->total ?? 0),
            ];

            $cursor->addDay();
        }

        return $rows;
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array<int, array<string, mixed>>
     */
    private function sessions(array $filters): array
    {
        [$from, $to] = $this->range($filters);

        return WifiSession::query()
            ->with(['hotspot:id,name', 'campaign:id,title'])
            ->tap(fn (Builder $q) => $this->scopeOrganization($q))
            ->when($from, fn (Builder $q) => $q->where('session_started_at', '>=', $from.' 00:00:00'))
            ->when($to, fn (Builder $q) => $q->where('session_started_at', '<=', $to.' 23:59:59'))
            ->when(! empty($filters['status']), fn (Builder $q) => $q->where('status', $filters['status']))
            ->latest('session_started_at')
            ->limit(10000)
            ->get()
            ->map(fn (WifiSession $s) => [
                'session_id' => $s->session_id,
                'started_at' => $s->session_started_at?->toDateTimeString(),
                'ended_at' => $s->ended_at?->toDateTimeString(),
                'status' => $s->status,
                'hotspot' => $s->hotspot?->name,
                'campaign' => $s->campaign?->title,
                'phone' => $s->phone,
                'mac_address' => $s->mac_address,
                'device_type' => $s->device_type,
                'duration_hours' => $s->durationHours(),
                'bandwidth_mb' => $s->bandwidthMb(),
                'video_completed' => $s->video_completed ? 'Yes' : 'No',
            ])
            ->all();
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array<int, array<string, mixed>>
     */
    private function events(array $filters): array
    {
        [$from, $to] = $this->range($filters);

        return Event::query()
            ->with(['hotspot:id,name', 'campaign:id,title'])
            ->tap(fn (Builder $q) => $this->scopeOrganization($q))
            ->when($from, fn (Builder $q) => $q->where('occurred_at', '>=', $from.' 00:00:00'))
            ->when($to, fn (Builder $q) => $q->where('occurred_at', '<=', $to.' 23:59:59'))
            ->when(! empty($filters['event_type']), fn (Builder $q) => $q->where('event_type', $filters['event_type']))
            ->latest('occurred_at')
            ->limit(10000)
            ->get()
            ->map(fn (Event $e) => [
                'occurred_at' => $e->occurred_at?->toDateTimeString(),
                'event_type' => $e->event_type,
                'hotspot' => $e->hotspot?->name,
                'campaign' => $e->campaign?->title,
                'ip_address' => $e->ip_address,
                'user_agent' => $e->user_agent,
            ])
            ->all();
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array<int, array<string, mixed>>
     */
    private function payments(array $filters): array
    {
        [$from, $to] = $this->range($filters);

        return Payment::query()
            ->with(['organization:id,name', 'sponsorship:id,reference'])
            ->tap(fn (Builder $q) => $this->scopeOrganization($q))
            ->when($from, fn (Builder $q) => $q->where('created_at', '>=', $from.' 00:00:00'))
            ->when($to, fn (Builder $q) => $q->where('created_at', '<=', $to.' 23:59:59'))
            ->when(! empty($filters['status']), fn (Builder $q) => $q->where('status', $filters['status']))
            ->latest()
            ->limit(10000)
            ->get()
            ->map(fn (Payment $p) => [
                'created_at' => $p->created_at?->toDateTimeString(),
                'transacted_at' => $p->transacted_at?->toDateTimeString(),
                'phone' => $p->phone,
                'amount' => $p->amount,
                'currency' => $p->currency,
                'status' => $p->status,
                'receipt' => $p->mpesa_receipt_number,
                'result_description' => $p->result_description,
            ])
            ->all();
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array<int, array<string, mixed>>
     */
    private function hotspots(array $filters): array
    {
        return Hotspot::query()
            ->withCount('sessions')
            ->tap(fn (Builder $q) => $this->scopeOrganization($q))
            ->when(! empty($filters['status']), fn (Builder $q) => $q->where('status', $filters['status']))
            ->orderBy('name')
            ->get()
            ->map(fn (Hotspot $h) => [
                'name' => $h->name,
                'router_id' => $h->router_id,
                'ssid' => $h->ssid,
                'ward' => $h->ward,
                'sub_county' => $h->sub_county,
                'status' => $h->status,
                'is_active' => $h->is_active ? 'Yes' : 'No',
                'max_clients' => $h->max_clients,
                'last_seen_at' => $h->last_seen_at?->toDateTimeString(),
                'sessions_count' => $h->sessions_count,
            ])
            ->all();
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array<int, array<string, mixed>>
     */
    private function campaigns(array $filters): array
    {
        return Campaign::query()
            ->with('sponsor:id,name')
            ->withCount('sessions')
            ->tap(fn (Builder $q) => $this->scopeOrganization($q))
            ->when(! empty($filters['status']), fn (Builder $q) => $q->where('status', $filters['status']))
            ->orderByDesc('current_plays')
            ->get()
            ->map(fn (Campaign $c) => [
                'title' => $c->title,
                'sponsor' => $c->sponsor?->name,
                'type' => $c->type,
                'status' => $c->status,
                'current_plays' => $c->current_plays,
                'max_plays' => $c->max_plays,
                'sessions_count' => $c->sessions_count,
                'starts_at' => $c->starts_at?->toDateString(),
                'ends_at' => $c->ends_at?->toDateString(),
            ])
            ->all();
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array<int, array<string, mixed>>
     */
    private function sponsorships(array $filters): array
    {
        return Sponsorship::query()
            ->with('sponsor:id,name')
            ->withCount('sessions')
            ->tap(fn (Builder $q) => $this->scopeOrganization($q))
            ->when(! empty($filters['status']), fn (Builder $q) => $q->where('status', $filters['status']))
            ->orderByDesc('quantity_used')
            ->get()
            ->map(fn (Sponsorship $s) => [
                'reference' => $s->reference,
                'sponsor' => $s->sponsor?->name,
                'type' => $s->type,
                'quantity_purchased' => $s->quantity_purchased,
                'quantity_used' => $s->quantity_used,
                'remaining' => $s->remaining,
                'utilization_rate' => $s->utilization_rate,
                'sessions_count' => $s->sessions_count,
                'total_amount' => $s->total_amount,
                'currency' => $s->currency,
                'status' => $s->status,
                'starts_at' => $s->starts_at?->toDateString(),
                'expires_at' => $s->expires_at?->toDateString(),
            ])
            ->all();
    }

    /**
     * @return array{0: string|null, 1: string|null}
     */
    private function range(array $filters): array
    {
        $from = isset($filters['from']) && is_string($filters['from']) ? trim($filters['from']) : '';
        $to = isset($filters['to']) && is_string($filters['to']) ? trim($filters['to']) : '';

        if ($from !== '' && $to !== '' && $from > $to) {
            [$from, $to] = [$to, $from];
        }

        return [$from !== '' ? $from : null, $to !== '' ? $to : null];
    }

    private function scopeOrganization(Builder $query, string $column = 'organization_id'): Builder
    {
        $user = Auth::user();

        if (! $user || $user->isSuperAdmin()) {
            return $query;
        }

        return $query->where($column, $user->organization_id);
    }
}
