@extends('layouts.admin')

@section('title', 'Audit Logs')
@section('page-title', 'Audit Logs')
@section('page-subtitle', 'Immutable trail of administrative operations')

@section('content')
    <div class="row row-cards">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        Audit Logs
                        <span class="badge bg-secondary-lt ms-2">{{ $auditLogs->total() }}</span>
                    </div>
                    <div class="card-actions">
                        <form method="GET" action="{{ route('admin.audit-logs.index') }}" class="d-flex flex-wrap gap-1 align-items-center">
                            <select name="action" class="form-select form-select-sm" style="width: auto;" aria-label="Filter by action">
                                <option value="">All actions</option>
                                @foreach($actions as $action)
                                    <option value="{{ $action }}" @selected(request('action') === $action)>{{ ucfirst($action) }}</option>
                                @endforeach
                            </select>
                            <select name="entity_type" class="form-select form-select-sm" style="width: auto;" aria-label="Filter by entity">
                                <option value="">All entities</option>
                                @foreach($entityTypes as $type)
                                    <option value="{{ $type }}" @selected(request('entity_type') === $type)>{{ \Illuminate\Support\Str::of($type)->afterLast('\\')->replace('_', ' ')->title() }}</option>
                                @endforeach
                            </select>
                            <input type="search" name="search" class="form-control form-control-sm" style="width: auto;"
                                   value="{{ request('search') }}" placeholder="Search user, IP, entity..." aria-label="Search">
                            <input type="date" name="from" class="form-control form-control-sm" style="width: auto;" value="{{ request('from') }}" aria-label="From">
                            <input type="date" name="to" class="form-control form-control-sm" style="width: auto;" value="{{ request('to') }}" aria-label="To">
                            <button type="submit" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">
                                <i class="ti ti-filter me-1"></i>Filter
                            </button>
                            @if(request('action') || request('entity_type') || request('search') || request('from') || request('to'))
                            <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center" title="Clear filters">
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
                                <th>#</th>
                                <th>Action</th>
                                <th>Entity</th>
                                <th>Actor</th>
                                <th>IP Address</th>
                                <th>When</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($auditLogs as $log)
                                @php
                                    $color = match ($log->action) {
                                        'created' => 'success',
                                        'updated' => 'primary',
                                        'deleted', 'force-deleted' => 'danger',
                                        'restored' => 'warning',
                                        default => 'secondary',
                                    };
                                @endphp
                                <tr>
                                    <td class="text-muted">#{{ $log->id }}</td>
                                    <td>
                                        <span class="badge bg-{{ $color }}-lt">{{ $log->actionLabel() }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.audit-logs.show', $log) }}" class="text-body fw-bold text-decoration-none">{{ $log->entityLabel() }}</a>
                                        @if($log->entity_id)
                                            <div class="small text-muted">ID {{ $log->entity_id }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($log->user)
                                            <span class="d-inline-flex align-items-center">
                                                <span class="avatar avatar-xs me-2">{{ strtoupper(substr($log->user->name, 0, 1)) }}</span>
                                                {{ $log->user->name }}
                                            </span>
                                        @else
                                            <span class="text-muted">System</span>
                                        @endif
                                    </td>
                                    <td class="text-muted">{{ $log->ip_address ?? '—' }}</td>
                                    <td class="small text-muted">{{ $log->created_at?->format('M d, Y H:i:s') }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.audit-logs.show', $log) }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">
                                            <i class="ti ti-eye me-1"></i>View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-5">
                                        <div class="my-4">
                                            <i class="ti ti-shield-lock text-secondary" style="font-size: 2.5rem;"></i>
                                            <div class="mt-2">No audit logs found.</div>
                                            <div class="small text-secondary mt-1">Administrative changes are recorded here automatically.</div>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($auditLogs->hasPages())
                <div class="card-footer py-3 border-top-0">
                    {{ $auditLogs->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection
