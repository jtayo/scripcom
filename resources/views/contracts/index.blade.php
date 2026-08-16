@extends('layouts.admin')

@section('title', 'Contracts')
@section('page-title', 'Contracts')

@section('content')
    <div class="row row-cards">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        Contracts
                        <span class="badge bg-secondary-lt ms-2">{{ $contracts->total() }}</span>
                    </div>
                    <div class="card-actions d-flex align-items-center gap-2">
                        <form method="GET" action="{{ route('admin.contracts.index') }}" class="d-flex gap-1">
                            <div class="input-icon">
                                <span class="input-icon-addon"><i class="ti ti-search"></i></span>
                                <input type="text" name="search" class="form-control" style="min-width: 220px;"
                                       placeholder="Search contracts..." value="{{ request('search') }}" aria-label="Search contracts">
                                @if(request('search'))
                                    <a href="{{ route('admin.contracts.index') }}" class="input-icon-addon text-decoration-none" title="Clear search">
                                        <i class="ti ti-x"></i>
                                    </a>
                                @endif
                            </div>
                            <select name="status" class="form-select" style="width: auto;" aria-label="Filter by status">
                                <option value="">All statuses</option>
                                @foreach(['active', 'draft', 'completed', 'cancelled'] as $status)
                                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                            <select name="type" class="form-select" style="width: auto;" aria-label="Filter by type">
                                <option value="">All types</option>
                                @foreach(['county', 'corporate', 'advertising'] as $type)
                                    <option value="{{ $type }}" @selected(request('type') === $type)>{{ ucfirst($type) }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-outline-secondary d-inline-flex align-items-center">
                                <i class="ti ti-filter me-1"></i>Filter
                            </button>
                            @if(request('status') || request('type') || request('search'))
                                <a href="{{ route('admin.contracts.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center" title="Clear filters">
                                    <i class="ti ti-x"></i>
                                </a>
                            @endif
                        </form>
                        @can('create-contract')
                            <a href="{{ route('admin.contracts.create') }}" class="btn btn-primary d-inline-flex align-items-center">
                                <i class="ti ti-plus me-1"></i>New Contract
                            </a>
                        @endcan
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter table-nowrap card-table mb-0">
                        <thead>
                            <tr>
                                <th>Contract</th>
                                <th>Type</th>
                                <th>Sponsor</th>
                                <th class="text-end">Allocated</th>
                                <th class="text-end">Value</th>
                                <th>Status</th>
                                <th>Period</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($contracts as $contract)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.contracts.show', $contract) }}" class="text-body fw-bold text-decoration-none">{{ $contract->title }}</a>
                                        <div class="small text-muted">{{ $contract->contract_number }}</div>
                                    </td>
                                    <td><span class="badge bg-primary-lt">{{ $contract->typeLabel() }}</span></td>
                                    <td class="text-muted">{{ $contract->sponsor?->name ?? '—' }}</td>
                                    <td class="text-end">{{ number_format($contract->sessions_allocated) }}</td>
                                    <td class="text-end">KSh {{ number_format($contract->contractValue(), 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $contract->statusColor() }}-lt">{{ ucfirst($contract->status) }}</span>
                                    </td>
                                    <td class="text-muted">
                                        {{ $contract->start_date?->format('d M Y') }} — {{ $contract->end_date?->format('d M Y') }}
                                    </td>
                                    <td class="text-end">
                                        <div class="d-inline-flex gap-1">
                                            <a href="{{ route('admin.contracts.show', $contract) }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center" title="View">
                                                <i class="ti ti-eye me-1"></i>View
                                            </a>
                                            @can('update-contract')
                                                <a href="{{ route('admin.contracts.edit', $contract) }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center" title="Edit">
                                                    <i class="ti ti-edit me-1"></i>Edit
                                                </a>
                                            @endcan
                                            @can('delete-contract')
                                                <form method="POST" action="{{ route('admin.contracts.destroy', $contract) }}" class="d-inline" onsubmit="return confirm('Delete this contract?');">
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
                                            <i class="ti ti-file-text text-secondary" style="font-size: 2.5rem;"></i>
                                            <div class="mt-2">No contracts found.</div>
                                            @if(request('search') || request('status') || request('type'))
                                                <div class="small text-secondary mt-1">
                                                    Try a different filter or <a href="{{ route('admin.contracts.index') }}" class="text-primary">clear filters</a>.
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($contracts->hasPages())
                    <div class="card-footer py-3">
                        {{ $contracts->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
