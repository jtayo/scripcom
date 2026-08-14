@extends('layouts.admin')

@section('title', "Payment #{$payment->id}")
@section('page-title', "Payment #{$payment->id}")

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                    <div>
                        <h1 class="h4 mb-1">Payment #{{ $payment->id }}</h1>
                        <div class="text-muted">
                            {{ $payment->currency }} {{ number_format($payment->amount, 2) }} · {{ $payment->phone }}
                        </div>
                    </div>
                    <span class="badge bg-{{ $payment->status === 'success' ? 'success' : ($payment->status === 'pending' ? 'warning' : 'danger') }}">{{ ucfirst($payment->status) }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-xl-6 mb-4">
            <div class="card">
                <div class="card-header"><h2 class="h5 mb-0">Details</h2></div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-5 text-muted">Phone</dt>
                        <dd class="col-7">{{ $payment->phone }}</dd>
                        <dt class="col-5 text-muted">Amount</dt>
                        <dd class="col-7">{{ $payment->currency }} {{ number_format($payment->amount, 2) }}</dd>
                        <dt class="col-5 text-muted">Organization</dt>
                        <dd class="col-7">{{ $payment->organization->name ?? '—' }}</dd>
                        <dt class="col-5 text-muted">Sponsorship</dt>
                        <dd class="col-7">@if($payment->sponsorship)<a href="{{ route('admin.sponsorships.show', $payment->sponsorship) }}">{{ $payment->sponsorship->reference }}</a>@else — @endif</dd>
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

        <div class="col-12 col-xl-6 mb-4">
            <div class="card">
                <div class="card-header"><h2 class="h5 mb-0">Callback Payload</h2></div>
                <div class="card-body p-0">
                    <pre class="p-3 mb-0 text-muted" style="max-height: 500px; overflow: auto;">{{ json_encode($payment->callback_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?: '—' }}</pre>
                </div>
            </div>
        </div>
    </div>
@endsection
