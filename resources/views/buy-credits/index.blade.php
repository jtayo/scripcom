@extends('layouts.admin')

@section('title', 'Buy Credits')
@section('page-title', 'Buy Credits')
@section('page-subtitle', 'Top up M-Pesa credits for Wi-Fi sessions')

@section('content')
    <div class="row row-cards">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-primary-lt text-primary me-3">
                            <i class="fa-solid fa-coins"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="stat-label text-muted mb-1">Unit Price</div>
                            <div class="stat-value fw-bolder text-body">KES {{ number_format($unitPrice, 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-success-lt text-success me-3">
                            <i class="fa-solid fa-money-bill"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="stat-label text-muted mb-1">Minimum Purchase</div>
                            <div class="stat-value fw-bolder text-body">KES {{ number_format($minPurchase, 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-indigo-lt text-indigo me-3">
                            <i class="fa-solid fa-handshake"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="stat-label text-muted mb-1">Active Sponsorships</div>
                            <div class="stat-value fw-bolder text-body">{{ number_format($balance->count()) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-warning-lt text-warning me-3">
                            <i class="fa-solid fa-hourglass-half"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="stat-label text-muted mb-1">Total Sessions Left</div>
                            <div class="stat-value fw-bolder text-body">{{ number_format($balance->sum(fn ($s) => $s->quantity_purchased - $s->quantity_used)) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-12 col-xl-6">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <span class="avatar avatar-sm bg-primary-lt text-primary me-2">
                            <i class="fa-solid fa-coins"></i>
                        </span>
                        <div>
                            <h2 class="h5 mb-0">Purchase Credits</h2>
                            <div class="small text-muted">An M-Pesa STK push will be sent to the phone number below</div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.buy-credits.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label" for="phone">M-Pesa Phone Number <span class="text-danger">*</span></label>
                            <div class="input-icon">
                                <span class="input-icon-addon"><i class="fa-solid fa-phone"></i></span>
                                <input type="text" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="e.g. 0712345678" required>
                                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="amount">Amount (KES) <span class="text-danger">*</span></label>
                            <div class="input-icon">
                                <span class="input-icon-addon"><i class="fa-solid fa-coins"></i></span>
                                <input type="number" id="amount" name="amount" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount', $minPurchase) }}" min="1" step="1" required>
                                @error('amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="form-text">≈ {{ number_format(max(1, $unitPrice > 0 ? $minPurchase / $unitPrice : 1)) }} sessions at the current unit price.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label d-block" for="sponsorship_id">Top Up Sponsorship</label>
                            <div class="input-icon">
                                <span class="input-icon-addon"><i class="fa-solid fa-handshake"></i></span>
                                <select id="sponsorship_id" name="sponsorship_id" class="form-select @error('sponsorship_id') is-invalid @enderror" style="padding-left: 2.5rem;">
                                    <option value="">General credits (no sponsorship)</option>
                                    @foreach($sponsorships as $sponsorship)
                                        <option value="{{ $sponsorship->id }}" @selected(old('sponsorship_id') == $sponsorship->id)>
                                            {{ $sponsorship->reference }} · {{ $sponsorship->sponsor->name ?? '' }} · {{ number_format($sponsorship->quantity_purchased - $sponsorship->quantity_used) }} left
                                        </option>
                                    @endforeach
                                </select>
                                @error('sponsorship_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="alert alert-info mb-0 small">
                            <i class="fa-solid fa-circle-info me-1"></i>
                            An M-Pesa STK push will be sent to the phone above. Complete the payment to receive credits.
                        </div>

                        <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary d-inline-flex align-items-center">
                                <i class="fa-solid fa-arrow-left me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary d-inline-flex align-items-center">
                                <i class="fa-solid fa-cart-shopping me-2"></i>Pay with M-Pesa
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-6">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <span class="avatar avatar-sm bg-success-lt text-success me-2">
                            <i class="fa-solid fa-handshake"></i>
                        </span>
                        <div>
                            <h2 class="h5 mb-0">Active Sponsorships</h2>
                            <div class="small text-muted">Credits you can top up across your sponsorships</div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table mb-0">
                        <thead>
                            <tr>
                                <th>Reference</th>
                                <th>Sponsor</th>
                                <th>Used</th>
                                <th>Remaining</th>
                                <th class="text-end">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($balance as $sponsorship)
                            <tr>
                                <td><a href="{{ route('admin.sponsorships.show', $sponsorship) }}" class="fw-bold text-body">{{ $sponsorship->reference }}</a></td>
                                <td>{{ $sponsorship->sponsor->name ?? '—' }}</td>
                                <td>{{ number_format($sponsorship->quantity_used) }}</td>
                                <td>{{ number_format($sponsorship->quantity_purchased - $sponsorship->quantity_used) }}</td>
                                <td class="text-end"><span class="badge bg-success-lt">Active</span></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-5">
                                    <div class="my-3">
                                        <i class="fa-solid fa-handshake text-secondary" style="font-size: 2.5rem;"></i>
                                        <div class="mt-2">No active sponsorships.</div>
                                    </div>
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
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .stat-label {
            font-size: .75rem;
            text-transform: uppercase;
            letter-spacing: .04em;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
@endpush
