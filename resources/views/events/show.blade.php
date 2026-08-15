@extends('layouts.admin')

@section('title', "Event #{$event->id}")
@section('page-title', "Event #{$event->id}")

@section('content')
    <div class="mb-3">
        <a href="{{ route('admin.events.index') }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">
            <i class="fa-solid fa-arrow-left me-1"></i>Back to Events
        </a>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                    <div class="d-flex align-items-center">
                        <span class="avatar avatar-lg bg-primary-lt text-primary me-3">
                            <i class="fa-solid fa-calendar-days"></i>
                        </span>
                        <div>
                            <h1 class="h4 mb-1">Event #{{ $event->id }}</h1>
                            <div class="text-muted d-flex align-items-center flex-wrap">
                                @php $evt = $event->eventType(); @endphp
                                <span class="badge bg-{{ $evt === \App\Enums\EventType::ErrorOccurred ? 'danger' : 'secondary' }}-lt me-2">{{ $evt->label() }}</span>
                                @if($event->hotspot)
                                <span class="d-inline-flex align-items-center me-2"><i class="fa-solid fa-location-dot me-1 text-secondary"></i>{{ $event->hotspot->name }}</span>
                                @endif
                                <span class="d-inline-flex align-items-center"><i class="fa-solid fa-clock me-1 text-secondary"></i>{{ $event->occurred_at?->format('M d, Y H:i:s') }}</span>
                            </div>
                        </div>
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
                            <i class="fa-solid fa-tags"></i>
                        </div>
                        <div>
                            <div class="stat-label text-muted mb-1">Event Type</div>
                            <div class="stat-value fw-bolder text-body">{{ $evt->label() }}</div>
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
                            <i class="fa-solid fa-wifi"></i>
                        </div>
                        <div>
                            <div class="stat-label text-muted mb-1">Session</div>
                            <div class="stat-value fw-bolder text-body text-truncate" style="max-width: 100%;" title="{{ $event->session?->session_id ?? $event->session_id ?? '' }}">
                                @if($event->session)
                                <a href="{{ route('admin.sessions.show', $event->session) }}" class="text-body text-decoration-none">{{ $event->session->session_id }}</a>
                                @else{{ $event->session_id ?? '—' }}@endif
                            </div>
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
                            <i class="fa-solid fa-location-dot"></i>
                        </div>
                        <div>
                            <div class="stat-label text-muted mb-1">Hotspot</div>
                            <div class="stat-value fw-bolder text-body text-truncate" style="max-width: 100%;" title="{{ $event->hotspot->name ?? '' }}">
                                @if($event->hotspot)
                                <a href="{{ route('admin.hotspots.show', $event->hotspot->id) }}" class="text-body text-decoration-none">{{ $event->hotspot->name }}</a>
                                @else—@endif
                            </div>
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
                            <i class="fa-solid fa-bullhorn"></i>
                        </div>
                        <div>
                            <div class="stat-label text-muted mb-1">Campaign</div>
                            <div class="stat-value fw-bolder text-body text-truncate" style="max-width: 100%;" title="{{ $event->campaign->title ?? '' }}">
                                @if($event->campaign)
                                <a href="{{ route('admin.campaigns.show', $event->campaign->id) }}" class="text-body text-decoration-none">{{ $event->campaign->title }}</a>
                                @else—@endif
                            </div>
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
                        <dt class="col-5 text-muted">Type</dt>
                        <dd class="col-7">
                            <span class="d-inline-flex align-items-center">
                                <i class="fa-solid fa-tags me-1 text-secondary"></i>
                                {{ $evt->label() }}
                            </span>
                            <span class="text-muted small">({{ $event->event_type }})</span>
                        </dd>
                        <dt class="col-5 text-muted">Organization</dt>
                        <dd class="col-7">
                            <span class="d-inline-flex align-items-center">
                                <i class="fa-solid fa-building me-1 text-secondary"></i>
                                {{ $event->organization->name ?? '—' }}
                            </span>
                        </dd>
                        <dt class="col-5 text-muted">Hotspot</dt>
                        <dd class="col-7">
                            @if($event->hotspot)
                            <span class="d-inline-flex align-items-center">
                                <i class="fa-solid fa-location-dot me-1 text-secondary"></i>
                                <a href="{{ route('admin.hotspots.show', $event->hotspot->id) }}">{{ $event->hotspot->name }}</a>
                            </span>
                            <span class="text-muted small">#{{ $event->hotspot->router_id ?? '' }}</span>
                            @else — @endif
                        </dd>
                        <dt class="col-5 text-muted">Campaign</dt>
                        <dd class="col-7">
                            @if($event->campaign)
                            <span class="d-inline-flex align-items-center">
                                <i class="fa-solid fa-bullhorn me-1 text-secondary"></i>
                                <a href="{{ route('admin.campaigns.show', $event->campaign->id) }}">{{ $event->campaign->title }}</a>
                            </span>
                            @else — @endif
                        </dd>
                        <dt class="col-5 text-muted">Session</dt>
                        <dd class="col-7">
                            @if($event->session)
                            <span class="d-inline-flex align-items-center">
                                <i class="fa-solid fa-wifi me-1 text-secondary"></i>
                                <a href="{{ route('admin.sessions.show', $event->session) }}">{{ $event->session->session_id }}</a>
                            </span>
                            <span class="text-muted small">({{ $event->session->phone }})</span>
                            @else {{ $event->session_id ?? '—' }}@endif
                        </dd>
                        <dt class="col-5 text-muted">IP Address</dt>
                        <dd class="col-7">{{ $event->ip_address ?? '—' }}</dd>
                        <dt class="col-5 text-muted">User Agent</dt>
                        <dd class="col-7 text-break">{{ $event->user_agent ?? '—' }}</dd>
                        <dt class="col-5 text-muted">Occurred</dt>
                        <dd class="col-7">{{ $event->occurred_at?->format('M d, Y H:i:s') ?? '—' }}</dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-6">
            <div class="card dashboard-card h-100">
                <div class="card-header">
                    <h2 class="card-title mb-0">
                        <i class="fa-solid fa-file-code text-primary me-2"></i>Payload
                    </h2>
                </div>
                <div class="card-body p-0">
                    <pre class="p-3 mb-0 text-muted" style="max-height: 400px; overflow: auto;">{{ json_encode($event->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?: '—' }}</pre>
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
