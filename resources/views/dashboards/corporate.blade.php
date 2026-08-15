@extends('layouts.admin')

@section('title', 'Advertiser Dashboard')
@section('page-title', 'Advertiser Dashboard')
@section('page-subtitle', $organization?->name ?? 'Campaign Overview')

@section('content')
    <div class="row row-cards">

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-primary-lt text-primary me-3">
                            <i class="ti ti-speakerphone"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="stat-label text-muted mb-1">Campaigns</div>
                            <div class="stat-value fw-bolder text-body">
                                {{ number_format($advertiser['total_campaigns']) }}
                                <span class="fs-6 fw-normal text-muted">/ {{ number_format($advertiser['active_campaigns']) }} active</span>
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
                            <i class="ti ti-eye"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="stat-label text-muted mb-1">Impressions</div>
                            <div class="stat-value fw-bolder text-body">
                                {{ number_format($advertiser['total_plays']) }}
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
                            <i class="ti ti-video"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="stat-label text-muted mb-1">Completed Views</div>
                            <div class="stat-value fw-bolder text-body">
                                {{ number_format($advertiser['total_completions']) }}
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
                            <i class="ti ti-percentage"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="stat-label text-muted mb-1">Completion Rate</div>
                            <div class="stat-value fw-bolder text-body">
                                {{ number_format($advertiser['overall_completion_rate'], 1) }}%
                            </div>
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
                        <i class="ti ti-chart-bar text-primary me-2"></i>Campaign Performance
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
                                <th class="text-end">Impressions</th>
                                <th class="text-end">Completed</th>
                                <th class="text-end">Completion</th>
                                <th class="text-end">Sessions</th>
                                <th class="text-end">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($advertiser['campaigns'] as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="avatar avatar-sm bg-primary-lt text-primary me-2">
                                                <i class="ti ti-speakerphone"></i>
                                            </span>
                                            <div>
                                                <a href="{{ route('admin.campaigns.show', $item['campaign']) }}"
                                                    class="text-body fw-bold text-decoration-none">{{ $item['campaign']->title }}</a>
                                                @if($item['campaign']->type)
                                                    <div class="small text-muted">{{ ucfirst($item['campaign']->type) }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <span class="badge bg-primary-lt">{{ number_format($item['plays']) }}</span>
                                    </td>
                                    <td class="text-end">
                                        <span class="badge bg-success-lt">{{ number_format($item['completions']) }}</span>
                                    </td>
                                    <td class="text-end">
                                        <span
                                            class="badge bg-{{ $item['completion_rate'] >= 90 ? 'success' : ($item['completion_rate'] >= 70 ? 'warning' : 'danger') }}-lt">
                                            {{ number_format($item['completion_rate'], 1) }}%
                                        </span>
                                    </td>
                                    <td class="text-end text-muted">{{ number_format($item['sessions']) }}</td>
                                    <td class="text-end">
                                        <span class="badge bg-{{ $item['is_active'] && $item['status'] === 'active' ? 'success' : 'secondary' }}-lt">
                                            {{ ucfirst($item['status']) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5">
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
    </div>

    <div class="row row-cards">
        <div class="col-12 col-xl-8">
            <div class="card dashboard-card h-100">
                <div class="card-header">
                    <h2 class="card-title mb-0">
                        <i class="ti ti-chart-line text-primary me-2"></i>Impressions &amp; Sessions (Last 14 Days)
                    </h2>
                </div>
                <div class="card-body">
                    <canvas id="chart-advertiser" style="height: 280px; width: 100%;"></canvas>
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
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('chart-advertiser');
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
                        label: 'Impressions',
                        data: @json($trends['plays']),
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
                        label: 'Sessions',
                        data: @json($trends['sessions']),
                        borderColor: '#2fb344',
                        backgroundColor: 'rgba(47, 179, 68, .08)',
                        borderWidth: 2,
                        pointRadius: 2,
                        pointBackgroundColor: '#2fb344',
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
