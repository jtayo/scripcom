@extends('layouts.admin')

@section('title', $voucher->code)
@section('page-title', $voucher->code)

@section('content')
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

    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                    <div class="d-flex align-items-center">
                        <span class="avatar avatar-lg bg-primary-lt text-primary me-3">
                            <i class="fa-solid fa-ticket"></i>
                        </span>
                        <div>
                            <h1 class="h4 mb-1 font-monospace">{{ $voucher->code }}</h1>
                            <div class="text-muted d-flex align-items-center flex-wrap gap-2">
                                <span class="badge bg-secondary-lt">{{ ucfirst($voucher->type) }}</span>
                                <span class="d-inline-flex align-items-center"><i class="fa-solid fa-layers me-1 text-secondary"></i>Batch <strong class="ms-1">{{ $voucher->batch_id }}</strong></span>
                                @if($voucher->sponsor)
                                    <span class="d-inline-flex align-items-center"><i class="fa-solid fa-handshake me-1 text-secondary"></i>Sponsored by <strong class="ms-1">{{ $voucher->sponsor->name }}</strong></span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mt-3 mt-md-0">
                        <span class="badge bg-{{ $statusColor }}-lt me-2">
                            @if($voucher->status === 'redeemed')
                            <span class="status-dot status-dot-animated me-2"></span>
                            @endif
                            {{ $expiredUnused ? 'Expired' : ucfirst($voucher->status) }}
                        </span>
                        @can('delete-voucher')
                        <form method="POST" action="{{ route('admin.vouchers.destroy', $voucher) }}" class="d-inline" onsubmit="return confirm('Delete this voucher?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger d-inline-flex align-items-center">
                                <i class="fa-solid fa-trash me-1"></i>Delete
                            </button>
                        </form>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cards">
        <div class="col-12 col-md-4">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-primary-lt text-primary me-3">
                            <i class="fa-solid fa-money-bill"></i>
                        </div>
                        <div>
                            <div class="stat-label text-muted mb-1">Value</div>
                            <div class="stat-value fw-bolder text-body">{{ number_format($voucher->value) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-success-lt text-success me-3">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                        <div>
                            <div class="stat-label text-muted mb-1">Status</div>
                            <div class="stat-value fw-bolder text-body">{{ $expiredUnused ? 'Expired' : ucfirst($voucher->status) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-warning-lt text-warning me-3">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                        <div>
                            <div class="stat-label text-muted mb-1">Expires</div>
                            <div class="stat-value fw-bolder text-body">{{ $voucher->expires_at?->format('M d, Y') ?? 'Never' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cards">
        <div class="col-12 col-xl-8">
            <div class="card dashboard-card h-100">
                <div class="card-header">
                    <h2 class="card-title mb-0">
                        <i class="fa-solid fa-circle-info text-primary me-2"></i>Details
                    </h2>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-5 text-muted">Code</dt>
                        <dd class="col-7 font-monospace fw-bold">{{ $voucher->code }}</dd>
                        <dt class="col-5 text-muted">Type</dt>
                        <dd class="col-7"><span class="badge bg-secondary-lt">{{ ucfirst($voucher->type) }}</span></dd>
                        <dt class="col-5 text-muted">Value</dt>
                        <dd class="col-7">{{ number_format($voucher->value) }} {{ $voucher->type }}</dd>
                        <dt class="col-5 text-muted">Batch</dt>
                        <dd class="col-7">{{ $voucher->batch_id }}</dd>
                        <dt class="col-5 text-muted">Sponsor</dt>
                        <dd class="col-7">
                            @if($voucher->sponsor)
                            <span class="d-inline-flex align-items-center">
                                <i class="fa-solid fa-handshake me-1 text-secondary"></i>
                                <a href="{{ route('admin.sponsors.show', $voucher->sponsor) }}">{{ $voucher->sponsor->name }}</a>
                            </span>
                            @else — @endif
                        </dd>
                        <dt class="col-5 text-muted">Sponsorship</dt>
                        <dd class="col-7">@if($voucher->sponsorship)<a href="{{ route('admin.sponsorships.show', $voucher->sponsorship) }}">{{ $voucher->sponsorship->reference }}</a>@else — @endif</dd>
                        <dt class="col-5 text-muted">Hotspot</dt>
                        <dd class="col-7">@if($voucher->hotspot)<a href="{{ route('admin.hotspots.show', $voucher->hotspot) }}">{{ $voucher->hotspot->name }}</a>@else — @endif</dd>
                        <dt class="col-5 text-muted">Status</dt>
                        <dd class="col-7">
                            <span class="badge bg-{{ $statusColor }}-lt">
                                @if($voucher->status === 'redeemed')
                                <span class="status-dot status-dot-animated me-2"></span>
                                @endif
                                {{ $expiredUnused ? 'Expired' : ucfirst($voucher->status) }}
                            </span>
                        </dd>
                        <dt class="col-5 text-muted">Issued</dt>
                        <dd class="col-7">{{ $voucher->created_at?->format('M d, Y H:i') ?? '—' }}</dd>
                        <dt class="col-5 text-muted">Redeemed At</dt>
                        <dd class="col-7">@if($voucher->redeemed_at){{ $voucher->redeemed_at->format('M d, Y H:i') }}@else — @endif</dd>
                        <dt class="col-5 text-muted">Expires</dt>
                        <dd class="col-7">{{ $voucher->expires_at?->format('M d, Y H:i') ?? 'Never' }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-4">
            <div class="card dashboard-card h-100">
                <div class="card-header">
                    <h2 class="card-title mb-0">
                        <i class="fa-solid fa-circle-check text-primary me-2"></i>Redemption
                    </h2>
                </div>
                <div class="card-body">
                    @if($voucher->status === 'redeemed')
                    <dl class="row mb-0">
                        <dt class="col-5 text-muted">Phone</dt>
                        <dd class="col-7">
                            <span class="d-inline-flex align-items-center">
                                <i class="fa-solid fa-mobile-screen me-1 text-secondary"></i>
                                {{ $voucher->redeemed_phone }}
                            </span>
                        </dd>
                        <dt class="col-5 text-muted">Redeemed At</dt>
                        <dd class="col-7">{{ $voucher->redeemed_at?->format('M d, Y H:i') }}</dd>
                        <dt class="col-5 text-muted">Session</dt>
                        <dd class="col-7">@if($voucher->session)<a href="{{ route('admin.sessions.show', $voucher->session) }}">{{ $voucher->session->session_id }}</a>@else — @endif</dd>
                    </dl>
                    @else
                    <p class="text-muted mb-0"><i class="fa-solid fa-circle-info me-1 text-secondary"></i>This voucher has not been redeemed yet.</p>
                    @endif
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
