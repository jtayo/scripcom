@extends('layouts.admin')

@section('title', $sponsorship->reference)
@section('page-title', $sponsorship->reference)

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                    <div>
                        <h1 class="h4 mb-1">{{ $sponsorship->reference }}</h1>
                        <div class="text-muted">
                            <span class="badge bg-secondary">{{ ucfirst($sponsorship->type) }}</span>
                            <span class="ms-2">Sponsor: <strong>{{ $sponsorship->sponsor->name ?? '—' }}</strong></span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-{{ $sponsorship->status === 'active' ? 'success' : ($sponsorship->status === 'pending' ? 'warning' : 'secondary') }} me-2">{{ ucfirst($sponsorship->status) }}</span>
                        @can('update-sponsorship')
                        <a href="{{ route('admin.sponsorships.edit', $sponsorship) }}" class="btn btn-sm btn-primary d-inline-flex align-items-center">Edit</a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-md-6 col-xl-3 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <h3 class="h6 text-muted mb-1">Purchased</h3>
                    <span class="fs-4 fw-bold">{{ number_format($sponsorship->quantity_purchased) }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <h3 class="h6 text-muted mb-1">Used</h3>
                    <span class="fs-4 fw-bold">{{ number_format($sponsorship->quantity_used) }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <h3 class="h6 text-muted mb-1">Remaining</h3>
                    <span class="fs-4 fw-bold">{{ number_format($sponsorship->quantity_purchased - $sponsorship->quantity_used) }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <h3 class="h6 text-muted mb-1">Total Amount</h3>
                    <span class="fs-4 fw-bold">{{ $sponsorship->currency }} {{ number_format($sponsorship->total_amount, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-xl-4 mb-4">
            <div class="card">
                <div class="card-header"><h2 class="h5 mb-0">Details</h2></div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-5 text-muted">Organization</dt>
                        <dd class="col-7">{{ $sponsorship->organization->name ?? '—' }}</dd>
                        <dt class="col-5 text-muted">Unit Price</dt>
                        <dd class="col-7">{{ $sponsorship->currency }} {{ number_format($sponsorship->unit_price, 2) }}</dd>
                        <dt class="col-5 text-muted">Starts</dt>
                        <dd class="col-7">{{ $sponsorship->starts_at?->format('M d, Y H:i') ?? '—' }}</dd>
                        <dt class="col-5 text-muted">Expires</dt>
                        <dd class="col-7">{{ $sponsorship->expires_at?->format('M d, Y H:i') ?? '—' }}</dd>
                        <dt class="col-5 text-muted">Notes</dt>
                        <dd class="col-7">{{ $sponsorship->notes ?? '—' }}</dd>
                    </dl>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header"><h2 class="h5 mb-0">Payments ({{ $sponsorship->payments->count() }})</h2></div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table mb-0">
                        <thead class="">
                            <tr>
                                <th class="border-bottom">Ref</th>
                                <th class="border-bottom">Amount</th>
                                <th class="border-bottom">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sponsorship->payments as $payment)
                            <tr>
                                <td>{{ $payment->transaction_reference ?? $payment->reference ?? '—' }}</td>
                                <td>{{ $payment->currency ?? 'KES' }} {{ number_format($payment->amount, 2) }}</td>
                                <td><span class="badge bg-{{ $payment->status === 'success' ? 'success' : ($payment->status === 'pending' ? 'warning' : 'danger') }}">{{ ucfirst($payment->status) }}</span></td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center text-muted py-4">No payments yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-8">
            <div class="card">
                <div class="card-header"><h2 class="h5 mb-0">Recent Sessions</h2></div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table mb-0">
                        <thead class="">
                            <tr>
                                <th class="border-bottom">Phone</th>
                                <th class="border-bottom">Hotspot</th>
                                <th class="border-bottom">Started</th>
                                <th class="border-bottom">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sponsorship->sessions as $session)
                            <tr>
                                <td class="text-body">{{ $session->phone }}</td>
                                <td>{{ $session->hotspot->name ?? '—' }}</td>
                                <td>{{ $session->session_started_at?->format('M d, H:i') }}</td>
                                <td><span class="badge bg-{{ $session->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($session->status) }}</span></td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">No sessions yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
