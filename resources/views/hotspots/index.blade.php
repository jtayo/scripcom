@extends('layouts.admin')

@section('title', 'Hotspots')
@section('page-title', 'Hotspots')

@section('content')
    <div class="row row-cards">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        Hotspots
                        <span class="badge bg-secondary-lt ms-2">{{ $hotspots->total() }}</span>
                    </div>
                    <div class="card-actions d-flex align-items-center gap-2">
                        <form method="GET" action="{{ route('admin.hotspots.index') }}" class="d-flex gap-1">
                            <div class="input-icon">
                                <span class="input-icon-addon"><i class="ti ti-search"></i></span>
                                <input type="text" name="search" class="form-control" style="min-width: 220px;"
                                       placeholder="Search hotspots..."
                                       value="{{ request('search') }}" aria-label="Search hotspots">
                                @if(request('search'))
                                <a href="{{ route('admin.hotspots.index') }}" class="input-icon-addon text-decoration-none" title="Clear search">
                                    <i class="ti ti-x"></i>
                                </a>
                                @endif
                            </div>
                            <select name="status" class="form-select" style="width: auto;" aria-label="Filter by status">
                                <option value="">All statuses</option>
                                @foreach(['online', 'offline', 'degraded', 'maintenance'] as $status)
                                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-outline-secondary d-inline-flex align-items-center">
                                <i class="ti ti-filter me-1"></i>Filter
                            </button>
                            @if(request('status') || request('search'))
                            <a href="{{ route('admin.hotspots.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center" title="Clear filters">
                                <i class="ti ti-x"></i>
                            </a>
                            @endif
                        </form>
                        @can('create-hotspot')
                        <a href="{{ route('admin.hotspots.create') }}" class="btn btn-primary d-inline-flex align-items-center">
                            <i class="ti ti-plus me-1"></i>New Hotspot
                        </a>
                        @endcan
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter table-nowrap card-table mb-0">
                        <thead>
                            <tr>
                                <th>Hotspot</th>
                                <th>Location</th>
                                <th class="text-center">Sessions</th>
                                <th>Status</th>
                                <th>Last Seen</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($hotspots as $hotspot)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="avatar avatar-sm bg-{{ $hotspot->status === 'online' ? 'success' : 'secondary' }}-lt text-{{ $hotspot->status === 'online' ? 'success' : 'secondary' }} me-2">
                                            <i class="ti ti-wifi"></i>
                                        </span>
                                        <div>
                                            <a href="{{ route('admin.hotspots.show', $hotspot) }}" class="text-body fw-bold text-decoration-none">{{ $hotspot->name }}</a>
                                            <div class="small text-muted">Router #{{ $hotspot->router_id ?? '—' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="d-inline-flex align-items-center text-muted">
                                        <i class="ti ti-map-pin me-1 text-secondary"></i>
                                        @if($hotspot->ward){{ $hotspot->ward }}@endif
                                        @if($hotspot->ward && $hotspot->sub_county) &middot; @endif
                                        @if($hotspot->sub_county){{ $hotspot->sub_county }}@endif
                                        @if(!$hotspot->ward && !$hotspot->sub_county)—@endif
                                    </span>
                                </td>
                                <td class="text-center"><span class="badge bg-secondary-lt">{{ number_format($hotspot->sessions_count) }}</span></td>
                                <td>
                                    @php $color = match($hotspot->status) { 'online' => 'success', 'offline' => 'danger', 'degraded' => 'warning', default => 'secondary' }; @endphp
                                    <span class="badge bg-{{ $color }}-lt">
                                        <span class="status-dot @if($hotspot->status === 'online') status-dot-animated @endif bg-{{ $color }} me-1 d-inline-block"></span>
                                        {{ ucfirst($hotspot->status) }}
                                    </span>
                                </td>
                                <td class="text-muted">{{ $hotspot->last_seen_at?->diffForHumans() ?? '—' }}</td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ route('admin.hotspots.show', $hotspot) }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center" title="View">
                                            <i class="ti ti-eye me-1"></i>View
                                        </a>
                                        @can('update-hotspot')
                                        <a href="{{ route('admin.hotspots.edit', $hotspot) }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center" title="Edit">
                                            <i class="ti ti-edit me-1"></i>Edit
                                        </a>
                                        @endcan
                                        @can('delete-hotspot')
                                        <form method="POST" action="{{ route('admin.hotspots.destroy', $hotspot) }}" class="d-inline" onsubmit="return confirm('Delete this hotspot?');">
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
                                <td colspan="8" class="text-center text-muted py-5">
                                    <div class="my-4">
                                        <i class="ti ti-building-broadcast-tower text-secondary" style="font-size: 2.5rem;"></i>
                                        <div class="mt-2">No hotspots found.</div>
                                        @if(request('search') || request('status'))
                                        <div class="small text-secondary mt-1">
                                            Try a different filter or <a href="{{ route('admin.hotspots.index') }}" class="text-primary">clear filters</a>.
                                        </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($hotspots->hasPages())
                <div class="card-footer py-3">
                    {{ $hotspots->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection
