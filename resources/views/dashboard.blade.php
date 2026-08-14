@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Overview of your Wi-Fi network and civic engagement')
@section('head', '')

@push('styles')
<style>
    .stat-card {
        transition: transform .2s ease, box-shadow .2s ease;
    }
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 1rem 2rem rgba(17, 24, 39, .08) !important;
    }
    .stat-icon {
        width: 3.25rem;
        height: 3.25rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: .9rem;
        font-size: 1.25rem;
    }
    .stat-value {
        font-size: 1.75rem;
        line-height: 1.1;
    }
    .stat-label {
        font-size: .75rem;
        text-transform: uppercase;
        letter-spacing: .04em;
    }
    .dashboard-card .card-header {
        background: transparent;
        border-bottom: 1px solid var(--tblr-border-color);
        padding: 1rem 1.25rem;
    }
</style>
@endpush

@section('content')
    <div class="row">
        <div class="col-12 col-xl-4 mb-4">
            <div class="card stat-card">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                            <i class="fa-solid fa-signal"></i>
                        </div>
                        <div class="ms-3">
                            <p class="stat-label text-muted fw-bold mb-1">Total Sessions</p>
                            <div class="stat-value fw-bolder text-body">{{ number_format($stats['total_sessions']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-4 mb-4">
            <div class="card stat-card">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-success bg-opacity-10 text-success">
                            <i class="fa-solid fa-circle-play"></i>
                        </div>
                        <div class="ms-3">
                            <p class="stat-label text-muted fw-bold mb-1">Active Sessions</p>
                            <div class="stat-value fw-bolder text-body">{{ number_format($stats['active_sessions']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-4 mb-4">
            <div class="card stat-card">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-indigo bg-opacity-10 text-indigo">
                            <i class="fa-solid fa-wifi"></i>
                        </div>
                        <div class="ms-3">
                            <p class="stat-label text-muted fw-bold mb-1">Online Hotspots</p>
                            <div class="stat-value fw-bolder text-body">
                                {{ $stats['online_hotspots'] }}
                                <span class="fs-6 fw-normal text-muted">/ {{ $stats['total_hotspots'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-xl-4 mb-4">
            <div class="card stat-card">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-info bg-opacity-10 text-info">
                            <i class="fa-solid fa-database"></i>
                        </div>
                        <div class="ms-3">
                            <p class="stat-label text-muted fw-bold mb-1">Bandwidth Used</p>
                            <div class="stat-value fw-bolder text-body">
                                {{ number_format($stats['bandwidth_mb'], 1) }}
                                <span class="fs-6 fw-normal text-muted">MB</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-4 mb-4">
            <div class="card stat-card">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                        <div class="ms-3">
                            <p class="stat-label text-muted fw-bold mb-1">Total Uptime</p>
                            <div class="stat-value fw-bolder text-body">
                                {{ number_format($stats['total_hours'], 1) }}
                                <span class="fs-6 fw-normal text-muted">hours</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-4 mb-4">
            <div class="card stat-card">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-danger bg-opacity-10 text-danger">
                            <i class="fa-solid fa-video"></i>
                        </div>
                        <div class="ms-3">
                            <p class="stat-label text-muted fw-bold mb-1">Video Completions</p>
                            <div class="stat-value fw-bolder text-body">{{ number_format($stats['video_completions']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-xl-8 mb-4">
            <div class="card dashboard-card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="h6 mb-0 fw-bold">
                        <i class="fa-solid fa-chart-line text-primary me-2"></i>Sessions (Last 14 Days)
                    </h2>
                </div>
                <div class="card-body px-2 py-4">
                    <div id="chart-sessions" class="chart chart-sm" style="height: 300px;"></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-4 mb-4">
            <div class="card dashboard-card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="h6 mb-0 fw-bold">
                        <i class="fa-solid fa-clock-rotate-left text-primary me-2"></i>Sessions by Hour
                    </h2>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-vcenter table-nowrap mb-0 rounded">
                            <tbody>
                                @foreach($sessionsByHour['labels'] as $i => $label)
                                <tr>
                                    <td class="border-0 fw-bold text-body">{{ $label }}</td>
                                    <td class="border-0">
                                        <div class="progress w-100" style="height: 8px;">
                                            @php $max = max(1, max($sessionsByHour['values'])); @endphp
                                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ round($sessionsByHour['values'][$i] / $max * 100) }}%" aria-valuenow="{{ $sessionsByHour['values'][$i] }}" aria-valuemin="0" aria-valuemax="{{ $max }}"></div>
                                        </div>
                                    </td>
                                    <td class="border-0 text-muted text-end">{{ number_format($sessionsByHour['values'][$i]) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-xl-6 mb-4">
            <div class="card dashboard-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="h6 mb-0 fw-bold">
                        <i class="fa-solid fa-bullhorn text-primary me-2"></i>Top Campaigns
                    </h2>
                </div>
                <div class="table-responsive">
                    <table class="table align-items-center card-table mb-0">
                        <thead class="">
                            <tr>
                                <th class="border-bottom" scope="col">Campaign</th>
                                <th class="border-bottom" scope="col">Plays</th>
                                <th class="border-bottom" scope="col">Sessions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($campaigns as $campaign)
                            <tr>
                                <td class="text-body fw-bold">{{ $campaign->title }}</td>
                                <td>{{ number_format($campaign->current_plays) }}</td>
                                <td><span class="badge bg-primary bg-opacity-10 text-primary">{{ number_format($campaign->sessions_count) }}</span></td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center text-muted py-4">No campaigns yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-6 mb-4">
            <div class="card dashboard-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="h6 mb-0 fw-bold">
                        <i class="fa-solid fa-tower-broadcast text-primary me-2"></i>Top Hotspots
                    </h2>
                </div>
                <div class="table-responsive">
                    <table class="table align-items-center card-table mb-0">
                        <thead class="">
                            <tr>
                                <th class="border-bottom" scope="col">Hotspot</th>
                                <th class="border-bottom" scope="col">Sessions</th>
                                <th class="border-bottom" scope="col">Bandwidth</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topHotspots as $hotspot)
                            <tr>
                                <td class="text-body fw-bold">{{ $hotspot->name }}</td>
                                <td>{{ number_format($hotspot->sessions_count) }}</td>
                                <td class="text-muted">{{ number_format($hotspot->total_bandwidth / (1024 * 1024), 1) }} MB</td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center text-muted py-4">No hotspots yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-xl-6 mb-4">
            <div class="card dashboard-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="h6 mb-0 fw-bold">
                        <i class="fa-solid fa-list text-primary me-2"></i>Recent Sessions
                    </h2>
                </div>
                <div class="table-responsive">
                    <table class="table align-items-center card-table mb-0">
                        <thead class="">
                            <tr>
                                <th class="border-bottom" scope="col">Phone</th>
                                <th class="border-bottom" scope="col">Hotspot</th>
                                <th class="border-bottom" scope="col">Campaign</th>
                                <th class="border-bottom" scope="col">Started</th>
                                <th class="border-bottom" scope="col">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentSessions as $session)
                            <tr>
                                <td class="text-body fw-bold">{{ $session->phone }}</td>
                                <td>{{ $session->hotspot->name ?? '—' }}</td>
                                <td>{{ $session->campaign->title ?? '—' }}</td>
                                <td>{{ $session->session_started_at?->format('M d, H:i') }}</td>
                                <td><span class="badge bg-{{ $session->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($session->status) }}</span></td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">No sessions yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-6 mb-4">
            <div class="card dashboard-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="h6 mb-0 fw-bold">
                        <i class="fa-solid fa-calendar-days text-primary me-2"></i>Recent Events
                    </h2>
                </div>
                <div class="table-responsive">
                    <table class="table align-items-center card-table mb-0">
                        <thead class="">
                            <tr>
                                <th class="border-bottom" scope="col">Event</th>
                                <th class="border-bottom" scope="col">Hotspot</th>
                                <th class="border-bottom" scope="col">Campaign</th>
                                <th class="border-bottom" scope="col">When</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentEvents as $event)
                            <tr>
                                <td class="text-body fw-bold">{{ $event->event_type }}</td>
                                <td>{{ $event->hotspot->name ?? '—' }}</td>
                                <td>{{ $event->campaign->title ?? '—' }}</td>
                                <td class="text-muted">{{ $event->occurred_at?->diffForHumans() }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">No events yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    new Chartist.Line('#chart-sessions', {
        labels: @json($sessionsPerDay['labels']),
        series: [@json($sessionsPerDay['values'])],
    }, {
        low: 0,
        showArea: true,
        fullWidth: true,
        axisY: { onlyInteger: true, offset: 30 },
        axisX: { showGrid: false },
        lineSmooth: Chartist.Interpolation.simple({ divisor: 3 }),
    });
</script>
@endpush
