@extends('layouts.admin')

@section('title', 'Sessions')
@section('page-title', 'Sessions')

@section('content')
    <div class="row row-cards">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <div>
                            Sessions
                            <span class="badge bg-secondary-lt ms-2">{{ $sessions->total() }}</span>
                        </div>
                        <div class="text-muted small fw-normal mt-1">Wi-Fi network connection sessions</div>
                    </div>
                    <div class="card-actions d-flex align-items-center gap-2">
                        <form method="GET" action="{{ route('admin.sessions.index') }}" class="d-flex gap-1">
                            <div class="input-icon">
                                <span class="input-icon-addon"><i class="ti ti-search"></i></span>
                                <input type="text" name="search" class="form-control" style="min-width: 220px;"
                                       placeholder="Search MAC..." value="{{ request('search') }}" aria-label="Search MAC">
                                @if(request('search'))
                                <a href="{{ route('admin.sessions.index') }}" class="input-icon-addon text-decoration-none" title="Clear search">
                                    <i class="ti ti-x"></i>
                                </a>
                                @endif
                            </div>
                            <select name="status" class="form-select" style="width: auto;" aria-label="Filter by status">
                                <option value="">All statuses</option>
                                @foreach($statuses as $status)
                                    <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
                                @endforeach
                            </select>
                            <input type="date" name="date" class="form-control" style="width: auto;" value="{{ request('date') }}" aria-label="Filter by date">
                            <button type="submit" class="btn btn-outline-secondary d-inline-flex align-items-center">
                                <i class="ti ti-filter me-1"></i>Filter
                            </button>
                            @if(request()->hasAny(['search', 'status', 'date']))
                            <a href="{{ route('admin.sessions.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center" title="Clear filters">
                                <i class="ti ti-x"></i>
                            </a>
                            @endif
                        </form>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter table-nowrap card-table mb-0">
                        <thead>
                            <tr>
                                <th>Session</th>
                                <th>Hotspot</th>
                                <th>Campaign</th>
                                <th>Started</th>
                                <th>Duration</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sessions as $session)
                            @php
                                $apiStatus = strtolower($session['status'] ?? '');
                                $color = match ($apiStatus) {
                                    'active' => 'success',
                                    'expired' => 'warning',
                                    'failed' => 'danger',
                                    default => 'secondary',
                                };
                                $label = match ($apiStatus) {
                                    'active' => 'Active',
                                    'expired' => 'Expired',
                                    'failed' => 'Failed',
                                    default => ucfirst($session['status'] ?? 'Unknown'),
                                };
                            @endphp
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="avatar avatar-sm bg-primary-lt text-primary me-2">
                                            <i class="ti ti-device-mobile"></i>
                                        </span>
                                        <div>
                                            @if($session['session'])
                                            <a href="{{ route('admin.sessions.show', $session['session']) }}" class="text-body fw-bold text-decoration-none"><code>{{ $session['mac_address'] }}</code></a>
                                            @else
                                            <code class="text-body fw-bold">{{ $session['mac_address'] }}</code>
                                            @endif
                                            <div class="small text-muted">{{ $session['router_name'] ?: '—' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($session['hotspot'])
                                    <a href="{{ route('admin.hotspots.show', $session['hotspot']) }}" class="text-body text-decoration-none">{{ $session['hotspot']->name }}</a>
                                    @else — @endif
                                </td>
                                <td>
                                    @if($session['campaign'])
                                    <a href="{{ route('admin.campaigns.show', $session['campaign']) }}" class="text-body text-decoration-none">{{ $session['campaign']->title }}</a>
                                    @else — @endif
                                </td>
                                <td class="text-muted">{{ $session['started_at']?->format('M d, H:i') ?? '—' }}</td>
                                <td class="text-muted">
                                    @if($session['total_duration'] !== null) {{ gmdate('H:i:s', $session['total_duration']) }} @else — @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $color }}-lt">
                                        <span class="status-dot @if($apiStatus === 'active') status-dot-animated @endif bg-{{ $color }} me-1 d-inline-block"></span>
                                        {{ $label }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    @if($session['session'])
                                    <div class="btn-group">
                                        <a href="{{ route('admin.sessions.show', $session['session']) }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center" title="View">
                                            <i class="ti ti-eye me-1"></i>View
                                        </a>
                                        @can('delete-session')
                                        <form method="POST" action="{{ route('admin.sessions.destroy', $session['session']) }}" class="d-inline" onsubmit="return confirm('{{ $session['session']->status === 'active' ? 'Terminate this session?' : 'Delete this session?' }}');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger d-inline-flex align-items-center" title="{{ $session['session']->status === 'active' ? 'Terminate' : 'Delete' }}">
                                                <i class="ti ti-trash me-1"></i>{{ $session['session']->status === 'active' ? 'Terminate' : 'Delete' }}
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                    @else
                                    <span class="text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <div class="my-4">
                                        <i class="ti ti-device-mobile text-secondary" style="font-size: 2.5rem;"></i>
                                        <div class="mt-2">No sessions found.</div>
                                        @if(request()->hasAny(['search', 'status', 'date']))
                                        <div class="small text-secondary mt-1">
                                            Try a different filter or <a href="{{ route('admin.sessions.index') }}" class="text-primary">clear filters</a>.
                                        </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($sessions->total() > 0)
                <div class="card-footer py-3 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                    <div class="text-muted small">
                        Showing <span class="fw-semibold">{{ $sessions->firstItem() ?: 0 }}</span> to
                        <span class="fw-semibold">{{ $sessions->lastItem() ?: 0 }}</span> of
                        <span class="fw-semibold">{{ number_format($sessions->total()) }}</span> sessions
                    </div>
                    @if($sessions->hasPages())
                    <div class="d-flex justify-content-center justify-content-md-end">
                        {{ $sessions->links('vendor.pagination.tabler') }}
                    </div>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection
