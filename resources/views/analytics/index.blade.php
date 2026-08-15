@extends('layouts.admin')

@section('title', 'Analytics')
@section('page-title', 'Analytics')
@section('page-subtitle', $organization?->name ?? 'Platform-wide overview')

@php
    $s = $summary;
    $rate = $s['completion_rate'];
    $deviceTotal = collect($devices)->sum('sessions');
@endphp

@section('content')
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body py-2">
                    <form method="GET" action="{{ route('admin.analytics') }}" class="d-flex flex-wrap align-items-center gap-2">
                        <div class="btn-group" role="group" aria-label="Quick ranges">
                            @foreach ([7 => '7d', 30 => '30d', 90 => '90d'] as $days => $label)
                                <a href="{{ route('admin.analytics', ['from' => now()->subDays($days)->toDateString(), 'to' => now()->toDateString()]) }}"
                                   class="btn btn-sm {{ (int) $from >= now()->subDays($days)->toDateString() ? 'btn-primary' : 'btn-outline-secondary' }}">{{ $label }}</a>
                            @endforeach
                        </div>
                        <div class="vr d-none d-sm-block"></div>
                        <label class="form-label mb-0 small text-muted">From</label>
                        <input type="date" name="from" class="form-control form-control-sm" style="width: auto;" value="{{ $from }}">
                        <label class="form-label mb-0 small text-muted">To</label>
                        <input type="date" name="to" class="form-control form-control-sm" style="width: auto;" value="{{ $to }}">
                        <button type="submit" class="btn btn-sm btn-primary d-inline-flex align-items-center">
                            <i class="ti ti-filter me-1"></i>Apply
                        </button>
                        <a href="{{ route('admin.analytics') }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center" title="Reset range">
                            <i class="ti ti-x"></i>
                        </a>
                        <span class="ms-auto text-muted small">
                            {{ \Carbon\Carbon::parse($from)->format('M d, Y') }} &rarr; {{ \Carbon\Carbon::parse($to)->format('M d, Y') }}
                        </span>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cards">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-primary-lt text-primary me-3">
                            <i class="ti ti-link"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="stat-label text-muted mb-1">Sessions</div>
                            <div class="stat-value fw-bolder text-body">{{ number_format($s['total_sessions']) }}</div>
                            <div class="mt-1 text-muted small">{{ number_format($s['active_sessions']) }} active now</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-success-lt text-success me-3">
                            <i class="ti ti-users"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="stat-label text-muted mb-1">Unique Users</div>
                            <div class="stat-value fw-bolder text-body">{{ number_format($s['unique_users']) }}</div>
                            <div class="mt-1 text-muted small">{{ number_format($s['repeat_users']) }} repeat users</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-info-lt text-info me-3">
                            <i class="ti ti-clock"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="stat-label text-muted mb-1">Internet Hours</div>
                            <div class="stat-value fw-bolder text-body">{{ number_format($s['total_hours'], 1) }}</div>
                            <div class="mt-1 text-muted small">avg {{ number_format($s['avg_session_minutes'], 1) }} min/session</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-warning-lt text-warning me-3">
                            <i class="ti ti-hand-heart"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="stat-label text-muted mb-1">Sponsored Sessions</div>
                            <div class="stat-value fw-bolder text-body">{{ number_format($s['sponsored_sessions']) }}</div>
                            <div class="mt-1 text-muted small">{{ number_format($s['paid_sessions']) }} paid sessions</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-4">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-cyan-lt text-cyan me-3">
                            <i class="ti ti-tower-broadcast"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="stat-label text-muted mb-1">Bandwidth</div>
                            <div class="stat-value fw-bolder text-body">{{ number_format($s['bandwidth_mb']) }} MB</div>
                            <div class="mt-1 text-muted small">down {{ number_format($s['bandwidth_down_mb']) }} &middot; up {{ number_format($s['bandwidth_up_mb']) }} MB</div>
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
                            <i class="ti ti-player-play"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="stat-label text-muted mb-1">Video Plays</div>
                            <div class="stat-value fw-bolder text-body">{{ number_format($s['video_plays']) }}</div>
                            <div class="mt-1 text-muted small">{{ number_format($s['video_completions']) }} completed</div>
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
                            <i class="ti ti-video"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="stat-label text-muted mb-1">Completion Rate</div>
                            <div class="stat-value fw-bolder text-body">
                                {{ number_format($rate, 1) }}%
                                <span class="badge bg-{{ $rate >= 90 ? 'success' : ($rate > 0 ? 'warning' : 'secondary') }}-lt ms-1">
                                    <i class="ti ti-{{ $rate >= 90 ? 'check' : ($rate > 0 ? 'alert-triangle' : 'minus') }} me-1"></i>
                                    {{ $rate >= 90 ? 'Healthy' : ($rate > 0 ? 'Watch' : 'N/A') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cards">
        <div class="col-12 col-xl-8">
            <div class="card dashboard-card h-100">
                <div class="card-header">
                    <h2 class="card-title mb-0">
                        <i class="ti ti-chart-line text-primary me-2"></i>Sessions &amp; Plays
                    </h2>
                </div>
                <div class="card-body">
                    <canvas id="chart-trend" style="height: 280px; width: 100%;"></canvas>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-4">
            <div class="card dashboard-card h-100">
                <div class="card-header">
                    <h2 class="card-title mb-0">
                        <i class="ti ti-clock-hour-4 text-primary me-2"></i>Peak Times
                    </h2>
                </div>
                <div class="card-body">
                    @php $maxHour = max(1, max($peakHours['values'])); @endphp
                    @foreach ($peakHours['labels'] as $i => $label)
                        <div class="d-flex align-items-center mb-2">
                            <span class="text-muted small me-2" style="width: 3rem;">{{ $label }}</span>
                            <div class="progress flex-grow-1" style="height: 6px;">
                                <div class="progress-bar bg-primary" role="progressbar"
                                    style="width: {{ round(($peakHours['values'][$i] / $maxHour) * 100) }}%"></div>
                            </div>
                            <span class="text-muted small text-end ms-2"
                                style="width: 2rem;">{{ number_format($peakHours['values'][$i]) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cards">
        <div class="col-12 col-xl-8">
            <div class="card dashboard-card h-100">
                <div class="card-header">
                    <h2 class="card-title mb-0">
                        <i class="ti ti-tower-broadcast text-primary me-2"></i>Bandwidth Usage
                    </h2>
                </div>
                <div class="card-body">
                    <canvas id="chart-bandwidth" style="height: 260px; width: 100%;"></canvas>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-4">
            <div class="card dashboard-card h-100">
                <div class="card-header">
                    <h2 class="card-title mb-0">
                        <i class="ti ti-device-mobile text-primary me-2"></i>Devices
                    </h2>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table mb-0">
                        <thead>
                            <tr>
                                <th>Device</th>
                                <th class="text-end">Sessions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($devices as $device)
                                <tr>
                                    <td>
                                        <span class="d-inline-flex align-items-center">
                                            <i class="ti ti-device-{{ $device['device'] === 'mobile' ? 'mobile' : 'desktop' }} me-2 text-secondary"></i>
                                            {{ ucfirst($device['device']) }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <span class="badge bg-primary-lt">{{ number_format($device['sessions']) }}</span>
                                        <span class="text-muted small ms-1">
                                            {{ $deviceTotal > 0 ? number_format(($device['sessions'] / $deviceTotal) * 100, 1) : 0 }}%
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted py-5">No device data.</td>
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
            <div class="card dashboard-card h-100">
                <div class="card-header">
                    <h2 class="card-title mb-0">
                        <i class="ti ti-building-broadcast-tower text-primary me-2"></i>Location Performance
                    </h2>
                    <div class="card-actions">
                        @can('view-any-hotspot')
                        <a href="{{ route('admin.hotspots.index') }}" class="btn btn-sm btn-outline-secondary">View all</a>
                        @endcan
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table mb-0">
                        <thead>
                            <tr>
                                <th>Location</th>
                                <th class="text-end">Sessions</th>
                                <th class="text-end">Unique</th>
                                <th class="text-end">Hours</th>
                                <th class="text-end">Bandwidth</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($geo as $row)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.hotspots.show', $row['hotspot']) }}"
                                            class="text-body fw-bold text-decoration-none">{{ $row['hotspot']->name }}</a>
                                        @if($row['hotspot']->sub_county)
                                            <div class="small text-muted">{{ $row['hotspot']->sub_county }}</div>
                                        @endif
                                    </td>
                                    <td class="text-end"><span class="badge bg-primary-lt">{{ number_format($row['sessions']) }}</span></td>
                                    <td class="text-end text-muted">{{ number_format($row['unique_users']) }}</td>
                                    <td class="text-end text-muted">{{ number_format($row['internet_hours'], 1) }}</td>
                                    <td class="text-end text-muted">{{ number_format($row['bandwidth_mb']) }} MB</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-5">No location data for this period.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-6">
            <div class="card dashboard-card h-100">
                <div class="card-header">
                    <h2 class="card-title mb-0">
                        <i class="ti ti-speakerphone text-primary me-2"></i>Campaign Analytics
                    </h2>
                    <div class="card-actions">
                        @can('view-any-campaign')
                        <a href="{{ route('admin.campaigns.index') }}" class="btn btn-sm btn-outline-secondary">View all</a>
                        @endcan
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table mb-0">
                        <thead>
                            <tr>
                                <th>Campaign</th>
                                <th class="text-end">Plays</th>
                                <th class="text-end">Completed</th>
                                <th class="text-end">Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($campaigns['rows'] as $row)
                                <tr>
                                    <td>
                                        <span class="fw-bold text-body">{{ $row['campaign']->title }}</span>
                                        @if($row['sponsor'])
                                            <div class="small text-muted">{{ $row['sponsor'] }}</div>
                                        @endif
                                    </td>
                                    <td class="text-end">{{ number_format($row['plays']) }}</td>
                                    <td class="text-end text-muted">{{ number_format($row['completions']) }}</td>
                                    <td class="text-end">
                                        <span class="badge bg-{{ $row['completion_rate'] >= 90 ? 'success' : ($row['completion_rate'] > 0 ? 'warning' : 'secondary') }}-lt">
                                            {{ number_format($row['completion_rate'], 1) }}%
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-5">No campaign activity for this period.</td>
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
            font-size: 1.35rem;
            line-height: 1.15;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .stat-label {
            font-size: .75rem;
            text-transform: uppercase;
            letter-spacing: .04em;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const isDark = document.body.classList.contains('dashboard-dark');
            const tickColor = isDark ? '#8b98a9' : '#9aa7b0';
            const gridColor = isDark ? 'rgba(255, 255, 255, .08)' : 'rgba(17, 24, 39, .06)';
            const pointColor = isDark ? '#1c2735' : '#ffffff';

            const trendCtx = document.getElementById('chart-trend');
            if (trendCtx) {
                new Chart(trendCtx, {
                    type: 'line',
                    data: {
                        labels: @json($trend['labels']),
                        datasets: [{
                            label: 'Sessions',
                            data: @json($trend['sessions']),
                            borderColor: '#206bc4',
                            backgroundColor: 'rgba(32, 107, 196, .15)',
                            borderWidth: 2,
                            pointRadius: 3,
                            pointBackgroundColor: '#206bc4',
                            pointBorderColor: pointColor,
                            pointBorderWidth: 1,
                            fill: true,
                            tension: .35,
                        }, {
                            label: 'Video Plays',
                            data: @json($trend['plays']),
                            borderColor: '#d63939',
                            backgroundColor: 'rgba(214, 57, 57, .08)',
                            borderWidth: 2,
                            pointRadius: 2,
                            pointBackgroundColor: '#d63939',
                            pointBorderColor: pointColor,
                            pointBorderWidth: 1,
                            fill: false,
                            tension: .35,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                labels: { color: tickColor, boxWidth: 12 }
                            },
                            tooltip: {
                                backgroundColor: isDark ? 'rgba(255, 255, 255, .12)' : 'rgba(17, 24, 39, .9)',
                                padding: 10,
                                displayColors: false
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: {
                                    color: tickColor,
                                    maxTicksLimit: 10,
                                    font: { size: 11 }
                                }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    color: tickColor,
                                    precision: 0,
                                    font: { size: 11 }
                                },
                                grid: { color: gridColor }
                            }
                        }
                    }
                });
            }

            const bandwidthCtx = document.getElementById('chart-bandwidth');
            if (bandwidthCtx) {
                new Chart(bandwidthCtx, {
                    type: 'bar',
                    data: {
                        labels: @json($trend['labels']),
                        datasets: [{
                            label: 'Bandwidth (MB)',
                            data: @json($trend['bandwidth_mb']),
                            backgroundColor: 'rgba(0, 145, 154, .65)',
                            borderColor: '#00919a',
                            borderWidth: 1,
                            borderRadius: 3,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                labels: { color: tickColor, boxWidth: 12 }
                            },
                            tooltip: {
                                backgroundColor: isDark ? 'rgba(255, 255, 255, .12)' : 'rgba(17, 24, 39, .9)',
                                padding: 10,
                                displayColors: false
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: {
                                    color: tickColor,
                                    maxTicksLimit: 10,
                                    font: { size: 11 }
                                }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    color: tickColor,
                                    font: { size: 11 }
                                },
                                grid: { color: gridColor }
                            }
                        }
                    }
                });
            }
        });
    </script>
@endpush
