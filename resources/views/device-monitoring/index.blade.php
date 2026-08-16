@extends('layouts.admin')

@section('title', 'Device Monitoring')
@section('page-title', 'Device Monitoring')
@section('page-subtitle', 'Real-time router and hotspot health overview')

@section('content')
    <div class="row row-cards mb-3">
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <div class="text-muted small">Routers</div>
                            <div class="h3 mb-0">{{ $overview['total_routers'] }}</div>
                        </div>
                        <i class="fa-solid fa-device-router ms-auto text-secondary"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <div class="text-muted small">Online</div>
                            <div class="h3 mb-0 text-success">{{ $overview['online_routers'] }}</div>
                        </div>
                        <i class="fa-solid fa-circle-check ms-auto text-success"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <div class="text-muted small">Degraded</div>
                            <div class="h3 mb-0 text-warning">{{ $overview['degraded_routers'] }}</div>
                        </div>
                        <i class="fa-solid fa-triangle-exclamation ms-auto text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <div class="text-muted small">Offline</div>
                            <div class="h3 mb-0 text-danger">{{ $overview['offline_routers'] }}</div>
                        </div>
                        <i class="fa-solid fa-circle-xmark ms-auto text-danger"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <div class="text-muted small">Hotspots</div>
                            <div class="h3 mb-0">{{ $overview['total_hotspots'] }}</div>
                        </div>
                        <i class="fa-solid fa-wifi ms-auto text-secondary"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <div class="text-muted small">Hotspots Offline</div>
                            <div class="h3 mb-0 text-danger">{{ $overview['offline_hotspots'] }}</div>
                        </div>
                        <i class="fa-solid fa-wifi ms-auto text-danger"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cards">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Router Fleet</div>
                    <div class="card-actions">
                        @can('create-router')
                            <a href="{{ route('admin.routers.create') }}" class="btn btn-primary btn-sm d-inline-flex align-items-center">
                                <i class="ti ti-plus me-1"></i>New Router
                            </a>
                        @endcan
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter table-nowrap card-table mb-0">
                        <thead>
                            <tr>
                                <th>Router</th>
                                <th>Hotspot</th>
                                <th>Status</th>
                                <th class="text-end">CPU</th>
                                <th class="text-end">Memory</th>
                                <th class="text-end">Latency</th>
                                <th class="text-end">Active Users</th>
                                <th>Last Check</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($routers as $router)
                                @php $health = $router->latestHealth(); @endphp
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="avatar avatar-sm bg-{{ $router->statusColor() }}-lt text-{{ $router->statusColor() }} me-2">
                                                <i class="ti ti-device-router"></i>
                                            </span>
                                            <div>
                                                <a href="{{ route('admin.routers.show', $router) }}" class="text-body fw-bold text-decoration-none">{{ $router->name }}</a>
                                                <div class="small text-muted">{{ $router->organization?->name ?? '—' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-muted">{{ $router->hotspot?->name ?? '—' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $router->statusColor() }}-lt">
                                            <span class="status-dot @if($router->status === 'online') status-dot-animated @endif bg-{{ $router->statusColor() }} me-1 d-inline-block"></span>
                                            {{ ucfirst($router->status) }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        @if($health?->cpu_usage !== null)
                                            <div class="d-flex align-items-center justify-content-end gap-2">
                                                <div class="progress progress-sm" style="width: 60px;">
                                                    <div class="progress-bar {{ $health->cpu_usage > 90 ? 'bg-danger' : ($health->cpu_usage > 70 ? 'bg-warning' : 'bg-success') }}" style="width: {{ min($health->cpu_usage, 100) }}%"></div>
                                                </div>
                                                {{ $health->cpu_usage }}%
                                            </div>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if($health?->memory_usage !== null)
                                            <div class="d-flex align-items-center justify-content-end gap-2">
                                                <div class="progress progress-sm" style="width: 60px;">
                                                    <div class="progress-bar {{ $health->memory_usage > 90 ? 'bg-danger' : ($health->memory_usage > 70 ? 'bg-warning' : 'bg-success') }}" style="width: {{ min($health->memory_usage, 100) }}%"></div>
                                                </div>
                                                {{ $health->memory_usage }}%
                                            </div>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="text-end">{{ $health?->latency_ms !== null ? $health->latency_ms . ' ms' : '—' }}</td>
                                    <td class="text-end">{{ $health?->active_users ?? '—' }}</td>
                                    <td class="text-muted">{{ $health?->recorded_at?->diffForHumans() ?? '—' }}</td>
                                    <td class="text-end">
                                        <div class="d-inline-flex gap-1">
                                            @can('update-router')
                                                <form method="POST" action="{{ route('admin.device-monitoring.check', $router) }}">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center" title="Run health check">
                                                        <i class="ti ti-rotate me-1"></i>Check
                                                    </button>
                                                </form>
                                            @endcan
                                            <a href="{{ route('admin.routers.show', $router) }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center" title="View">
                                                <i class="ti ti-eye me-1"></i>View
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-5">
                                        <div class="my-4">
                                            <i class="ti ti-device-router text-secondary" style="font-size: 2.5rem;"></i>
                                            <div class="mt-2">No routers registered yet.</div>
                                            @can('create-router')
                                                <a href="{{ route('admin.routers.create') }}" class="btn btn-primary btn-sm mt-2">Register a router</a>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @if($recentNotifications->isNotEmpty())
            <div class="col-12 col-xl-4">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Recent Alerts</div>
                        <div class="card-actions">
                            <a href="{{ route('admin.notifications.index') }}" class="btn btn-sm btn-link">View all</a>
                        </div>
                    </div>
                    <div class="list-group list-group-flush">
                        @foreach($recentNotifications as $notification)
                            @php
                                $data = $notification->data;
                                $levelColors = [
                                    'danger' => 'text-danger bg-danger-lt',
                                    'warning' => 'text-warning bg-warning-lt',
                                    'success' => 'text-success bg-success-lt',
                                    'info' => 'text-primary bg-primary-lt',
                                ];
                                $level = $data['level'] ?? 'info';
                            @endphp
                            <a href="{{ route('admin.notifications.show', $notification) }}" class="list-group-item list-group-item-action d-flex">
                                <span class="avatar avatar-sm rounded {{ $levelColors[$level] ?? $levelColors['info'] }} me-3">
                                    <i class="ti ti-{{ $data['icon'] ?? 'bell' }}"></i>
                                </span>
                                <div class="flex-fill">
                                    <div class="fw-semibold">{{ $data['title'] ?? 'Notification' }}</div>
                                    <div class="small text-secondary">{{ $data['message'] ?? '' }}</div>
                                    <div class="small text-secondary mt-1">{{ $notification->created_at->diffForHumans() }}</div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
