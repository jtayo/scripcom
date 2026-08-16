@extends('layouts.admin')

@section('title', 'Routers')
@section('page-title', 'Routers')

@section('content')
    <div class="row row-cards">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        Routers
                        <span class="badge bg-secondary-lt ms-2">{{ $routers->total() }}</span>
                    </div>
                    <div class="card-actions d-flex align-items-center gap-2">
                        <form method="GET" action="{{ route('admin.routers.index') }}" class="d-flex gap-1">
                            <div class="input-icon">
                                <span class="input-icon-addon"><i class="ti ti-search"></i></span>
                                <input type="text" name="search" class="form-control" style="min-width: 220px;"
                                       placeholder="Search routers..." value="{{ request('search') }}" aria-label="Search routers">
                                @if(request('search'))
                                    <a href="{{ route('admin.routers.index') }}" class="input-icon-addon text-decoration-none" title="Clear search">
                                        <i class="ti ti-x"></i>
                                    </a>
                                @endif
                            </div>
                            <select name="status" class="form-select" style="width: auto;" aria-label="Filter by status">
                                <option value="">All statuses</option>
                                @foreach(['online', 'degraded', 'offline', 'maintenance'] as $status)
                                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-outline-secondary d-inline-flex align-items-center">
                                <i class="ti ti-filter me-1"></i>Filter
                            </button>
                            @if(request('status') || request('search'))
                                <a href="{{ route('admin.routers.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center" title="Clear filters">
                                    <i class="ti ti-x"></i>
                                </a>
                            @endif
                        </form>
                        @can('create-router')
                            <a href="{{ route('admin.routers.create') }}" class="btn btn-primary d-inline-flex align-items-center">
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
                                <th>Organization</th>
                                <th>IP Address</th>
                                <th>Status</th>
                                <th>Last Seen</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($routers as $router)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="avatar avatar-sm bg-{{ $router->statusColor() }}-lt text-{{ $router->statusColor() }} me-2">
                                                <i class="ti ti-device-router"></i>
                                            </span>
                                            <div>
                                                <a href="{{ route('admin.routers.show', $router) }}" class="text-body fw-bold text-decoration-none">{{ $router->name }}</a>
                                                <div class="small text-muted">{{ $router->model ?? '—' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-muted">{{ $router->hotspot?->name ?? '—' }}</td>
                                    <td class="text-muted">{{ $router->organization?->name ?? '—' }}</td>
                                    <td>
                                        <span class="font-monospace small">{{ $router->ip_address ?? '—' }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $router->statusColor() }}-lt">
                                            <span class="status-dot @if($router->status === 'online') status-dot-animated @endif bg-{{ $router->statusColor() }} me-1 d-inline-block"></span>
                                            {{ ucfirst($router->status) }}
                                        </span>
                                    </td>
                                    <td class="text-muted">{{ $router->last_seen_at?->diffForHumans() ?? '—' }}</td>
                                    <td class="text-end">
                                        <div class="d-inline-flex gap-1">
                                            <a href="{{ route('admin.routers.show', $router) }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center" title="View">
                                                <i class="ti ti-eye me-1"></i>View
                                            </a>
                                            @can('update-router')
                                                <a href="{{ route('admin.routers.edit', $router) }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center" title="Edit">
                                                    <i class="ti ti-edit me-1"></i>Edit
                                                </a>
                                            @endcan
                                            @can('delete-router')
                                                <form method="POST" action="{{ route('admin.routers.destroy', $router) }}" class="d-inline" onsubmit="return confirm('Delete this router?');">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger d-inline-flex align-items-center" title="Delete">
                                                        <i class="ti ti-trash me-1"></i>Delete
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-5">
                                        <div class="my-4">
                                            <i class="ti ti-device-router text-secondary" style="font-size: 2.5rem;"></i>
                                            <div class="mt-2">No routers found.</div>
                                            @if(request('search') || request('status'))
                                                <div class="small text-secondary mt-1">
                                                    Try a different filter or <a href="{{ route('admin.routers.index') }}" class="text-primary">clear filters</a>.
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($routers->hasPages())
                    <div class="card-footer py-3">
                        {{ $routers->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
