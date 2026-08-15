@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Overview of your Wi-Fi network and civic engagement')
@section('body-attrs', 'data-bs-theme="dark" class="dashboard-dark"')

@php
    $trendUp = fn($v) => $v !== null && $v > 0;
    $trendBadge = function ($key, $label) use ($trends) {
        $v = $trends[$key] ?? null;
        if ($v === null) {
            return '';
        }
        $up = $v >= 0;
        return '<span class="badge bg-' .
            ($up ? 'success' : 'danger') .
            '-lt ms-1">
            <i class="ti ti-trending-' .
            ($up ? 'up' : 'down') .
            ' me-1"></i>' .
            number_format(abs($v), 1) .
            '%
        </span><span class="text-muted small ms-1">' .
            $label .
            '</span>';
    };
@endphp

@section('content')
    <div class="row row-cards">

        <div class="col-12 col-sm-6 col-xl-4">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-primary-lt text-primary me-3">
                            <i class="ti ti-chart-dots-2"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="stat-label text-muted mb-1">Total Sessions</div>
                            <div class="stat-value fw-bolder text-body">
                                {{ number_format($liveSessions['total']) }}
                                <span class="fs-6 fw-normal text-muted">in 7d</span>
                            </div>
                            <div class="mt-1">
                                <span class="badge bg-secondary-lt">
                                    <i class="ti ti-clock-off me-1"></i>{{ number_format($liveSessions['expired']) }} expired
                                    · {{ number_format($liveSessions['failed']) }} failed
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-4">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-success-lt text-success me-3">
                            <i class="ti ti-player-play"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="stat-label text-muted mb-1">Active Sessions</div>
                            <div class="stat-value fw-bolder text-body">
                                {{ number_format($activeSessionsToday['active']) }}
                                <span class="fs-6 fw-normal text-muted">today</span>
                            </div>
                            <div class="mt-1">
                                <span class="badge bg-success-lt">
                                    <i class="ti ti-refresh me-1"></i>{{ number_format($activeSessionsToday['expired']) }} expired
                                    · {{ number_format($activeSessionsToday['total']) }} today
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-4">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-indigo-lt text-indigo me-3">
                            <i class="ti ti-wifi"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="stat-label text-muted mb-1">Online Hotspots</div>
                            <div class="stat-value fw-bolder text-body">
                                {{ $stats['online_hotspots'] }}
                                <span class="fs-6 fw-normal text-muted">/ {{ $stats['total_hotspots'] }}</span>
                            </div>
                            <div class="mt-1">{!! $trendBadge('online_hotspots', 'vs prev. 7d') !!}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-4">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-info-lt text-info me-3">
                            <i class="ti ti-database"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="stat-label text-muted mb-1">Bandwidth Used</div>
                            <div class="stat-value fw-bolder text-body">
                                {{ number_format($stats['bandwidth_mb'], 1) }}
                                <span class="fs-6 fw-normal text-muted">MB</span>
                            </div>
                            <div class="mt-1">{!! $trendBadge('bandwidth_mb', 'vs prev. 7d') !!}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-4">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-warning-lt text-warning me-3">
                            <i class="ti ti-clock"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="stat-label text-muted mb-1">Total Uptime</div>
                            <div class="stat-value fw-bolder text-body">
                                {{ number_format($stats['total_hours'], 1) }}
                                <span class="fs-6 fw-normal text-muted">hrs</span>
                            </div>
                            <div class="mt-1">{!! $trendBadge('total_hours', 'vs prev. 7d') !!}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-4">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-danger-lt text-danger me-3">
                            <i class="ti ti-video"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="stat-label text-muted mb-1">Video Completions</div>
                            <div class="stat-value fw-bolder text-body">{!! number_format($stats['video_completions']) !!}</div>
                            <div class="mt-1">{!! $trendBadge('video_completions', 'vs prev. 7d') !!}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cards">
        <div class="col-12">
            <div class="card dashboard-card">
                <div class="card-header">
                    <h2 class="card-title mb-0">
                        <i class="ti ti-map-2 text-primary me-2"></i>Hotspot Map
                    </h2>
                    <div class="card-actions">
                        <span class="badge bg-success-lt" id="map-online-count">
                            <i
                                class="ti ti-wifi me-1"></i>{{ $hotspotMarkers ? collect($hotspotMarkers)->filter(fn($h) => $h['online'])->count() : 0 }}
                            online
                        </span>
                        @can('view-any-hotspot')
                        <a href="{{ route('admin.hotspots.index') }}" class="btn btn-sm btn-outline-secondary">View all</a>
                        @endcan
                    </div>
                </div>
                <div class="card-body p-0">
                    <div id="hotspot-map" style="height: 420px; width: 100%;"></div>
                </div>
            </div>
        </div>
    </div>

    @if (!empty($liveSessions['routers']))
        <div class="row row-cards">
            <div class="col-12">
                <div class="card dashboard-card">
                    <div class="card-header">
                        <h2 class="card-title mb-0">
                            <i class="ti ti-device-mobile text-primary me-2"></i>Live Sessions by Hotspot
                        </h2>
                        <div class="card-actions">
                            <span class="badge bg-secondary-lt">
                                <i class="ti ti-calendar me-1"></i>{{ \Carbon\Carbon::now()->subDays(7)->format('M d') }} –
                                {{ \Carbon\Carbon::now()->format('M d') }}
                            </span>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table mb-0">
                            <thead>
                                <tr>
                                    <th>Hotspot</th>
                                    <th class="text-end">Total</th>
                                    <th class="text-end">Active</th>
                                    <th class="text-end">Expired</th>
                                    <th class="text-end">Failed</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($liveSessions['routers'] as $router)
                                    <tr>
                                        <td class="text-body fw-bold">
                                            {{ $router['name'] }}
                                            <span class="text-muted small fw-normal">#{{ $router['router_id'] }}</span>
                                        </td>
                                        <td class="text-end">{{ number_format($router['total']) }}</td>
                                        <td class="text-end">
                                            @if ($router['active'] > 0)
                                                <span
                                                    class="badge bg-success-lt">{{ number_format($router['active']) }}</span>
                                            @else
                                                <span class="text-muted">0</span>
                                            @endif
                                        </td>
                                        <td class="text-end text-muted">{{ number_format($router['expired']) }}</td>
                                        <td class="text-end text-muted">{{ number_format($router['failed']) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="row row-cards">
        <div class="col-12 col-xl-8">
            <div class="card dashboard-card h-100">
                <div class="card-header">
                    <h2 class="card-title mb-0">
                        <i class="ti ti-chart-line text-primary me-2"></i>Sessions (Last 14 Days)
                    </h2>
                    <div class="card-actions">
                        <span class="badge bg-primary-lt">{{ array_sum($sessionsPerDay['values']) }} total</span>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="chart-sessions" style="height: 280px; width: 100%;"></canvas>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-4">
            <div class="card dashboard-card h-100">
                <div class="card-header">
                    <h2 class="card-title mb-0">
                        <i class="ti ti-clock-hour-4 text-primary me-2"></i>Sessions by Hour
                    </h2>
                </div>
                <div class="card-body">
                    @php $maxHour = max(1, max($sessionsByHour['values'])); @endphp
                    @foreach ($sessionsByHour['labels'] as $i => $label)
                        <div class="d-flex align-items-center mb-2">
                            <span class="text-muted small me-2" style="width: 3rem;">{{ $label }}</span>
                            <div class="progress flex-grow-1" style="height: 6px;">
                                <div class="progress-bar bg-primary" role="progressbar"
                                    style="width: {{ round(($sessionsByHour['values'][$i] / $maxHour) * 100) }}%"
                                    aria-valuenow="{{ $sessionsByHour['values'][$i] }}" aria-valuemin="0"
                                    aria-valuemax="{{ $maxHour }}"></div>
                            </div>
                            <span class="text-muted small text-end ms-2"
                                style="width: 2rem;">{{ number_format($sessionsByHour['values'][$i]) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cards">
        <div class="col-12 col-xl-6">
            <div class="card dashboard-card">
                <div class="card-header">
                    <h2 class="card-title mb-0">
                        <i class="ti ti-speakerphone text-primary me-2"></i>Top Campaigns
                    </h2>
                    <div class="card-actions">
                        @can('view-any-campaign')
                        <a href="{{ route('admin.campaigns.index') }}" class="btn btn-sm btn-outline-secondary">View
                            all</a>
                        @endcan
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table mb-0 text-sm">
                        <thead>
                            <tr>
                                <th>Campaign</th>
                                <th class="text-end">Plays</th>
                                <th class="text-end">Sessions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($campaigns as $campaign)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="avatar avatar-sm bg-primary-lt text-primary me-2">
                                                <i class="ti ti-speakerphone"></i>
                                            </span>
                                            <div>
                                                <a href="{{ route('admin.campaigns.show', $campaign) }}"
                                                    class="text-body fw-bold text-decoration-none">{{ $campaign->title }}</a>
                                                @if($campaign->type)
                                                    <div class="small text-muted">{{ ucfirst($campaign->type) }}
                                                        @if($campaign->is_mandatory)
                                                            <span class="text-primary">&middot; mandatory</span>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <span class="d-inline-flex align-items-center text-muted">
                                            <i class="ti ti-player-play me-1 text-secondary"></i>
                                            {{ number_format($campaign->current_plays) }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <span class="badge bg-primary-lt">{{ number_format($campaign->sessions_count) }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-5">
                                        <i class="ti ti-speakerphone text-secondary" style="font-size: 2rem;"></i>
                                        <div class="mt-2">No campaigns yet.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-6">
            <div class="card dashboard-card">
                <div class="card-header">
                    <h2 class="card-title mb-0">
                        <i class="ti ti-building-broadcast-tower text-primary me-2"></i>Top Hotspots
                    </h2>
                    <div class="card-actions">
                        @can('view-any-hotspot')
                        <a href="{{ route('admin.hotspots.index') }}" class="btn btn-sm btn-outline-secondary">View
                            all</a>
                        @endcan
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table mb-0 text-sm">
                        <thead>
                            <tr>
                                <th>Hotspot</th>
                                <th class="text-end">Sessions</th>
                                <th class="text-end">Bandwidth</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topHotspots as $hotspot)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="avatar avatar-sm bg-success-lt text-success me-2">
                                                <i class="ti ti-wifi"></i>
                                            </span>
                                            <div>
                                                <a href="{{ route('admin.hotspots.show', $hotspot) }}"
                                                    class="text-body fw-bold text-decoration-none">{{ $hotspot->name }}</a>
                                                @if($hotspot->ward || $hotspot->sub_county)
                                                    <div class="small text-muted">
                                                        @if($hotspot->ward){{ $hotspot->ward }}@endif
                                                        @if($hotspot->ward && $hotspot->sub_county) &middot; @endif
                                                        @if($hotspot->sub_county){{ $hotspot->sub_county }}@endif
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <span class="badge bg-secondary-lt">{{ number_format($hotspot->sessions_count) }}</span>
                                    </td>
                                    <td class="text-end">
                                        <span class="d-inline-flex align-items-center text-muted">
                                            <i class="ti ti-database me-1 text-secondary"></i>
                                            {{ number_format($hotspot->total_bandwidth / (1024 * 1024), 1) }} MB
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-5">
                                        <i class="ti ti-building-broadcast-tower text-secondary" style="font-size: 2rem;"></i>
                                        <div class="mt-2">No hotspots yet.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cards">
        <div class="col-12 col-xl-6">
            <div class="card dashboard-card">
                <div class="card-header">
                    <h2 class="card-title mb-0">
                        <i class="ti ti-device-mobile text-primary me-2"></i>Recent Sessions
                    </h2>
                    <div class="card-actions">
                        @can('view-any-session')
                        <a href="{{ route('admin.sessions.index') }}" class="btn btn-sm btn-outline-secondary">View
                            all</a>
                        @endcan
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table mb-0">
                        <thead>
                            <tr>
                                <th>MAC Address</th>
                                <th>Hotspot</th>
                                <th class="text-end">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentSessions as $session)
                                <tr>
                                    <td><code class="text-body fw-bold">{{ $session['mac_address'] }}</code></td>
                                    <td class="text-muted">{{ $session['router_name'] ?: '—' }}</td>
                                    <td class="text-end">
                                        <span
                                            class="badge bg-{{ strtolower($session['status']) === 'active' ? 'success' : 'secondary' }}-lt">
                                            {{ ucfirst(strtolower($session['status'])) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">No live sessions from Tolclin.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-6">
            <div class="card dashboard-card">
                <div class="card-header">
                    <h2 class="card-title mb-0">
                        <i class="ti ti-calendar-event text-primary me-2"></i>Recent Events
                    </h2>
                    <div class="card-actions">
                        @can('view-any-event')
                        <a href="{{ route('admin.events.index') }}" class="btn btn-sm btn-outline-secondary">View all</a>
                        @endcan
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table mb-0">
                        <thead>
                            <tr>
                                <th>Event</th>
                                <th>Hotspot</th>
                                <th class="text-end">When</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentEvents as $event)
                                <tr>
                                    <td>
                                        <span
                                            class="badge bg-{{ $event->event_type === 'video.completed' ? 'success' : 'info' }}-lt">
                                            {{ $event->event_type }}
                                        </span>
                                    </td>
                                    <td class="text-muted">{{ $event->hotspot->name ?? '—' }}</td>
                                    <td class="text-end text-muted">{{ $event->occurred_at?->diffForHumans() }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">No events yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('vendor/leaflet/css/leaflet.css') }}">
    <style>
        .stat-card {
            transition: transform .2s ease, box-shadow .2s ease;
            height: 100%;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 1rem 2rem rgba(17, 24, 39, .08) !important;
        }

        .stat-icon {
            width: 3rem;
            height: 3rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: .75rem;
            font-size: 1.4rem;
            flex-shrink: 0;
        }

        .stat-value {
            font-size: 1.5rem;
            line-height: 1.15;
        }

        .stat-label {
            font-size: .75rem;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .dashboard-card .card-header {
            background: transparent;
            border-bottom: 1px solid var(--tblr-border-color);
            padding: .9rem 1.25rem;
            min-height: 0;
        }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('vendor/leaflet/js/leaflet.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('chart-sessions').getContext('2d');
            const isDark = document.body.classList.contains('dashboard-dark');
            const tickColor = isDark ? '#8b98a9' : '#9aa7b0';
            const gridColor = isDark ? 'rgba(255, 255, 255, .08)' : 'rgba(17, 24, 39, .06)';
            const pointColor = isDark ? '#1c2735' : '#ffffff';
            const gradient = ctx.createLinearGradient(0, 0, 0, 280);
            gradient.addColorStop(0, 'rgba(32, 107, 196, .35)');
            gradient.addColorStop(1, 'rgba(32, 107, 196, 0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($sessionsPerDay['labels']),
                    datasets: [{
                        label: 'Sessions',
                        data: @json($sessionsPerDay['values']),
                        borderColor: '#206bc4',
                        backgroundColor: gradient,
                        borderWidth: 2,
                        pointRadius: 3,
                        pointBackgroundColor: '#206bc4',
                        pointBorderColor: pointColor,
                        pointBorderWidth: 1,
                        fill: true,
                        tension: .35,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: isDark ? 'rgba(255, 255, 255, .12)' : 'rgba(17, 24, 39, .9)',
                            padding: 10,
                            displayColors: false,
                            callbacks: {
                                title: items => items[0].label,
                                label: item => ' ' + item.parsed.y + ' sessions'
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: tickColor,
                                maxTicksLimit: 7,
                                font: {
                                    size: 11
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: tickColor,
                                precision: 0,
                                font: {
                                    size: 11
                                }
                            },
                            grid: {
                                color: gridColor
                            }
                        }
                    }
                }
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mapEl = document.getElementById('hotspot-map');
            if (!mapEl || typeof L === 'undefined') return;

            const markers = @json($hotspotMarkers);

            const map = L.map('hotspot-map', {
                scrollWheelZoom: false,
                attributionControl: false,
            });

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
            }).addTo(map);

            if (!markers.length) {
                map.setView([0, 20], 3);
                return;
            }

            const icon = (color) => L.divIcon({
                className: '',
                html: '<div class="hotspot-pin" style="--pin-color:' + color + '"></div>',
                iconSize: [24, 32],
                iconAnchor: [12, 32],
                popupAnchor: [0, -30],
            });

            const bounds = [];
            let onlineCount = 0;

            markers.forEach(function(h) {
                if (h.online) onlineCount++;

                const popup = document.createElement('div');
                popup.innerHTML = [
                    '<div class="d-flex align-items-center mb-1">',
                    '<span class="badge bg-' + (h.online ? 'success' : 'danger') + '-lt me-2">',
                    (h.online ? 'Online' : 'Offline'),
                    '</span>',
                    '<strong class="text-body">' + h.name + '</strong>',
                    '</div>',
                    h.ward ? '<div class="small text-muted">' + h.ward + (h.sub_county ? ' &middot; ' +
                        h.sub_county : '') + '</div>' : '',
                    h.ssid ? '<div class="small text-muted">SSID: ' + h.ssid + '</div>' : '',
                    '<div class="d-flex align-items-center mt-1">',
                    '<span class="badge bg-primary-lt me-2"><i class="ti ti-player-play me-1"></i>' + h
                    .active_sessions + '</span>',
                    '<span class="small text-muted">active sessions</span>',
                    '</div>',
                ].join('');

                const latlng = [h.latitude, h.longitude];
                bounds.push(latlng);

                L.marker(latlng, {
                        icon: icon(h.online ? '#2fb344' : '#d63939'),
                        title: h.name
                    })
                    .addTo(map)
                    .bindPopup(popup);
            });

            const onlineBadge = document.getElementById('map-online-count');
            if (onlineBadge) {
                onlineBadge.innerHTML = '<i class="ti ti-wifi me-1"></i>' + onlineCount + ' online';
            }

            map.fitBounds(bounds, {
                padding: [30, 30]
            });
        });
    </script>
    @push('styles')
        <style>
            .hotspot-pin {
                width: 24px;
                height: 32px;
                border-radius: 50% 50% 50% 0;
                transform: rotate(-45deg);
                background: var(--pin-color);
                border: 2px solid #fff;
                box-shadow: 0 2px 8px rgba(17, 24, 39, .3);
            }

            .hotspot-pin::after {
                content: '';
                position: absolute;
                top: 8px;
                left: 8px;
                width: 6px;
                height: 6px;
                border-radius: 50%;
                background: rgba(255, 255, 255, .9);
            }

            #hotspot-map .leaflet-popup-content {
                margin: 10px 14px;
                line-height: 1.4;
            }

            #hotspot-map .leaflet-container {
                font-family: var(--tblr-font-sans-serif);
            }
        </style>
    @endpush
