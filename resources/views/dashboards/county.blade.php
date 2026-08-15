@extends('layouts.admin')

@section('title', 'County Dashboard')
@section('page-title', 'Digital Connectivity Dashboard')
@section('page-subtitle', $organization?->name ?? 'County Overview')

@php
    $rate = $county['completion_rate'];
    $rateUp = $rate >= 90;
@endphp

@section('content')
    <div class="row row-cards">

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-primary-lt text-primary me-3">
                            <i class="ti ti-device-mobile"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="stat-label text-muted mb-1">Sponsored Sessions</div>
                            <div class="stat-value fw-bolder text-body">
                                {{ number_format($county['sponsored_sessions']) }}
                            </div>
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
                            <div class="stat-label text-muted mb-1">Citizens Reached</div>
                            <div class="stat-value fw-bolder text-body">
                                {{ number_format($county['unique_users']) }}
                            </div>
                            <div class="mt-1 text-muted small">
                                {{ number_format($county['repeat_users']) }} repeat users
                            </div>
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
                            <div class="stat-value fw-bolder text-body">
                                {{ number_format($county['internet_hours'], 1) }}
                            </div>
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
                            <i class="ti ti-wifi"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="stat-label text-muted mb-1">Active Locations</div>
                            <div class="stat-value fw-bolder text-body">
                                {{ number_format($county['active_locations']) }}
                                <span class="fs-6 fw-normal text-muted">/ {{ number_format($county['total_locations']) }}</span>
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
                        <div class="stat-icon bg-danger-lt text-danger me-3">
                            <i class="ti ti-player-play"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="stat-label text-muted mb-1">Video Plays</div>
                            <div class="stat-value fw-bolder text-body">
                                {{ number_format($county['video_plays']) }}
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
                            <i class="ti ti-video"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="stat-label text-muted mb-1">Completion Rate</div>
                            <div class="stat-value fw-bolder text-body">
                                {{ number_format($rate, 1) }}%
                                <span class="badge bg-{{ $rateUp ? 'success' : 'warning' }}-lt ms-1">
                                    <i class="ti ti-{{ $rateUp ? 'check' : 'alert-triangle' }} me-1"></i>
                                    {{ $rateUp ? 'Healthy' : 'Watch' }}
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
                        <div class="stat-icon bg-cyan-lt text-cyan me-3">
                            <i class="ti ti-building-community"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="stat-label text-muted mb-1">Average Frequency</div>
                            <div class="stat-value fw-bolder text-body">
                                {{ $county['unique_users'] > 0 ? number_format($county['sponsored_sessions'] / $county['unique_users'], 2) : '0.0' }}
                                <span class="fs-6 fw-normal text-muted">sessions/user</span>
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
                        <i class="ti ti-chart-line text-primary me-2"></i>Connections &amp; Plays (Last 14 Days)
                    </h2>
                </div>
                <div class="card-body">
                    <canvas id="chart-county" style="height: 280px; width: 100%;"></canvas>
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
        <div class="col-12">
            <div class="card dashboard-card">
                <div class="card-header">
                    <h2 class="card-title mb-0">
                        <i class="ti ti-building-broadcast-tower text-primary me-2"></i>Location Performance
                    </h2>
                    <div class="card-actions">
                        <a href="{{ route('admin.hotspots.index') }}" class="btn btn-sm btn-outline-secondary">View all</a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table mb-0">
                        <thead>
                            <tr>
                                <th>Location</th>
                                <th class="text-end">Sessions</th>
                                <th class="text-end">Internet Hours</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($locations as $location)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="avatar avatar-sm bg-success-lt text-success me-2">
                                                <i class="ti ti-wifi"></i>
                                            </span>
                                            <div>
                                                <a href="{{ route('admin.hotspots.show', $location) }}"
                                                    class="text-body fw-bold text-decoration-none">{{ $location->name }}</a>
                                                @if($location->sub_county)
                                                    <div class="small text-muted">{{ $location->sub_county }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <span class="badge bg-primary-lt">{{ number_format($location->sessions_count) }}</span>
                                    </td>
                                    <td class="text-end text-muted">
                                        {{ number_format(($location->total_duration ?? 0) / 3600, 1) }} hrs
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-5">No location data yet.</td>
                                </tr>
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
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('chart-county');
            if (!ctx) return;
            const isDark = document.body.classList.contains('dashboard-dark');
            const tickColor = isDark ? '#8b98a9' : '#9aa7b0';
            const gridColor = isDark ? 'rgba(255, 255, 255, .08)' : 'rgba(17, 24, 39, .06)';
            const pointColor = isDark ? '#1c2735' : '#ffffff';

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($trends['labels']),
                    datasets: [{
                        label: 'Connections',
                        data: @json($trends['sessions']),
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
                        data: @json($trends['plays']),
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
                            labels: {
                                color: tickColor,
                                boxWidth: 12
                            }
                        },
                        tooltip: {
                            backgroundColor: isDark ? 'rgba(255, 255, 255, .12)' : 'rgba(17, 24, 39, .9)',
                            padding: 10,
                            displayColors: false
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
@endpush
