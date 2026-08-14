@extends('layouts.admin')

@section('title', $hotspot->name)
@section('page-title', $hotspot->name)

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                    <div>
                        <h1 class="h4 mb-1">{{ $hotspot->name }}</h1>
                        <span class="text-muted">Router #{{ $hotspot->router_id ?? '—' }}@if($hotspot->ssid) · {{ $hotspot->ssid }}@endif</span>
                    </div>
                    <div class="d-flex align-items-center">
                        @php $color = match($hotspot->status) { 'online' => 'success', 'offline' => 'danger', 'degraded' => 'warning', default => 'secondary' }; @endphp
                        <span class="badge bg-{{ $color }} me-2">{{ ucfirst($hotspot->status) }}</span>
                        @can('update-hotspot')
                        <a href="{{ route('admin.hotspots.edit', $hotspot) }}" class="btn btn-sm btn-primary d-inline-flex align-items-center">Edit</a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-md-3 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <h3 class="h6 text-muted mb-1">Total Sessions</h3>
                    <span class="fs-4 fw-bold">{{ number_format($stats['total_sessions']) }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <h3 class="h6 text-muted mb-1">Active Sessions</h3>
                    <span class="fs-4 fw-bold">{{ number_format($stats['active_sessions']) }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <h3 class="h6 text-muted mb-1">Bandwidth</h3>
                    <span class="fs-4 fw-bold">{{ number_format($stats['bandwidth_mb'], 1) }} MB</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <h3 class="h6 text-muted mb-1">Connect Time</h3>
                    <span class="fs-4 fw-bold">{{ number_format($stats['total_hours'], 1) }} hrs</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-xl-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h2 class="h5 mb-0">Details</h2>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-5 text-muted">Organization</dt>
                        <dd class="col-7">{{ $hotspot->organization->name ?? '—' }}</dd>
                        <dt class="col-5 text-muted">Device Model</dt>
                        <dd class="col-7">{{ $hotspot->device_model ?? '—' }}</dd>
                        <dt class="col-5 text-muted">Firmware</dt>
                        <dd class="col-7">{{ $hotspot->firmware_version ?? '—' }}</dd>
                        <dt class="col-5 text-muted">IP Address</dt>
                        <dd class="col-7">{{ $hotspot->ip_address ?? '—' }}</dd>
                        <dt class="col-5 text-muted">MAC Address</dt>
                        <dd class="col-7">{{ $hotspot->mac_address ?? '—' }}</dd>
                        <dt class="col-5 text-muted">ISP</dt>
                        <dd class="col-7">{{ $hotspot->isp ?? '—' }}</dd>
                        <dt class="col-5 text-muted">Bandwidth</dt>
                        <dd class="col-7">{{ $hotspot->bandwidth_up ?? 0 }} / {{ $hotspot->bandwidth_down ?? 0 }} Mbps</dd>
                        <dt class="col-5 text-muted">Location</dt>
                        <dd class="col-7">
                            {{ collect([$hotspot->ward, $hotspot->sub_county])->filter()->join(', ') ?: '—' }}
                            @if($hotspot->latitude && $hotspot->longitude)
                            <div class="small text-muted">{{ $hotspot->latitude }}, {{ $hotspot->longitude }}</div>
                            @endif
                        </dd>
                        <dt class="col-5 text-muted">Max Clients</dt>
                        <dd class="col-7">{{ $hotspot->max_clients ?? '—' }}</dd>
                        <dt class="col-5 text-muted">Last Seen</dt>
                        <dd class="col-7">{{ $hotspot->last_seen_at?->diffForHumans() ?? '—' }}</dd>
                        <dt class="col-5 text-muted">Active</dt>
                        <dd class="col-7">{{ $hotspot->is_active ? 'Yes' : 'No' }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h2 class="h5 mb-0">Campaigns</h2>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table mb-0">
                        <thead class="">
                            <tr>
                                <th class="border-bottom">Campaign</th>
                                <th class="border-bottom">Type</th>
                                <th class="border-bottom">Status</th>
                                <th class="border-bottom">Plays</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($hotspot->campaigns as $campaign)
                            <tr>
                                <td class="text-body">{{ $campaign->title }}</td>
                                <td>{{ ucfirst($campaign->type) }}</td>
                                <td><span class="badge bg-{{ $campaign->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($campaign->status) }}</span></td>
                                <td>{{ number_format($campaign->current_plays) }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">No campaigns assigned.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2 class="h5 mb-0">Recent Sessions</h2>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table mb-0">
                        <thead class="">
                            <tr>
                                <th class="border-bottom">Phone</th>
                                <th class="border-bottom">Device</th>
                                <th class="border-bottom">Duration</th>
                                <th class="border-bottom">Started</th>
                                <th class="border-bottom">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($hotspot->sessions as $session)
                            <tr>
                                <td class="text-body">{{ $session->phone }}</td>
                                <td>{{ $session->device_type ?? '—' }}</td>
                                <td>{{ gmdate('H:i:s', $session->total_duration ?? 0) }}</td>
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
    </div>
@endsection
