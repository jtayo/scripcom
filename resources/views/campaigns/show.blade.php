@extends('layouts.admin')

@section('title', $campaign->title)
@section('page-title', $campaign->title)

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                    <div class="d-flex align-items-center">
                        <span class="avatar avatar-lg bg-primary-lt text-primary me-3">
                            <i class="fa-solid fa-bullhorn"></i>
                        </span>
                        <div>
                            <h1 class="h4 mb-1">{{ $campaign->title }}</h1>
                            <div class="text-muted d-flex align-items-center flex-wrap">
                                <span class="badge bg-secondary-lt me-2">{{ ucfirst($campaign->type) }}</span>
                                @if($campaign->sponsor)
                                    <span class="d-inline-flex align-items-center"><i class="fa-solid fa-handshake me-1 text-secondary"></i>Sponsored by <strong class="ms-1">{{ $campaign->sponsor->name }}</strong></span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mt-3 mt-md-0">
                        <span class="badge bg-{{ $campaign->status === 'active' ? 'success' : ($campaign->status === 'paused' ? 'warning' : 'secondary') }}-lt me-2">{{ ucfirst($campaign->status) }}</span>
                        @can('update-campaign')
                        <a href="{{ route('admin.campaigns.edit', $campaign) }}" class="btn btn-sm btn-primary d-inline-flex align-items-center">
                            <i class="fa-solid fa-pen me-1"></i>Edit
                        </a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cards">
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-primary-lt text-primary me-3">
                            <i class="fa-solid fa-play"></i>
                        </div>
                        <div>
                            <div class="stat-label text-muted mb-1">Total Plays</div>
                            <div class="stat-value fw-bolder text-body">{{ number_format($stats['total_plays']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-info-lt text-info me-3">
                            <i class="fa-solid fa-users"></i>
                        </div>
                        <div>
                            <div class="stat-label text-muted mb-1">Sessions</div>
                            <div class="stat-value fw-bolder text-body">{{ number_format($stats['total_sessions']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-success-lt text-success me-3">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                        <div>
                            <div class="stat-label text-muted mb-1">Completions</div>
                            <div class="stat-value fw-bolder text-body">{{ number_format($stats['completions']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-warning-lt text-warning me-3">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                        <div>
                            <div class="stat-label text-muted mb-1">Avg Watch (s)</div>
                            <div class="stat-value fw-bolder text-body">{{ number_format($stats['avg_watch'], 1) }}</div>
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
                        <i class="fa-solid fa-chart-line text-primary me-2"></i>Plays (14 days)
                    </h2>
                </div>
                <div class="card-body">
                    <canvas id="chart-daily" style="height: 250px; width: 100%;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-4">
            <div class="card dashboard-card h-100">
                <div class="card-header">
                    <h2 class="card-title mb-0">
                        <i class="fa-solid fa-circle-info text-primary me-2"></i>Details
                    </h2>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-5 text-muted">Organization</dt>
                        <dd class="col-7">
                            <span class="d-inline-flex align-items-center">
                                <i class="fa-solid fa-building me-1 text-secondary"></i>
                                {{ $campaign->organization->name ?? '—' }}
                            </span>
                        </dd>
                        <dt class="col-5 text-muted">Content</dt>
                        <dd class="col-7 text-truncate">
                            <span class="d-inline-flex align-items-center">
                                <i class="fa-solid fa-photo-film me-1 text-secondary"></i>
                                {{ ucfirst($campaign->content_type) }}
                                @if($campaign->content_url) · <a href="{{ $campaign->content_url }}" target="_blank" rel="noopener">view</a>@endif
                            </span>
                        </dd>
                        <dt class="col-5 text-muted">Duration</dt>
                        <dd class="col-7">{{ $campaign->duration_seconds }}s</dd>
                        <dt class="col-5 text-muted">Priority</dt>
                        <dd class="col-7">{{ $campaign->priority ?? 0 }}</dd>
                        <dt class="col-5 text-muted">Plays</dt>
                        <dd class="col-7">{{ number_format($campaign->current_plays) }}@if($campaign->max_plays) / {{ number_format($campaign->max_plays) }}@endif</dd>
                        <dt class="col-5 text-muted">Created By</dt>
                        <dd class="col-7">{{ $campaign->creator->name ?? '—' }}</dd>
                        <dt class="col-5 text-muted">Starts</dt>
                        <dd class="col-7">{{ $campaign->starts_at?->format('M d, Y H:i') ?? '—' }}</dd>
                        <dt class="col-5 text-muted">Ends</dt>
                        <dd class="col-7">{{ $campaign->ends_at?->format('M d, Y H:i') ?? '—' }}</dd>
                        <dt class="col-5 text-muted">Skip / Mandatory</dt>
                        <dd class="col-7">
                            <span class="badge bg-{{ $campaign->skip_allowed ? 'warning' : 'secondary' }}-lt">Skip {{ $campaign->skip_allowed ? 'Yes' : 'No' }}</span>
                            <span class="badge bg-{{ $campaign->is_mandatory ? 'primary' : 'secondary' }}-lt">Mandatory {{ $campaign->is_mandatory ? 'Yes' : 'No' }}</span>
                        </dd>
                        @if($campaign->redirect_url)
                        <dt class="col-5 text-muted">Redirect</dt>
                        <dd class="col-7 text-truncate"><a href="{{ $campaign->redirect_url }}" target="_blank" rel="noopener">{{ $campaign->redirect_url }}</a></dd>
                        @endif
                    </dl>
                </div>
            </div>

            <div class="card dashboard-card mt-4">
                <div class="card-header">
                    <h2 class="card-title mb-0">
                        <i class="fa-solid fa-wifi text-primary me-2"></i>Targeted Hotspots
                    </h2>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table mb-0">
                        <tbody>
                            @forelse($campaign->hotspots as $hotspot)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="avatar avatar-sm bg-{{ $hotspot->status === 'online' ? 'success' : 'secondary' }}-lt text-{{ $hotspot->status === 'online' ? 'success' : 'secondary' }} me-2">
                                            <i class="fa-solid fa-wifi"></i>
                                        </span>
                                        <div>
                                            <span class="fw-bold text-body">{{ $hotspot->name }}</span>
                                            <span class="d-block small text-muted">#{{ $hotspot->router_id ?? '—' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <span class="badge bg-{{ $hotspot->status === 'online' ? 'success' : 'secondary' }}-lt">{{ ucfirst($hotspot->status) }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td class="text-center text-muted py-4">
                                    <i class="fa-solid fa-globe text-secondary mb-1 d-block" style="font-size: 1.5rem;"></i>
                                    All hotspots
                                </td>
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
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('chart-daily').getContext('2d');
    const isDark = document.body.classList.contains('dashboard-dark');
    const tickColor = isDark ? '#8b98a9' : '#9aa7b0';
    const gridColor = isDark ? 'rgba(255, 255, 255, .08)' : 'rgba(17, 24, 39, .06)';
    const pointColor = isDark ? '#1c2735' : '#ffffff';
    const gradient = ctx.createLinearGradient(0, 0, 0, 250);
    gradient.addColorStop(0, 'rgba(32, 107, 196, .3)');
    gradient.addColorStop(1, 'rgba(32, 107, 196, 0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($daily->keys()->map(fn ($d) => \Illuminate\Support\Carbon::parse($d)->format('d M'))),
            datasets: [{
                label: 'Plays',
                data: @json($daily->values()),
                borderColor: '#206bc4',
                backgroundColor: gradient,
                borderWidth: 2,
                pointRadius: 0,
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
                legend: { display: false },
                tooltip: {
                    backgroundColor: isDark ? 'rgba(255, 255, 255, .12)' : 'rgba(17, 24, 39, .9)',
                    padding: 10,
                    displayColors: false,
                    callbacks: { title: items => items[0].label, label: item => ' ' + item.parsed.y + ' plays' }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: tickColor, maxTicksLimit: 7, font: { size: 11 } }
                },
                y: {
                    beginAtZero: true,
                    ticks: { color: tickColor, precision: 0, font: { size: 11 } },
                    grid: { color: gridColor }
                }
            }
        }
    });
});
</script>
@endpush
