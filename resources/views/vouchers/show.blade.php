@extends('layouts.admin')

@section('title', $voucher->code)
@section('page-title', $voucher->code)

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                    <div>
                        <h1 class="h4 mb-1 font-monospace">{{ $voucher->code }}</h1>
                        <div class="text-muted small">Batch: {{ $voucher->batch_id }}</div>
                    </div>
                    <div class="d-flex align-items-center">
                        @if($voucher->isExpired() && $voucher->status === 'unused')
                            <span class="badge bg-warning me-2">Expired</span>
                        @else
                            <span class="badge bg-{{ $voucher->status === 'unused' ? 'success' : ($voucher->status === 'redeemed' ? 'secondary' : 'danger') }} me-2">{{ ucfirst($voucher->status) }}</span>
                        @endif
                        @can('delete-voucher')
                        <form method="POST" action="{{ route('admin.vouchers.destroy', $voucher) }}" class="d-inline" onsubmit="return confirm('Delete this voucher?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger d-inline-flex align-items-center">Delete</button>
                        </form>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-xl-8">
            <div class="card">
                <div class="card-header"><h2 class="h5 mb-0">Details</h2></div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-4 text-muted">Type</dt>
                        <dd class="col-8"><span class="badge bg-secondary">{{ ucfirst($voucher->type) }}</span></dd>
                        <dt class="col-4 text-muted">Value</dt>
                        <dd class="col-8">{{ number_format($voucher->value) }} {{ $voucher->type }}</dd>
                        <dt class="col-4 text-muted">Sponsor</dt>
                        <dd class="col-8">@if($voucher->sponsor)<a href="{{ route('admin.sponsors.show', $voucher->sponsor) }}">{{ $voucher->sponsor->name }}</a>@else — @endif</dd>
                        <dt class="col-4 text-muted">Sponsorship</dt>
                        <dd class="col-8">@if($voucher->sponsorship)<a href="{{ route('admin.sponsorships.show', $voucher->sponsorship) }}">{{ $voucher->sponsorship->reference }}</a>@else — @endif</dd>
                        <dt class="col-4 text-muted">Hotspot</dt>
                        <dd class="col-8">@if($voucher->hotspot)<a href="{{ route('admin.hotspots.show', $voucher->hotspot) }}">{{ $voucher->hotspot->name }}</a>@else — @endif</dd>
                        <dt class="col-4 text-muted">Expires</dt>
                        <dd class="col-8">{{ $voucher->expires_at?->format('M d, Y H:i') ?? 'Never' }}</dd>
                        <dt class="col-4 text-muted">Created</dt>
                        <dd class="col-8">{{ $voucher->created_at?->format('M d, Y H:i') ?? '—' }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-4">
            <div class="card">
                <div class="card-header"><h2 class="h5 mb-0">Redemption</h2></div>
                <div class="card-body">
                    @if($voucher->status === 'redeemed')
                    <dl class="row mb-0">
                        <dt class="col-5 text-muted">Phone</dt>
                        <dd class="col-7">{{ $voucher->redeemed_phone }}</dd>
                        <dt class="col-5 text-muted">Redeemed At</dt>
                        <dd class="col-7">{{ $voucher->redeemed_at?->format('M d, Y H:i') }}</dd>
                        <dt class="col-5 text-muted">Session</dt>
                        <dd class="col-7">@if($voucher->session)<a href="{{ route('admin.sessions.show', $voucher->session) }}">{{ $voucher->session->session_id }}</a>@else — @endif</dd>
                    </dl>
                    @else
                    <p class="text-muted mb-0">This voucher has not been redeemed yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
