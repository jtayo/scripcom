@extends('layouts.admin')

@section('title', 'Hotspots')
@section('page-title', 'Hotspots')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.hotspots.index') }}" class="row g-3 align-items-center">
                        <div class="col-12 col-md-5 col-lg-4">
                            <div class="input-group">
                                <span class="input-group-text"><svg class="icon text-secondary" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path></svg></span>
                                <input type="text" name="search" class="form-control" placeholder="Search hotspots..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-12 col-md-3 col-lg-2">
                            <select name="status" class="form-select">
                                <option value="">All statuses</option>
                                @foreach(['online', 'offline', 'degraded', 'maintenance'] as $status)
                                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-4 col-lg-2">
                            <button type="submit" class="btn btn-dark d-inline-flex align-items-center">Filter</button>
                        </div>
                        <div class="col-12 col-lg-4 text-lg-end">
                            @can('create-hotspot')
                            <a href="{{ route('admin.hotspots.create') }}" class="btn btn-primary d-inline-flex align-items-center">
                                <svg class="icon me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                New Hotspot
                            </a>
                            @endcan
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-vcenter table-nowrap mb-0">
                            <thead class="">
                                <tr>
                                    <th class="border-0 rounded-start">Name</th>
                                    <th class="border-0">Organization</th>
                                    <th class="border-0">SSID</th>
                                    <th class="border-0">Ward</th>
                                    <th class="border-0">Sessions</th>
                                    <th class="border-0">Status</th>
                                    <th class="border-0">Last Seen</th>
                                    <th class="border-0 rounded-end text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($hotspots as $hotspot)
                                <tr>
                                    <td class="text-body fw-bold">
                                        <a href="{{ route('admin.hotspots.show', $hotspot) }}">{{ $hotspot->name }}</a>
                                        <div class="small text-muted">Router #{{ $hotspot->router_id ?? '—' }}</div>
                                    </td>
                                    <td>{{ $hotspot->organization->name ?? '—' }}</td>
                                    <td>{{ $hotspot->ssid ?? '—' }}</td>
                                    <td>{{ $hotspot->ward ?? '—' }}</td>
                                    <td>{{ number_format($hotspot->sessions_count) }}</td>
                                    <td>
                                        @php $color = match($hotspot->status) { 'online' => 'success', 'offline' => 'danger', 'degraded' => 'warning', default => 'secondary' }; @endphp
                                        <span class="badge bg-{{ $color }}">{{ ucfirst($hotspot->status) }}</span>
                                    </td>
                                    <td class="text-muted">{{ $hotspot->last_seen_at?->diffForHumans() ?? '—' }}</td>
                                    <td class="text-end">
                                        <div class="btn-group">
                                            <a href="{{ route('admin.hotspots.show', $hotspot) }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">View</a>
                                            @can('update-hotspot')
                                            <a href="{{ route('admin.hotspots.edit', $hotspot) }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">Edit</a>
                                            @endcan
                                            @can('delete-hotspot')
                                            <form method="POST" action="{{ route('admin.hotspots.destroy', $hotspot) }}" class="d-inline" onsubmit="return confirm('Delete this hotspot?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger d-inline-flex align-items-center">Delete</button>
                                            </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="8" class="text-center text-muted py-5">No hotspots found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($hotspots->hasPages())
                <div class="card-footer border-0 py-2">
                    {{ $hotspots->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection
