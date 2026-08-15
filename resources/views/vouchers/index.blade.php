@extends('layouts.admin')

@section('title', 'Vouchers')
@section('page-title', 'Vouchers')

@section('content')
    <div class="row row-cards">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div>
                        <div class="card-title mb-0">
                            Vouchers
                            <span class="badge bg-secondary-lt ms-2">{{ $vouchers->total() }}</span>
                        </div>
                        <div class="small text-muted">Issue and manage redemption vouchers</div>
                    </div>
                    <div class="card-actions d-flex align-items-center gap-2">
                        <form method="GET" action="{{ route('admin.vouchers.index') }}" class="d-flex gap-1">
                            <div class="input-icon">
                                <span class="input-icon-addon"><i class="ti ti-search"></i></span>
                                <input type="text" name="search" class="form-control" style="min-width: 220px;"
                                       placeholder="Search code or batch..."
                                       value="{{ request('search') }}" aria-label="Search vouchers">
                                @if(request('search'))
                                <a href="{{ route('admin.vouchers.index') }}" class="input-icon-addon text-decoration-none" title="Clear search">
                                    <i class="ti ti-x"></i>
                                </a>
                                @endif
                            </div>
                            <select name="status" class="form-select" style="width: auto;" aria-label="Filter by status">
                                <option value="">All statuses</option>
                                @foreach(['unused', 'redeemed', 'expired', 'revoked'] as $status)
                                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-outline-secondary d-inline-flex align-items-center">
                                <i class="ti ti-filter me-1"></i>Filter
                            </button>
                            @if(request('status') || request('search'))
                            <a href="{{ route('admin.vouchers.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center" title="Clear filters">
                                <i class="ti ti-x"></i>
                            </a>
                            @endif
                        </form>
                        @can('create-voucher')
                        <a href="{{ route('admin.vouchers.create') }}" class="btn btn-primary d-inline-flex align-items-center">
                            <i class="ti ti-plus me-1"></i>New Voucher
                        </a>
                        @endcan
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter table-nowrap card-table mb-0">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Batch</th>
                                <th>Type</th>
                                <th class="text-center">Value</th>
                                <th>Sponsor</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vouchers as $voucher)
                            @php
                                $expiredUnused = $voucher->isExpired() && $voucher->status === 'unused';
                                $statusColor = $expiredUnused ? 'danger'
                                    : match($voucher->status) {
                                        'unused' => 'warning',
                                        'redeemed' => 'success',
                                        'expired' => 'danger',
                                        'revoked' => 'secondary',
                                        default => 'dark',
                                    };
                            @endphp
                            <tr>
                                <td>
                                    <a href="{{ route('admin.vouchers.show', $voucher) }}" class="text-body text-decoration-none">
                                        <code class="font-monospace fw-bold">{{ $voucher->code }}</code>
                                    </a>
                                </td>
                                <td>
                                    <span class="d-inline-flex align-items-center text-muted">
                                        <i class="ti ti-layers-intersect me-1 text-secondary"></i>
                                        {{ $voucher->batch_id }}
                                    </span>
                                </td>
                                <td><span class="badge bg-secondary-lt">{{ ucfirst($voucher->type) }}</span></td>
                                <td class="text-center">
                                    <span class="d-inline-flex align-items-center text-muted">
                                        <i class="ti ti-coins me-1 text-secondary"></i>
                                        {{ number_format($voucher->value) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="d-inline-flex align-items-center text-muted">
                                        <i class="ti ti-building-community me-1 text-secondary"></i>
                                        {{ $voucher->sponsor->name ?? '—' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $statusColor }}-lt">
                                        @if($voucher->status === 'redeemed')
                                        <span class="status-dot status-dot-animated me-2"></span>
                                        @endif
                                        {{ $expiredUnused ? 'Expired' : ucfirst($voucher->status) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ route('admin.vouchers.show', $voucher) }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center" title="View">
                                            <i class="ti ti-eye me-1"></i>View
                                        </a>
                                        @can('delete-voucher')
                                        <form method="POST" action="{{ route('admin.vouchers.destroy', $voucher) }}" class="d-inline" onsubmit="return confirm('Delete this voucher?');">
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
                                        <i class="ti ti-ticket text-secondary" style="font-size: 2.5rem;"></i>
                                        <div class="mt-2">No vouchers found.</div>
                                        @if(request('search') || request('status'))
                                        <div class="small text-secondary mt-1">
                                            Try a different filter or <a href="{{ route('admin.vouchers.index') }}" class="text-primary">clear filters</a>.
                                        </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($vouchers->hasPages())
                <div class="card-footer py-3 border-top-0">
                    {{ $vouchers->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection
