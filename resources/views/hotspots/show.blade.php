@extends('layouts.admin')

@section('title', $hotspot->name)
@section('page-title', $hotspot->name)

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                    <div class="d-flex align-items-center">
                        @php $color = match($hotspot->status) { 'online' => 'success', 'offline' => 'danger', 'degraded' => 'warning', default => 'secondary' }; @endphp
                        <span class="avatar avatar-lg bg-{{ $color }}-lt text-{{ $color }} me-3">
                            <i class="fa-solid fa-wifi"></i>
                        </span>
                        <div>
                            <h1 class="h4 mb-1">{{ $hotspot->name }}</h1>
                            <div class="text-muted">
                                <span class="d-inline-flex align-items-center">
                                    <i class="fa-solid fa-server me-1 text-secondary"></i>Router #{{ $hotspot->router_id ?? '—' }}
                                </span>
                                @if($hotspot->ssid)
                                <span class="d-inline-flex align-items-center ms-3">
                                    <i class="fa-solid fa-signal me-1 text-secondary"></i>{{ $hotspot->ssid }}
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mt-3 mt-md-0">
                        <span class="badge bg-{{ $color }}-lt me-2">
                            <span class="status-dot @if($hotspot->status === 'online') status-dot-animated @endif bg-{{ $color }} me-1 d-inline-block"></span>
                            {{ ucfirst($hotspot->status) }}
                        </span>
                        @can('update-hotspot')
                        <a href="{{ route('admin.hotspots.edit', $hotspot) }}" class="btn btn-sm btn-primary d-inline-flex align-items-center">
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
                            <i class="fa-solid fa-users"></i>
                        </div>
                        <div>
                            <div class="stat-label text-muted mb-1">Total Sessions</div>
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
                            <i class="fa-solid fa-user-check"></i>
                        </div>
                        <div>
                            <div class="stat-label text-muted mb-1">Active Sessions</div>
                            <div class="stat-value fw-bolder text-body">{{ number_format($stats['active_sessions']) }}</div>
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
                            <div class="stat-label text-muted mb-1">Bandwidth</div>
                            <div class="stat-value fw-bolder text-body">{{ number_format($stats['bandwidth_mb'], 1) }} <span class="fs-6 fw-normal text-muted">MB</span></div>
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
                            <div class="stat-label text-muted mb-1">Connect Time</div>
                            <div class="stat-value fw-bolder text-body">{{ number_format($stats['total_hours'], 1) }} <span class="fs-6 fw-normal text-muted">hrs</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cards">
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
                                {{ $hotspot->organization->name ?? '—' }}
                            </span>
                        </dd>
                        <dt class="col-5 text-muted">Device Model</dt>
                        <dd class="col-7">{{ $hotspot->device_model ?? '—' }}</dd>
                        <dt class="col-5 text-muted">Firmware</dt>
                        <dd class="col-7">{{ $hotspot->firmware_version ?? '—' }}</dd>
                        <dt class="col-5 text-muted">IP Address</dt>
                        <dd class="col-7"><code>{{ $hotspot->ip_address ?? '—' }}</code></dd>
                        <dt class="col-5 text-muted">MAC Address</dt>
                        <dd class="col-7"><code>{{ $hotspot->mac_address ?? '—' }}</code></dd>
                        <dt class="col-5 text-muted">ISP</dt>
                        <dd class="col-7">{{ $hotspot->isp ?? '—' }}</dd>
                        <dt class="col-5 text-muted">Bandwidth</dt>
                        <dd class="col-7">{{ $hotspot->bandwidth_up ?? 0 }} / {{ $hotspot->bandwidth_down ?? 0 }} Mbps</dd>
                        <dt class="col-5 text-muted">Location</dt>
                        <dd class="col-7">
                            <span class="d-inline-flex align-items-center">
                                <i class="fa-solid fa-map-pin me-1 text-secondary"></i>
                                {{ collect([$hotspot->ward, $hotspot->sub_county])->filter()->join(', ') ?: '—' }}
                            </span>
                            @if($hotspot->latitude && $hotspot->longitude)
                            <div class="small text-muted">{{ $hotspot->latitude }}, {{ $hotspot->longitude }}</div>
                            @endif
                        </dd>
                        <dt class="col-5 text-muted">Max Clients</dt>
                        <dd class="col-7">{{ $hotspot->max_clients ?? '—' }}</dd>
                        <dt class="col-5 text-muted">Last Seen</dt>
                        <dd class="col-7">
                            <span class="d-inline-flex align-items-center">
                                <i class="fa-solid fa-clock me-1 text-secondary"></i>
                                {{ $hotspot->last_seen_at?->diffForHumans() ?? '—' }}
                            </span>
                        </dd>
                        <dt class="col-5 text-muted">Active</dt>
                        <dd class="col-7">
                            <span class="badge bg-{{ $hotspot->is_active ? 'success' : 'danger' }}-lt">{{ $hotspot->is_active ? 'Yes' : 'No' }}</span>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-8">
            <div class="card dashboard-card mb-4">
                <div class="card-header">
                    <h2 class="card-title mb-0">
                        <i class="fa-solid fa-bullhorn text-primary me-2"></i>Campaigns
                    </h2>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table mb-0">
                        <thead>
                            <tr>
                                <th>Campaign</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th class="text-end">Plays</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($hotspot->campaigns as $campaign)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="avatar avatar-sm bg-primary-lt text-primary me-2">
                                            <i class="fa-solid fa-bullhorn"></i>
                                        </span>
                                        <a href="{{ route('admin.campaigns.show', $campaign) }}" class="text-body fw-bold text-decoration-none">{{ $campaign->title }}</a>
                                    </div>
                                </td>
                                <td><span class="badge bg-secondary-lt">{{ ucfirst($campaign->type) }}</span></td>
                                <td>
                                    <span class="badge bg-{{ $campaign->status === 'active' ? 'success' : ($campaign->status === 'paused' ? 'warning' : 'secondary') }}-lt">{{ ucfirst($campaign->status) }}</span>
                                </td>
                                <td class="text-end">
                                    <span class="d-inline-flex align-items-center text-muted">
                                        <i class="fa-solid fa-play me-1 text-secondary"></i>
                                        {{ number_format($campaign->current_plays) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-5">
                                    <i class="fa-solid fa-bullhorn text-secondary mb-1 d-block" style="font-size: 2rem;"></i>
                                    No campaigns assigned.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card dashboard-card">
                <div class="card-header">
                    <h2 class="card-title mb-0">
                        <i class="fa-solid fa-mobile-screen text-primary me-2"></i>Recent Sessions
                    </h2>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table mb-0">
                        <thead>
                            <tr>
                                <th>Phone</th>
                                <th>Device</th>
                                <th>Duration</th>
                                <th>Started</th>
                                <th class="text-end">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($hotspot->sessions as $session)
                            <tr>
                                <td class="text-body fw-bold">{{ $session->phone }}</td>
                                <td>
                                    <span class="d-inline-flex align-items-center text-muted">
                                        <i class="fa-solid fa-mobile-screen me-1 text-secondary"></i>
                                        {{ $session->device_type ?? '—' }}
                                    </span>
                                </td>
                                <td class="text-muted">{{ gmdate('H:i:s', $session->total_duration ?? 0) }}</td>
                                <td class="text-muted">{{ $session->session_started_at?->format('M d, H:i') }}</td>
                                <td class="text-end">
                                    <span class="badge bg-{{ $session->status === 'active' ? 'success' : 'secondary' }}-lt">{{ ucfirst($session->status) }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-5">
                                    <i class="fa-solid fa-mobile-screen text-secondary mb-1 d-block" style="font-size: 2rem;"></i>
                                    No sessions yet.
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
