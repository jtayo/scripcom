@extends('layouts.admin')

@section('title', $router->name)
@section('page-title', $router->name)
@section('page-subtitle', 'Router details and health history')

@section('content')
    <div class="row g-3 mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body d-flex flex-wrap align-items-center gap-3">
                    <span class="avatar avatar-lg bg-{{ $router->statusColor() }}-lt text-{{ $router->statusColor() }}">
                        <i class="fa-solid fa-device-router"></i>
                    </span>
                    <div class="flex-fill">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <h2 class="h4 mb-0">{{ $router->name }}</h2>
                            <span class="badge bg-{{ $router->statusColor() }}-lt">
                                <span class="status-dot @if($router->status === 'online') status-dot-animated @endif bg-{{ $router->statusColor() }} me-1 d-inline-block"></span>
                                {{ ucfirst($router->status) }}
                            </span>
                            @unless($router->is_active)
                                <span class="badge bg-secondary-lt">Inactive</span>
                            @endunless
                        </div>
                        <div class="text-secondary mt-1">
                            {{ $router->model ?? 'Generic router' }}
                            @if($router->firmware_version) &middot; Firmware {{ $router->firmware_version }} @endif
                        </div>
                    </div>
                    <div class="d-inline-flex gap-2">
                        @can('update-router')
                            <form method="POST" action="{{ route('admin.device-monitoring.check', $router) }}">
                                @csrf
                                <button type="submit" class="btn btn-outline-primary d-inline-flex align-items-center">
                                    <i class="fa-solid fa-rotate me-2"></i>Run Check
                                </button>
                            </form>
                        @endcan
                        @can('update-router')
                            <a href="{{ route('admin.routers.edit', $router) }}" class="btn btn-outline-secondary d-inline-flex align-items-center">
                                <i class="fa-solid fa-pen me-2"></i>Edit
                            </a>
                        @endcan
                        <a href="{{ route('admin.routers.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center">
                            <i class="fa-solid fa-arrow-left me-2"></i>Back
                        </a>
                    </div>
                </div>
            </div>
        </div>

        @php $health = $router->latestHealth(); @endphp
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <div class="text-muted small">CPU Usage</div>
                            <div class="h3 mb-0">{{ optional($health)->cpu_usage !== null ? optional($health)->cpu_usage . '%' : '—' }}</div>
                        </div>
                        <i class="fa-solid fa-microchip ms-auto text-secondary"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <div class="text-muted small">Memory Usage</div>
                            <div class="h3 mb-0">{{ optional($health)->memory_usage !== null ? optional($health)->memory_usage . '%' : '—' }}</div>
                        </div>
                        <i class="fa-solid fa-memory ms-auto text-secondary"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <div class="text-muted small">Active Users</div>
                            <div class="h3 mb-0">{{ optional($health)->active_users ?? '—' }}</div>
                        </div>
                        <i class="fa-solid fa-users ms-auto text-secondary"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <div class="text-muted small">Last Seen</div>
                            <div class="h3 mb-0">{{ $router->last_seen_at?->diffForHumans() ?? '—' }}</div>
                        </div>
                        <i class="fa-solid fa-clock ms-auto text-secondary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cards">
        <div class="col-12 col-xl-4">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Details</div>
                </div>
                <div class="card-body">
                    <div class="datagrid">
                        <div class="datagrid-item">
                            <div class="datagrid-title">Organization</div>
                            <div class="datagrid-content">{{ $router->organization?->name ?? '—' }}</div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">Hotspot</div>
                            <div class="datagrid-content">
                                @if($router->hotspot)
                                    <a href="{{ route('admin.hotspots.show', $router->hotspot) }}">{{ $router->hotspot->name }}</a>
                                    <span class="badge bg-{{ $router->hotspot->status }}-lt ms-1">{{ ucfirst($router->hotspot->status) }}</span>
                                @else
                                    —
                                @endif
                            </div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">IP Address</div>
                            <div class="datagrid-content font-monospace">{{ $router->ip_address ?? '—' }}</div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">API Port</div>
                            <div class="datagrid-content">{{ $router->port ?? 8728 }}</div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">API Username</div>
                            <div class="datagrid-content">{{ $router->username ?? '—' }}</div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">Last Online</div>
                            <div class="datagrid-content">{{ $router->last_online_at?->diffForHumans() ?? '—' }}</div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">Registered</div>
                            <div class="datagrid-content">{{ $router->created_at?->format('d M Y') ?? '—' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-8">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Health History</div>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table mb-0">
                        <thead>
                            <tr>
                                <th>Recorded</th>
                                <th>Status</th>
                                <th class="text-end">CPU</th>
                                <th class="text-end">Memory</th>
                                <th class="text-end">Latency</th>
                                <th class="text-end">Uptime</th>
                                <th class="text-end">Users</th>
                                <th class="text-end">Traffic (Rx/Tx)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($healthLogs as $log)
                                <tr>
                                    <td class="text-muted">{{ $log->recorded_at->format('d M Y H:i:s') }}</td>
                                    <td>
                                        @php $color = match($log->status) { 'online' => 'success', 'degraded' => 'warning', 'offline' => 'danger', default => 'secondary' }; @endphp
                                        <span class="badge bg-{{ $color }}-lt">{{ ucfirst($log->status) }}</span>
                                    </td>
                                    <td class="text-end">{{ $log->cpu_usage !== null ? $log->cpu_usage . '%' : '—' }}</td>
                                    <td class="text-end">{{ $log->memory_usage !== null ? $log->memory_usage . '%' : '—' }}</td>
                                    <td class="text-end">{{ $log->latency_ms !== null ? $log->latency_ms . ' ms' : '—' }}</td>
                                    <td class="text-end">{{ $log->uptimeLabel() }}</td>
                                    <td class="text-end">{{ $log->active_users ?? '—' }}</td>
                                    <td class="text-end text-muted">
                                        @if($log->rx_bytes !== null)
                                            {{ number_format($log->rx_bytes / 1048576, 1) }} / {{ number_format($log->tx_bytes / 1048576, 1) }} MB
                                        @else
                                            —
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-5">No health records yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($healthLogs->hasPages())
                    <div class="card-footer py-3">
                        {{ $healthLogs->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
