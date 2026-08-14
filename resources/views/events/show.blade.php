@extends('layouts.admin')

@section('title', "Event #{$event->id}")
@section('page-title', "Event #{$event->id}")

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                    <div>
                        <h1 class="h4 mb-1">Event #{{ $event->id }}</h1>
                        @php $evt = $event->eventType(); @endphp
                        <span class="badge bg-{{ in_array($evt, [\App\Enums\EventType::ErrorOccurred, \App\Enums\EventType::SessionFailed]) ? 'danger' : 'secondary' }}">{{ $evt->label() }}</span>
                    </div>
                    <div class="text-muted small">
                        {{ $event->occurred_at?->format('M d, Y H:i:s') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-xl-6 mb-4">
            <div class="card">
                <div class="card-header"><h2 class="h5 mb-0">Details</h2></div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-4 text-muted">Type</dt>
                        <dd class="col-8">{{ $evt->label() }} <span class="text-muted small">({{ $event->event_type }})</span></dd>
                        <dt class="col-4 text-muted">Organization</dt>
                        <dd class="col-8">{{ $event->organization->name ?? '—' }}</dd>
                        <dt class="col-4 text-muted">Hotspot</dt>
                        <dd class="col-8">@if($event->hotspot)<a href="{{ route('admin.hotspots.show', $event->hotspot->id) }}">{{ $event->hotspot->name }}</a> <span class="text-muted small">#{{ $event->hotspot->router_id ?? '' }}</span>@else — @endif</dd>
                        <dt class="col-4 text-muted">Campaign</dt>
                        <dd class="col-8">@if($event->campaign)<a href="{{ route('admin.campaigns.show', $event->campaign->id) }}">{{ $event->campaign->title }}</a>@else — @endif</dd>
                        <dt class="col-4 text-muted">Session</dt>
                        <dd class="col-8">@if($event->session)<a href="{{ route('admin.sessions.show', $event->session) }}">{{ $event->session->session_id }}</a> <span class="text-muted small">({{ $event->session->phone }})</span>@else {{ $event->session_id ?? '—' }}@endif</dd>
                        <dt class="col-4 text-muted">IP Address</dt>
                        <dd class="col-8">{{ $event->ip_address ?? '—' }}</dd>
                        <dt class="col-4 text-muted">User Agent</dt>
                        <dd class="col-8 text-break">{{ $event->user_agent ?? '—' }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-6 mb-4">
            <div class="card">
                <div class="card-header"><h2 class="h5 mb-0">Payload</h2></div>
                <div class="card-body p-0">
                    <pre class="p-3 mb-0 text-muted" style="max-height: 400px; overflow: auto;">{{ json_encode($event->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?: '—' }}</pre>
                </div>
            </div>
        </div>
    </div>
@endsection
