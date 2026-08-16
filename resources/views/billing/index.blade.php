@extends('layouts.admin')

@section('title', 'Billing Dashboard')
@section('page-title', 'Billing Dashboard')
@section('page-subtitle', 'Contracts, invoices and revenue at a glance')

@section('content')
    <div class="row row-cards mb-3">
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <div class="text-muted small">Contracts</div>
                            <div class="h3 mb-0">{{ $overview['total_contracts'] }}</div>
                        </div>
                        <i class="fa-solid fa-file-contract ms-auto text-secondary"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <div class="text-muted small">Active</div>
                            <div class="h3 mb-0 text-success">{{ $overview['active_contracts'] }}</div>
                        </div>
                        <i class="fa-solid fa-circle-check ms-auto text-success"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <div class="text-muted small">Invoiced</div>
                            <div class="h3 mb-0">{{ $overview['total_invoices'] }}</div>
                        </div>
                        <i class="fa-solid fa-receipt ms-auto text-secondary"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <div class="text-muted small">Open Invoices</div>
                            <div class="h3 mb-0 text-warning">{{ $overview['open_invoices'] }}</div>
                        </div>
                        <i class="fa-solid fa-hourglass-half ms-auto text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <div class="text-muted small">Outstanding</div>
                            <div class="h3 mb-0">KSh {{ number_format($overview['outstanding_amount'], 2) }}</div>
                        </div>
                        <i class="fa-solid fa-credit-card ms-auto text-danger"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <div class="text-muted small">Collected</div>
                            <div class="h3 mb-0">KSh {{ number_format($overview['collected_amount'], 2) }}</div>
                        </div>
                        <i class="fa-solid fa-sack-dollar ms-auto text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cards">
        <div class="col-12 col-xl-6">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Recent Contracts</div>
                    <div class="card-actions">
                        @can('create-contract')
                            <a href="{{ route('admin.contracts.create') }}" class="btn btn-sm btn-primary d-inline-flex align-items-center">
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
                                <th>Organization</th>
                                <th>Status</th>
                                <th class="text-end">Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($overview['recent_contracts'] as $contract)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.contracts.show', $contract) }}" class="text-body fw-bold text-decoration-none">{{ $contract->title }}</a>
                                        <div class="small text-muted">{{ $contract->contract_number }}</div>
                                    </td>
                                    <td class="text-muted">{{ $contract->organization?->name ?? '—' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $contract->statusColor() }}-lt">{{ ucfirst($contract->status) }}</span>
                                    </td>
                                    <td class="text-end">KSh {{ number_format($contract->contractValue(), 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-5">No contracts yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-6">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Recent Invoices</div>
                    <div class="card-actions">
                        <a href="{{ route('admin.invoices.index') }}" class="btn btn-sm btn-link">View all</a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter table-nowrap card-table mb-0">
                        <thead>
                            <tr>
                                <th>Invoice</th>
                                <th>Contract</th>
                                <th>Status</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($overview['recent_invoices'] as $invoice)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.invoices.show', $invoice) }}" class="text-body fw-bold text-decoration-none">{{ $invoice->invoice_number }}</a>
                                        <div class="small text-muted">{{ $invoice->issue_date?->format('d M Y') ?? '—' }}</div>
                                    </td>
                                    <td class="text-muted">{{ $invoice->contract?->title ?? '—' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $invoice->statusColor() }}-lt">{{ ucfirst($invoice->status) }}</span>
                                    </td>
                                    <td class="text-end">KSh {{ number_format((float) $invoice->total, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-5">No invoices yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
