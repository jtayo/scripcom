@extends('layouts.admin')

@section('title', $session->session_id)
@section('page-title', $session->session_id)

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                    <div>
                        <h1 class="h4 mb-1">{{ $session->session_id }}</h1>
                        <div class="text-muted">
                            {{ $session->phone }}@if($session->hotspot) · {{ $session->hotspot->name }}@endif
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-{{ $session->statusObject()->color() }} me-2">{{ $session->statusObject()->label() }}</span>
                        @can('delete-session')
                        <form method="POST" action="{{ route('admin.sessions.destroy', $session) }}" class="d-inline" onsubmit="return confirm('{{ $session->status === 'active' ? 'Terminate this session?' : 'Delete this session?' }}');">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger d-inline-flex align-items-center">{{ $session->status === 'active' ? 'Terminate' : 'Delete' }}</button>
                        </form>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-md-6 col-xl-3 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <h3 class="h6 text-muted mb-1">Duration</h3>
                    <span class="fs-4 fw-bold">{{ gmdate('H:i:s', $session->total_duration ?? 0) }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <h3 class="h6 text-muted mb-1">Bandwidth</h3>
                    <span class="fs-4 fw-bold">{{ number_format($session->bandwidthMb(), 2) }} MB</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <h3 class="h6 text-muted mb-1">Started</h3>
                    <span class="fs-5 fw-bold">{{ $session->session_started_at?->format('M d, H:i') }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <h3 class="h6 text-muted mb-1">Auth Method</h3>
                    <span class="fs-5 fw-bold text-capitalize">{{ $session->auth_method ?? '—' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-xl-6 mb-4">
            <div class="card">
                <div class="card-header"><h2 class="h5 mb-0">Client</h2></div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-4 text-muted">Phone</dt>
                        <dd class="col-8">{{ $session->phone }}</dd>
                        <dt class="col-4 text-muted">MAC Address</dt>
                        <dd class="col-8">{{ $session->mac_address ?? '—' }}</dd>
                        <dt class="col-4 text-muted">Device</dt>
                        <dd class="col-8">{{ $session->device_type ?? '—' }} @if($session->browser)({{ $session->browser }})@endif</dd>
                        <dt class="col-4 text-muted">IP Address</dt>
                        <dd class="col-8">{{ $session->ip_address ?? '—' }}</dd>
                        <dt class="col-4 text-muted">Bandwidth</dt>
                        <dd class="col-8">{{ number_format($session->bandwidth_up ?? 0, 2) }} / {{ number_format($session->bandwidth_down ?? 0, 2) }} Mbps</dd>
                        <dt class="col-4 text-muted">Video Watched</dt>
                        <dd class="col-8">{{ $session->video_completed ? 'Completed' : ($session->video_watch_duration ? number_format($session->video_watch_duration, 1) . 's' : 'N/A') }}</dd>
                        <dt class="col-4 text-muted">Ended</dt>
                        <dd class="col-8">{{ $session->ended_at?->format('M d, Y H:i') ?? '—' }}</dd>
                        <dt class="col-4 text-muted">End Reason</dt>
                        <dd class="col-8">{{ $session->end_reason ?? '—' }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-6">
            <div class="card">
                <div class="card-header"><h2 class="h5 mb-0">Context</h2></div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-4 text-muted">Organization</dt>
                        <dd class="col-8">{{ $session->organization->name ?? '—' }}</dd>
                        <dt class="col-4 text-muted">Hotspot</dt>
                        <dd class="col-8">
                            @if($session->hotspot)
                                <a href="{{ route('admin.hotspots.show', $session->hotspot->id) }}">{{ $session->hotspot->name }}</a>
                                <div class="small text-muted">#{{ $session->hotspot->router_id ?? '—' }} @if($session->hotspot->latitude){{ $session->hotspot->latitude }}, {{ $session->hotspot->longitude }}@endif</div>
                            @else — @endif
                        </dd>
                        <dt class="col-4 text-muted">Campaign</dt>
                        <dd class="col-8">@if($session->campaign)<a href="{{ route('admin.campaigns.show', $session->campaign->id) }}">{{ $session->campaign->title }}</a> <span class="text-muted small">({{ $session->campaign->type }})</span>@else — @endif</dd>
                        <dt class="col-4 text-muted">Sponsorship</dt>
                        <dd class="col-8">@if($session->sponsorship)<a href="{{ route('admin.sponsorships.show', $session->sponsorship->id) }}">{{ $session->sponsorship->reference }}</a>@else — @endif</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
@endsection
