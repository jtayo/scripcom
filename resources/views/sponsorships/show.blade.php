@extends('layouts.admin')

@section('title', $sponsorship->reference)
@section('page-title', $sponsorship->reference)

@section('content')
    <div class="mb-3">
        <a href="{{ route('admin.sponsorships.index') }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">
            <i class="fa-solid fa-arrow-left me-1"></i>Back to Sponsorships
        </a>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                    <div class="d-flex align-items-center">
                        <span class="avatar avatar-lg bg-primary-lt text-primary me-3">
                            <i class="fa-solid fa-handshake"></i>
                        </span>
                        <div>
                            <h1 class="h4 mb-1">{{ $sponsorship->reference }}</h1>
                            <div class="text-muted d-flex align-items-center flex-wrap">
                                <span class="badge bg-secondary-lt me-2">{{ ucfirst($sponsorship->type) }}</span>
                                @if($sponsorship->sponsor)
                                    <span class="d-inline-flex align-items-center"><i class="fa-solid fa-building me-1 text-secondary"></i>Sponsor: <a href="{{ route('admin.sponsors.show', $sponsorship->sponsor) }}" class="text-body ms-1"><strong>{{ $sponsorship->sponsor->name }}</strong></a></span>
                                @else
                                    <span class="d-inline-flex align-items-center"><i class="fa-solid fa-building me-1 text-secondary"></i>Sponsor: <strong class="ms-1">—</strong></span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mt-3 mt-md-0">
                        @php
                            $statusColor = match($sponsorship->status) {
                                'active' => 'success',
                                'pending' => 'warning',
                                'expired' => 'secondary',
                                default => 'danger',
                            };
                        @endphp
                        <span class="badge bg-{{ $statusColor }}-lt me-2">{{ ucfirst($sponsorship->status) }}</span>
                        @can('update-sponsorship')
                        <a href="{{ route('admin.sponsorships.edit', $sponsorship) }}" class="btn btn-sm btn-primary d-inline-flex align-items-center">
                            <i class="fa-solid fa-pen me-1"></i>Edit
                        </a>
                        @endcan
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
                            <i class="fa-solid fa-cart-shopping"></i>
                        </div>
                        <div>
                            <div class="stat-label text-muted mb-1">Purchased</div>
                            <div class="stat-value fw-bolder text-body">{{ number_format($sponsorship->quantity_purchased) }}</div>
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
                            <i class="fa-solid fa-play"></i>
                        </div>
                        <div>
                            <div class="stat-label text-muted mb-1">Used</div>
                            <div class="stat-value fw-bolder text-body">{{ number_format($sponsorship->quantity_used) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-success-lt text-success me-3">
                            <i class="fa-solid fa-hourglass-half"></i>
                        </div>
                        <div>
                            <div class="stat-label text-muted mb-1">Remaining</div>
                            <div class="stat-value fw-bolder text-body">{{ number_format($sponsorship->quantity_purchased - $sponsorship->quantity_used) }}</div>
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
                            <i class="fa-solid fa-money-bill"></i>
                        </div>
                        <div>
                            <div class="stat-label text-muted mb-1">Total Amount</div>
                            <div class="stat-value fw-bolder text-body">{{ $sponsorship->currency }} {{ number_format($sponsorship->total_amount, 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cards">
        <div class="col-12 col-xl-4">
            <div class="card dashboard-card">
                <div class="card-header">
                    <h2 class="card-title mb-0">
                        <i class="fa-solid fa-circle-info text-primary me-2"></i>Details
                    </h2>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-5 text-muted">Organization</dt>
                        <dd class="col-7">
                            <span class="d-inline-flex align-items-center">
                                <i class="fa-solid fa-building me-1 text-secondary"></i>
                                {{ $sponsorship->organization->name ?? '—' }}
                            </span>
                        </dd>
                        <dt class="col-5 text-muted">Sponsor</dt>
                        <dd class="col-7">
                            @if($sponsorship->sponsor)
                            <span class="d-inline-flex align-items-center">
                                <i class="fa-solid fa-handshake me-1 text-secondary"></i>
                                <a href="{{ route('admin.sponsors.show', $sponsorship->sponsor) }}" class="text-body">{{ $sponsorship->sponsor->name }}</a>
                            </span>
                            @else
                            —
                            @endif
                        </dd>
                        <dt class="col-5 text-muted">Reference</dt>
                        <dd class="col-7">{{ $sponsorship->reference }}</dd>
                        <dt class="col-5 text-muted">Type</dt>
                        <dd class="col-7">{{ ucfirst($sponsorship->type) }}</dd>
                        <dt class="col-5 text-muted">Unit Price</dt>
                        <dd class="col-7">{{ $sponsorship->currency }} {{ number_format($sponsorship->unit_price, 2) }}</dd>
                        <dt class="col-5 text-muted">Starts</dt>
                        <dd class="col-7">{{ $sponsorship->starts_at?->format('M d, Y H:i') ?? '—' }}</dd>
                        <dt class="col-5 text-muted">Expires</dt>
                        <dd class="col-7">{{ $sponsorship->expires_at?->format('M d, Y H:i') ?? '—' }}</dd>
                        <dt class="col-5 text-muted">Status</dt>
                        <dd class="col-7">
                            <span class="badge bg-{{ $statusColor }}-lt">{{ ucfirst($sponsorship->status) }}</span>
                        </dd>
                        <dt class="col-5 text-muted">Notes</dt>
                        <dd class="col-7">{{ $sponsorship->notes ?? '—' }}</dd>
                    </dl>
                </div>
            </div>

            <div class="card dashboard-card mt-4">
                <div class="card-header">
                    <h2 class="card-title mb-0">
                        <i class="fa-solid fa-credit-card text-primary me-2"></i>Payments ({{ $sponsorship->payments->count() }})
                    </h2>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter table-nowrap card-table mb-0">
                        <thead>
                            <tr>
                                <th>Ref</th>
                                <th class="text-end">Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sponsorship->payments as $payment)
                            @php
                                $paymentColor = match($payment->status) {
                                    'success' => 'success',
                                    'pending' => 'warning',
                                    default => 'danger',
                                };
                            @endphp
                            <tr>
                                <td>{{ $payment->transaction_reference ?? $payment->reference ?? '—' }}</td>
                                <td class="text-end">{{ $payment->currency ?? 'KES' }} {{ number_format($payment->amount, 2) }}</td>
                                <td>
                                    <span class="badge bg-{{ $paymentColor }}-lt">
                                        <span class="status-dot @if($payment->status === 'success') status-dot-animated @endif me-1"></span>
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">
                                    <i class="fa-solid fa-credit-card text-secondary mb-1 d-block" style="font-size: 1.5rem;"></i>
                                    No payments yet.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-8">
            <div class="card dashboard-card h-100">
                <div class="card-header">
                    <h2 class="card-title mb-0">
                        <i class="fa-solid fa-tower-broadcast text-primary me-2"></i>Recent Sessions
                    </h2>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter table-nowrap card-table mb-0">
                        <thead>
                            <tr>
                                <th>Phone</th>
                                <th>Hotspot</th>
                                <th>Started</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sponsorship->sessions as $session)
                            <tr>
                                <td class="text-body">{{ $session->phone }}</td>
                                <td>{{ $session->hotspot->name ?? '—' }}</td>
                                <td class="small text-muted">{{ $session->session_started_at?->format('M d, Y H:i') }}</td>
                                <td>
                                    <span class="badge bg-{{ $session->status === 'active' ? 'success' : 'secondary' }}-lt">
                                        <span class="status-dot @if($session->status === 'active') status-dot-animated @endif me-1"></span>
                                        {{ ucfirst($session->status) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    <i class="fa-solid fa-wifi text-secondary mb-1 d-block" style="font-size: 1.5rem;"></i>
                                    No sessions yet.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
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
