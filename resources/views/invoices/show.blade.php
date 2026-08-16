@extends('layouts.admin')

@section('title', $invoice->invoice_number)
@section('page-title', $invoice->invoice_number)
@section('page-subtitle', 'Invoice details and line items')

@section('content')
    <div class="row g-3 mb-3">
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <div class="text-muted small">Total</div>
                            <div class="h3 mb-0">KSh {{ number_format((float) $invoice->total, 2) }}</div>
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
                            <div class="text-muted small">Balance Due</div>
                            <div class="h3 mb-0 text-{{ $invoice->isPaid() ? 'success' : 'danger' }}">KSh {{ number_format($invoice->balanceDue(), 2) }}</div>
                        </div>
                        <i class="fa-solid fa-credit-card ms-auto text-secondary"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <div class="text-muted small">Status</div>
                            <span class="badge bg-{{ $invoice->statusColor() }}-lt mt-1">{{ ucfirst($invoice->status) }}</span>
                        </div>
                        <i class="fa-solid fa-circle-check ms-auto text-secondary"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <div class="text-muted small">Due Date</div>
                            <div class="h3 mb-0">{{ $invoice->due_date?->format('d M Y') ?? '—' }}</div>
                        </div>
                        <i class="fa-solid fa-calendar ms-auto text-secondary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cards">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-12 col-md-6">
                            <div class="text-uppercase small fw-bold text-secondary">Billed To</div>
                            <div class="h4 mt-1 mb-0">{{ $invoice->organization?->name ?? '—' }}</div>
                            @if($invoice->organization?->address)
                                <div class="text-secondary">{{ $invoice->organization->address }}</div>
                            @endif
                            @if($invoice->organization?->phone)
                                <div class="text-secondary">{{ $invoice->organization->phone }}</div>
                            @endif
                            @if($invoice->organization?->email)
                                <div class="text-secondary">{{ $invoice->organization->email }}</div>
                            @endif
                        </div>
                        <div class="col-12 col-md-6 text-md-end">
                            <div class="text-uppercase small fw-bold text-secondary">Details</div>
                            <div class="mt-2">
                                <div><span class="text-secondary">Contract:</span> <a href="{{ route('admin.contracts.show', $invoice->contract) }}">{{ $invoice->contract?->title ?? '—' }}</a></div>
                                <div class="mt-1"><span class="text-secondary">Period:</span> {{ $invoice->period_start?->format('d M Y') }} — {{ $invoice->period_end?->format('d M Y') }}</div>
                                <div class="mt-1"><span class="text-secondary">Issued:</span> {{ $invoice->issue_date?->format('d M Y') ?? '—' }}</div>
                                <div class="mt-1"><span class="text-secondary">Paid at:</span> {{ $invoice->paid_at?->format('d M Y H:i') ?? '—' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive mt-4">
                        <table class="table table-vcenter card-table mb-0">
                            <thead>
                                <tr>
                                    <th>Description</th>
                                    <th class="text-end">Quantity</th>
                                    <th class="text-end">Unit Price</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($invoice->items as $item)
                                    <tr>
                                        <td>{{ $item->description }}</td>
                                        <td class="text-end">{{ number_format($item->quantity) }}</td>
                                        <td class="text-end">KSh {{ number_format((float) $item->unit_price, 2) }}</td>
                                        <td class="text-end">KSh {{ number_format((float) $item->amount, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">No line items.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end fw-semibold">Subtotal</td>
                                    <td class="text-end">KSh {{ number_format((float) $invoice->subtotal, 2) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end fw-semibold">Tax ({{ (float) $invoice->tax_rate }}%)</td>
                                    <td class="text-end">KSh {{ number_format((float) $invoice->tax_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end fw-bold">Total</td>
                                    <td class="text-end fw-bold">KSh {{ number_format((float) $invoice->total, 2) }}</td>
                                </tr>
                                @unless($invoice->isPaid())
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold text-danger">Balance Due</td>
                                        <td class="text-end fw-bold text-danger">KSh {{ number_format($invoice->balanceDue(), 2) }}</td>
                                    </tr>
                                @else
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold text-success">Paid</td>
                                        <td class="text-end fw-bold text-success">KSh {{ number_format((float) $invoice->amount_paid, 2) }}</td>
                                    </tr>
                                @endunless
                            </tfoot>
                        </table>
                    </div>

                    @if($invoice->notes)
                        <div class="alert alert-secondary mt-4 mb-0">
                            <i class="fa-solid fa-circle-info me-1"></i>{{ $invoice->notes }}
                        </div>
                    @endif

                    @can('update-invoice')
                        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                            @unless($invoice->isPaid())
                                <form method="POST" action="{{ route('admin.invoices.mark-paid', $invoice) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success d-inline-flex align-items-center" onclick="return confirm('Mark this invoice as fully paid?');">
                                        <i class="fa-solid fa-circle-check me-2"></i>Mark as Paid
                                    </button>
                                </form>
                            @endunless
                            @unless(in_array($invoice->status, ['paid', 'cancelled']))
                                <form method="POST" action="{{ route('admin.invoices.cancel', $invoice) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger d-inline-flex align-items-center" onclick="return confirm('Cancel this invoice?');">
                                        <i class="fa-solid fa-ban me-2"></i>Cancel Invoice
                                    </button>
                                </form>
                            @endunless
                        </div>
                    @endcan
                </div>
            </div>
        </div>
    </div>
@endsection
