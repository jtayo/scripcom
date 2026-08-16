@extends('layouts.admin')

@section('title', 'Invoices')
@section('page-title', 'Invoices')

@section('content')
    <div class="row row-cards">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        Invoices
                        <span class="badge bg-secondary-lt ms-2">{{ $invoices->total() }}</span>
                    </div>
                    <div class="card-actions d-flex align-items-center gap-2">
                        <form method="GET" action="{{ route('admin.invoices.index') }}" class="d-flex gap-1">
                            <div class="input-icon">
                                <span class="input-icon-addon"><i class="ti ti-search"></i></span>
                                <input type="text" name="search" class="form-control" style="min-width: 220px;"
                                       placeholder="Search invoices..." value="{{ request('search') }}" aria-label="Search invoices">
                                @if(request('search'))
                                    <a href="{{ route('admin.invoices.index') }}" class="input-icon-addon text-decoration-none" title="Clear search">
                                        <i class="ti ti-x"></i>
                                    </a>
                                @endif
                            </div>
                            <select name="status" class="form-select" style="width: auto;" aria-label="Filter by status">
                                <option value="">All statuses</option>
                                @foreach(['draft', 'sent', 'paid', 'overdue', 'cancelled'] as $status)
                                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-outline-secondary d-inline-flex align-items-center">
                                <i class="ti ti-filter me-1"></i>Filter
                            </button>
                            @if(request('status') || request('search'))
                                <a href="{{ route('admin.invoices.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center" title="Clear filters">
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
                                <th>Invoice</th>
                                <th>Contract</th>
                                <th>Issued</th>
                                <th>Due</th>
                                <th>Status</th>
                                <th class="text-end">Total</th>
                                <th class="text-end">Balance</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($invoices as $invoice)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.invoices.show', $invoice) }}" class="text-body fw-bold text-decoration-none">{{ $invoice->invoice_number }}</a>
                                        <div class="small text-muted">{{ $invoice->organization?->name ?? '—' }}</div>
                                    </td>
                                    <td class="text-muted">{{ $invoice->contract?->title ?? '—' }}</td>
                                    <td class="text-muted">{{ $invoice->issue_date?->format('d M Y') ?? '—' }}</td>
                                    <td class="text-muted">{{ $invoice->due_date?->format('d M Y') ?? '—' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $invoice->statusColor() }}-lt">{{ ucfirst($invoice->status) }}</span>
                                    </td>
                                    <td class="text-end">KSh {{ number_format((float) $invoice->total, 2) }}</td>
                                    <td class="text-end">KSh {{ number_format($invoice->balanceDue(), 2) }}</td>
                                    <td class="text-end">
                                        <div class="d-inline-flex gap-1">
                                            <a href="{{ route('admin.invoices.show', $invoice) }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center" title="View">
                                                <i class="ti ti-eye me-1"></i>View
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-5">
                                        <div class="my-4">
                                            <i class="ti ti-file-invoice text-secondary" style="font-size: 2.5rem;"></i>
                                            <div class="mt-2">No invoices found.</div>
                                            @if(request('search') || request('status'))
                                                <div class="small text-secondary mt-1">
                                                    Try a different filter or <a href="{{ route('admin.invoices.index') }}" class="text-primary">clear filters</a>.
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($invoices->hasPages())
                    <div class="card-footer py-3">
                        {{ $invoices->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
