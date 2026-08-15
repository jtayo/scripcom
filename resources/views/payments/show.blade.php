@extends('layouts.admin')

@section('title', "Payment #{$payment->id}")
@section('page-title', "Payment #{$payment->id}")

@section('content')
    <div class="mb-3">
        <a href="{{ route('admin.payments.index') }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">
            <i class="fa-solid fa-arrow-left me-1"></i>Back to Payments
        </a>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                    <div class="d-flex align-items-center">
                        <span class="avatar avatar-lg bg-primary-lt text-primary me-3">
                            <i class="fa-solid fa-credit-card"></i>
                        </span>
                        <div>
                            <h1 class="h4 mb-1">Payment #{{ $payment->id }}</h1>
                            <div class="text-muted d-flex align-items-center flex-wrap">
                                <span class="badge bg-secondary-lt me-2">{{ $payment->currency }} {{ number_format($payment->amount, 2) }}</span>
                                <span class="d-inline-flex align-items-center"><i class="fa-solid fa-phone me-1 text-secondary"></i>{{ $payment->phone }}</span>
                                @if($payment->organization)
                                <span class="d-inline-flex align-items-center ms-2"><i class="fa-solid fa-building me-1 text-secondary"></i>{{ $payment->organization->name }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @php $statusColor = match($payment->status) { 'success' => 'success', 'pending' => 'warning', 'failed' => 'danger', 'cancelled' => 'danger', default => 'secondary' }; @endphp
                    <div class="d-flex align-items-center mt-3 mt-md-0">
                        <span class="badge bg-{{ $statusColor }}-lt">{{ ucfirst($payment->status) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cards">
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-primary-lt text-primary me-3">
                            <i class="fa-solid fa-money-bill-wave"></i>
                        </div>
                        <div>
                            <div class="stat-label text-muted mb-1">Amount</div>
                            <div class="stat-value fw-bolder text-body">{{ $payment->currency }} {{ number_format($payment->amount, 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-info-lt text-info me-3">
                            <i class="fa-solid fa-mobile-screen"></i>
                        </div>
                        <div>
                            <div class="stat-label text-muted mb-1">Method</div>
                            <div class="stat-value fw-bolder text-body">M-Pesa</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-{{ $statusColor }}-lt text-{{ $statusColor }} me-3">
                            <i class="fa-solid {{ $payment->status === 'success' ? 'fa-circle-check' : 'fa-circle-xmark' }}"></i>
                        </div>
                        <div>
                            <div class="stat-label text-muted mb-1">Status</div>
                            <div class="stat-value fw-bolder text-body">{{ ucfirst($payment->status) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-warning-lt text-warning me-3">
                            <i class="fa-solid fa-receipt"></i>
                        </div>
                        <div>
                            <div class="stat-label text-muted mb-1">Receipt</div>
                            <div class="stat-value fw-bolder text-body">{{ $payment->mpesa_receipt_number ?? '—' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cards">
        <div class="col-12 col-xl-6">
            <div class="card dashboard-card h-100">
                <div class="card-header">
                    <h2 class="card-title mb-0">
                        <i class="fa-solid fa-circle-info text-primary me-2"></i>Details
                    </h2>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-5 text-muted">Reference</dt>
                        <dd class="col-7 fw-bold">#{{ $payment->id }}</dd>
                        <dt class="col-5 text-muted">Phone</dt>
                        <dd class="col-7">{{ $payment->phone }}</dd>
                        <dt class="col-5 text-muted">Organization</dt>
                        <dd class="col-7">{{ $payment->organization->name ?? '—' }}</dd>
                        <dt class="col-5 text-muted">Sponsorship</dt>
                        <dd class="col-7">@if($payment->sponsorship)<a href="{{ route('admin.sponsorships.show', $payment->sponsorship) }}">{{ $payment->sponsorship->reference }}</a>@else — @endif</dd>
                        <dt class="col-5 text-muted">Amount</dt>
                        <dd class="col-7 fw-bold">{{ $payment->currency }} {{ number_format($payment->amount, 2) }}</dd>
                        <dt class="col-5 text-muted">Method</dt>
                        <dd class="col-7">M-Pesa</dd>
                        <dt class="col-5 text-muted">Status</dt>
                        <dd class="col-7"><span class="badge bg-{{ $statusColor }}-lt">{{ ucfirst($payment->status) }}</span></dd>
                        <dt class="col-5 text-muted">Checkout Request ID</dt>
                        <dd class="col-7 text-break">{{ $payment->checkout_request_id ?? '—' }}</dd>
                        <dt class="col-5 text-muted">M-Pesa Receipt</dt>
                        <dd class="col-7">{{ $payment->mpesa_receipt_number ?? '—' }}</dd>
                        <dt class="col-5 text-muted">Transaction ID</dt>
                        <dd class="col-7 text-break">{{ $payment->transaction_id ?? '—' }}</dd>
                        <dt class="col-5 text-muted">Result Code</dt>
                        <dd class="col-7">{{ $payment->result_code ?? '—' }}</dd>
                        <dt class="col-5 text-muted">Result Description</dt>
                        <dd class="col-7">{{ $payment->result_description ?? '—' }}</dd>
                        <dt class="col-5 text-muted">Transacted At</dt>
                        <dd class="col-7">{{ $payment->transacted_at?->format('M d, Y H:i') ?? '—' }}</dd>
                        <dt class="col-5 text-muted">Created</dt>
                        <dd class="col-7">{{ $payment->created_at?->format('M d, Y H:i') }}</dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-6">
            <div class="card dashboard-card h-100">
                <div class="card-header">
                    <h2 class="card-title mb-0">
                        <i class="fa-solid fa-braces text-primary me-2"></i>Callback Payload
                    </h2>
                </div>
                <div class="card-body p-0">
                    <pre class="p-3 mb-0 text-muted" style="max-height: 500px; overflow: auto;">{{ json_encode($payment->callback_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?: '—' }}</pre>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .stat-card {
            transition: transform .2s ease, box-shadow .2s ease;
            height: 100%;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 1rem 2rem rgba(17, 24, 39, .08) !important;
        }

        .stat-icon {
            width: 3rem;
            height: 3rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: .75rem;
            font-size: 1.4rem;
            flex-shrink: 0;
        }

        .stat-value {
            font-size: 1.5rem;
            line-height: 1.15;
        }

        .stat-label {
            font-size: .75rem;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .dashboard-card .card-header {
            background: transparent;
            border-bottom: 1px solid var(--tblr-border-color);
            padding: .9rem 1.25rem;
            min-height: 0;
        }
    </style>
@endpush
