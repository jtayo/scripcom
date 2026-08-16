@extends('layouts.admin')

@section('title', $contract->title)
@section('page-title', $contract->title)
@section('page-subtitle', $contract->contract_number . ' &middot; ' . $contract->typeLabel() . ' contract')

@section('content')
    <div class="row g-3 mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body d-flex flex-wrap align-items-center gap-3">
                    <span class="avatar avatar-lg bg-{{ $contract->statusColor() }}-lt text-{{ $contract->statusColor() }}">
                        <i class="fa-solid fa-file-contract"></i>
                    </span>
                    <div class="flex-fill">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <h2 class="h4 mb-0">{{ $contract->title }}</h2>
                            <span class="badge bg-{{ $contract->statusColor() }}-lt">{{ ucfirst($contract->status) }}</span>
                            <span class="badge bg-primary-lt">{{ $contract->typeLabel() }}</span>
                        </div>
                        <div class="text-secondary mt-1">
                            {{ $contract->organization?->name ?? '—' }}
                            @if($contract->sponsor) &middot; Sponsored by {{ $contract->sponsor->name }} @endif
                            &middot; {{ $contract->start_date?->format('d M Y') }} — {{ $contract->end_date?->format('d M Y') }}
                        </div>
                    </div>
                    <div class="d-inline-flex gap-2">
                        @can('update-contract')
                            <a href="{{ route('admin.contracts.edit', $contract) }}" class="btn btn-outline-secondary d-inline-flex align-items-center">
                                <i class="fa-solid fa-pen me-2"></i>Edit
                            </a>
                        @endcan
                        <a href="{{ route('admin.contracts.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center">
                            <i class="fa-solid fa-arrow-left me-2"></i>Back
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <div class="text-muted small">Sessions Used</div>
                            <div class="h3 mb-0">{{ number_format($stats['sessions_used']) }}</div>
                            <div class="small text-secondary">of {{ number_format($stats['sessions_allocated']) }} allocated</div>
                        </div>
                        <i class="fa-solid fa-users ms-auto text-secondary"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <div class="text-muted small">Utilization</div>
                            <div class="h3 mb-0">{{ $stats['utilization'] }}%</div>
                            <div class="progress progress-sm mt-2" style="height: 6px;">
                                <div class="progress-bar {{ $stats['utilization'] > 90 ? 'bg-danger' : ($stats['utilization'] > 70 ? 'bg-warning' : 'bg-success') }}"
                                     style="width: {{ min($stats['utilization'], 100) }}%"></div>
                            </div>
                        </div>
                        <i class="fa-solid fa-gauge-high ms-auto text-secondary"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <div class="text-muted small">Contract Value</div>
                            <div class="h3 mb-0">KSh {{ number_format($stats['contract_value'], 2) }}</div>
                            <div class="small text-secondary">{{ number_format($contract->sessions_allocated) }} &times; KSh {{ number_format((float) $contract->unit_price, 2) }}</div>
                        </div>
                        <i class="fa-solid fa-coins ms-auto text-secondary"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <div class="text-muted small">Tax Rate</div>
                            <div class="h3 mb-0">{{ (float) $contract->tax_rate }}%</div>
                            <div class="small text-secondary">{{ $contract->invoices->count() }} invoice(s)</div>
                        </div>
                        <i class="fa-solid fa-percent ms-auto text-secondary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cards">
        <div class="col-12 col-xl-4">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Campaigns</div>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table mb-0">
                        <thead>
                            <tr>
                                <th>Campaign</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($contract->campaigns as $contractCampaign)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.campaigns.show', $contractCampaign->campaign) }}" class="text-body fw-semibold text-decoration-none">{{ $contractCampaign->campaign->title }}</a>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $contractCampaign->campaign->status === 'active' ? 'success' : 'secondary' }}-lt">{{ ucfirst($contractCampaign->campaign->status) }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted py-4">No campaigns attached.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-8">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Invoices</div>
                    <div class="card-actions">
                        @can('create-invoice')
                            <button type="button" class="btn btn-sm btn-primary d-inline-flex align-items-center"
                                data-bs-toggle="modal" data-bs-target="#generateInvoiceModal">
                                <i class="ti ti-file-plus me-1"></i>Generate Invoice
                            </button>
                        @endcan
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter table-nowrap card-table mb-0">
                        <thead>
                            <tr>
                                <th>Invoice</th>
                                <th>Period</th>
                                <th>Status</th>
                                <th class="text-end">Total</th>
                                <th class="text-end">Balance</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($contract->invoices as $invoice)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.invoices.show', $invoice) }}" class="text-body fw-bold text-decoration-none">{{ $invoice->invoice_number }}</a>
                                        <div class="small text-muted">Issued {{ $invoice->issue_date?->format('d M Y') ?? '—' }}</div>
                                    </td>
                                    <td class="text-muted">
                                        {{ $invoice->period_start?->format('d M Y') }} — {{ $invoice->period_end?->format('d M Y') }}
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $invoice->statusColor() }}-lt">{{ ucfirst($invoice->status) }}</span>
                                    </td>
                                    <td class="text-end">KSh {{ number_format((float) $invoice->total, 2) }}</td>
                                    <td class="text-end">KSh {{ number_format($invoice->balanceDue(), 2) }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.invoices.show', $invoice) }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">
                                            <i class="ti ti-eye me-1"></i>View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5">No invoices generated for this contract yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @can('create-invoice')
        <div class="modal modal-blur fade" id="generateInvoiceModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Generate Invoice</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="{{ route('admin.invoices.generate', $contract) }}">
                        @csrf
                        <div class="modal-body">
                            <div class="text-secondary mb-3">Choose the billing period. Sessions served during this period are counted from the contract's campaigns.</div>
                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label" for="from">Period From</label>
                                    <input type="date" id="from" name="from" class="form-control" value="{{ now()->startOfMonth()->toDateString() }}" required>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label" for="to">Period To</label>
                                    <input type="date" id="to" name="to" class="form-control" value="{{ now()->toDateString() }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-link" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary ms-auto">
                                <i class="fa-solid fa-file-invoice me-2"></i>Generate Invoice
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan
@endsection
