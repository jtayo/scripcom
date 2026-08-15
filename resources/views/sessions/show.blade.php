@extends('layouts.admin')

@section('title', $session->session_id)
@section('page-title', $session->session_id)

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                    <div class="d-flex align-items-center">
                        <span class="avatar avatar-lg bg-primary-lt text-primary me-3">
                            <i class="fa-solid fa-mobile-screen"></i>
                        </span>
                        <div>
                            <h1 class="h4 mb-1">{{ $session->session_id }}</h1>
                            <div class="text-muted">
                                <span class="d-inline-flex align-items-center">
                                    <i class="fa-solid fa-mobile-screen-button me-1 text-secondary"></i>{{ $session->phone }}
                                </span>
                                @if($session->hotspot)
                                <span class="d-inline-flex align-items-center ms-3">
                                    <i class="fa-solid fa-wifi me-1 text-secondary"></i>{{ $session->hotspot->name }}
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mt-3 mt-md-0">
                        @php $color = $session->statusObject()->color(); @endphp
                        <span class="badge bg-{{ $color }}-lt me-2">
                            <span class="status-dot @if($session->status === 'active') status-dot-animated @endif bg-{{ $color }} me-1 d-inline-block"></span>
                            {{ $session->statusObject()->label() }}
                        </span>
                        @can('delete-session')
                        <form method="POST" action="{{ route('admin.sessions.destroy', $session) }}" class="d-inline" onsubmit="return confirm('{{ $session->status === 'active' ? 'Terminate this session?' : 'Delete this session?' }}');">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger d-inline-flex align-items-center">
                                <i class="fa-solid fa-{{ $session->status === 'active' ? 'ban' : 'trash' }} me-1"></i>{{ $session->status === 'active' ? 'Terminate' : 'Delete' }}
                            </button>
                        </form>
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
                        <div class="stat-icon bg-warning-lt text-warning me-3">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                        <div>
                            <div class="stat-label text-muted mb-1">Duration</div>
                            <div class="stat-value fw-bolder text-body">{{ gmdate('H:i:s', $session->total_duration ?? 0) }}</div>
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
                            <i class="fa-solid fa-database"></i>
                        </div>
                        <div>
                            <div class="stat-label text-muted mb-1">Data Used</div>
                            <div class="stat-value fw-bolder text-body">{{ number_format($session->bandwidthMb(), 2) }} <span class="fs-6 fw-normal text-muted">MB</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-primary-lt text-primary me-3">
                            <i class="fa-solid fa-calendar-days"></i>
                        </div>
                        <div>
                            <div class="stat-label text-muted mb-1">Started</div>
                            <div class="stat-value fw-bolder text-body fs-5">{{ $session->session_started_at?->format('M d, H:i') ?? '—' }}</div>
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
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>
                        <div>
                            <div class="stat-label text-muted mb-1">Auth Method</div>
                            <div class="stat-value fw-bolder text-body fs-5 text-capitalize">{{ $session->auth_method ?? '—' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cards">
        <div class="col-12 col-xl-6">
            <div class="card dashboard-card h-100">
                <div class="card-header">
                    <h2 class="card-title mb-0">
                        <i class="fa-solid fa-circle-info text-primary me-2"></i>Details
                    </h2>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-4 text-muted">Phone</dt>
                        <dd class="col-8">
                            <span class="d-inline-flex align-items-center">
                                <i class="fa-solid fa-mobile-screen me-1 text-secondary"></i>{{ $session->phone }}
                            </span>
                        </dd>
                        <dt class="col-4 text-muted">Status</dt>
                        <dd class="col-8">
                            <span class="badge bg-{{ $color }}-lt">
                                <span class="status-dot @if($session->status === 'active') status-dot-animated @endif bg-{{ $color }} me-1 d-inline-block"></span>
                                {{ $session->statusObject()->label() }}
                            </span>
                        </dd>
                        <dt class="col-4 text-muted">MAC Address</dt>
                        <dd class="col-8"><code>{{ $session->mac_address ?? '—' }}</code></dd>
                        <dt class="col-4 text-muted">IP Address</dt>
                        <dd class="col-8"><code>{{ $session->ip_address ?? '—' }}</code></dd>
                        <dt class="col-4 text-muted">Device</dt>
                        <dd class="col-8">{{ $session->device_type ?? '—' }}@if($session->browser) <span class="text-muted">({{ $session->browser }})</span>@endif</dd>
                        <dt class="col-4 text-muted">Auth Method</dt>
                        <dd class="col-8 text-capitalize">{{ $session->auth_method ?? '—' }}</dd>
                        <dt class="col-4 text-muted">Started</dt>
                        <dd class="col-8">{{ $session->session_started_at?->format('M d, Y H:i') ?? '—' }}</dd>
                        <dt class="col-4 text-muted">Ended</dt>
                        <dd class="col-8">{{ $session->ended_at?->format('M d, Y H:i') ?? '—' }}</dd>
                        <dt class="col-4 text-muted">End Reason</dt>
                        <dd class="col-8">{{ $session->end_reason ?? '—' }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-6">
            <div class="card dashboard-card h-100">
                <div class="card-header">
                    <h2 class="card-title mb-0">
                        <i class="fa-solid fa-link text-primary me-2"></i>Context
                    </h2>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-4 text-muted">Organization</dt>
                        <dd class="col-8">
                            <span class="d-inline-flex align-items-center">
                                <i class="fa-solid fa-building me-1 text-secondary"></i>
                                {{ $session->organization->name ?? '—' }}
                            </span>
                        </dd>
                        <dt class="col-4 text-muted">Hotspot</dt>
                        <dd class="col-8">
                            @if($session->hotspot)
                            <span class="d-inline-flex align-items-center">
                                <i class="fa-solid fa-wifi me-1 text-secondary"></i>
                                <a href="{{ route('admin.hotspots.show', $session->hotspot) }}" class="text-body text-decoration-none">{{ $session->hotspot->name }}</a>
                            </span>
                            <div class="small text-muted">#{{ $session->hotspot->router_id ?? '—' }}@if($session->hotspot->latitude) · {{ $session->hotspot->latitude }}, {{ $session->hotspot->longitude }}@endif</div>
                            @else — @endif
                        </dd>
                        <dt class="col-4 text-muted">Campaign</dt>
                        <dd class="col-8">
                            @if($session->campaign)
                            <a href="{{ route('admin.campaigns.show', $session->campaign) }}" class="text-body text-decoration-none">{{ $session->campaign->title }}</a>
                            <span class="text-muted small">({{ $session->campaign->type }})</span>
                            @else — @endif
                        </dd>
                        <dt class="col-4 text-muted">Sponsorship</dt>
                        <dd class="col-8">
                            @if($session->sponsorship)
                            <a href="{{ route('admin.sponsorships.show', $session->sponsorship) }}" class="text-body text-decoration-none">{{ $session->sponsorship->reference }}</a>
                            @else — @endif
                        </dd>
                        <dt class="col-4 text-muted">Bandwidth</dt>
                        <dd class="col-8">{{ number_format($session->bandwidth_up ?? 0, 2) }} / {{ number_format($session->bandwidth_down ?? 0, 2) }} Mbps</dd>
                        <dt class="col-4 text-muted">Video Watched</dt>
                        <dd class="col-8">{{ $session->video_completed ? 'Completed' : ($session->video_watch_duration ? number_format($session->video_watch_duration, 1) . 's' : 'N/A') }}</dd>
                    </dl>
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
