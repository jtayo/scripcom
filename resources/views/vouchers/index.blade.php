@extends('layouts.admin')

@section('title', 'Vouchers')
@section('page-title', 'Vouchers')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.vouchers.index') }}" class="row g-3 align-items-center">
                        <div class="col-12 col-md-5 col-lg-4">
                            <div class="input-group">
                                <span class="input-group-text"><svg class="icon text-secondary" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path></svg></span>
                                <input type="text" name="search" class="form-control" placeholder="Search code or batch..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-12 col-md-3 col-lg-2">
                            <select name="status" class="form-select">
                                <option value="">All statuses</option>
                                @foreach(['unused', 'redeemed', 'expired', 'revoked'] as $status)
                                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-4 col-lg-2">
                            <button type="submit" class="btn btn-dark d-inline-flex align-items-center">Filter</button>
                        </div>
                        <div class="col-12 col-lg-4 text-lg-end">
                            @can('create-voucher')
                            <a href="{{ route('admin.vouchers.create') }}" class="btn btn-primary d-inline-flex align-items-center">
                                <svg class="icon me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                Generate Vouchers
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
                                    <th class="border-0 rounded-start">Code</th>
                                    <th class="border-0">Batch</th>
                                    <th class="border-0">Type</th>
                                    <th class="border-0">Value</th>
                                    <th class="border-0">Sponsor</th>
                                    <th class="border-0">Status</th>
                                    <th class="border-0 rounded-end text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($vouchers as $voucher)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.vouchers.show', $voucher) }}" class="text-body fw-bold font-monospace">{{ $voucher->code }}</a>
                                    </td>
                                    <td class="small text-muted">{{ $voucher->batch_id }}</td>
                                    <td><span class="badge bg-secondary">{{ ucfirst($voucher->type) }}</span></td>
                                    <td>{{ number_format($voucher->value) }}</td>
                                    <td>{{ $voucher->sponsor->name ?? '—' }}</td>
                                    <td>
                                        @if($voucher->isExpired() && $voucher->status === 'unused')
                                            <span class="badge bg-warning">Expired</span>
                                        @else
                                            <span class="badge bg-{{ $voucher->status === 'unused' ? 'success' : ($voucher->status === 'redeemed' ? 'secondary' : 'danger') }}">{{ ucfirst($voucher->status) }}</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group">
                                            <a href="{{ route('admin.vouchers.show', $voucher) }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">View</a>
                                            @can('delete-voucher')
                                            <form method="POST" action="{{ route('admin.vouchers.destroy', $voucher) }}" class="d-inline" onsubmit="return confirm('Delete this voucher?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger d-inline-flex align-items-center">Delete</button>
                                            </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="7" class="text-center text-muted py-5">No vouchers found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($vouchers->hasPages())
                <div class="card-footer border-0 py-2">
                    {{ $vouchers->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection
