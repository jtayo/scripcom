@extends('layouts.admin')

@section('title', 'Buy Credits')
@section('page-title', 'Buy Credits')

@section('content')
    <div class="row">
        <div class="col-12 col-md-6 col-xl-3 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <h3 class="h6 text-muted mb-1">Unit Price</h3>
                    <span class="fs-4 fw-bold">KES {{ number_format($unitPrice, 2) }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <h3 class="h6 text-muted mb-1">Minimum Purchase</h3>
                    <span class="fs-4 fw-bold">KES {{ number_format($minPurchase, 2) }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <h3 class="h6 text-muted mb-1">Active Sponsorships</h3>
                    <span class="fs-4 fw-bold">{{ number_format($balance->count()) }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <h3 class="h6 text-muted mb-1">Total Sessions Left</h3>
                    <span class="fs-4 fw-bold">{{ number_format($balance->sum(fn ($s) => $s->quantity_purchased - $s->quantity_used)) }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-xl-6 mb-4">
            <div class="card">
                <div class="card-header"><h2 class="h5 mb-0">Purchase Credits</h2></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.buy-credits.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label" for="phone">M-Pesa Phone Number <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><svg class="icon text-secondary" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path></svg></span>
                                <input type="text" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="e.g. 0712345678" required>
                            </div>
                            @error('phone') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="amount">Amount (KES) <span class="text-danger">*</span></label>
                            <input type="number" id="amount" name="amount" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount', $minPurchase) }}" min="1" step="1" required>
                            @error('amount') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            <div class="form-text">≈ {{ number_format(max(1, $unitPrice > 0 ? $minPurchase / $unitPrice : 1)) }} sessions at the current unit price.</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label d-block" for="sponsorship_id">Top Up Sponsorship</label>
                            <select id="sponsorship_id" name="sponsorship_id" class="form-select @error('sponsorship_id') is-invalid @enderror">
                                <option value="">General credits (no sponsorship)</option>
                                @foreach($sponsorships as $sponsorship)
                                    <option value="{{ $sponsorship->id }}" @selected(old('sponsorship_id') == $sponsorship->id)>
                                        {{ $sponsorship->reference }} · {{ $sponsorship->sponsor->name ?? '' }} · {{ number_format($sponsorship->quantity_purchased - $sponsorship->quantity_used) }} left
                                    </option>
                                @endforeach
                            </select>
                            @error('sponsorship_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <div class="alert alert-info mb-0 small">
                                An M-Pesa STK push will be sent to the phone above. Complete the payment to receive credits.
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 d-inline-flex align-items-center justify-content-center">
                            <svg class="icon me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h8V3a1 1 0 112 0v1h1a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2V6a2 2 0 012-2h1V3a1 1 0 011-1zm10 4H5v11h10V6z" clip-rule="evenodd"></path></svg>
                            Pay with M-Pesa
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-6">
            <div class="card">
                <div class="card-header"><h2 class="h5 mb-0">Active Sponsorships</h2></div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table mb-0">
                        <thead class="">
                            <tr>
                                <th class="border-bottom">Reference</th>
                                <th class="border-bottom">Sponsor</th>
                                <th class="border-bottom">Used</th>
                                <th class="border-bottom">Remaining</th>
                                <th class="border-bottom text-end">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($balance as $sponsorship)
                            <tr>
                                <td><a href="{{ route('admin.sponsorships.show', $sponsorship) }}" class="fw-bold text-body">{{ $sponsorship->reference }}</a></td>
                                <td>{{ $sponsorship->sponsor->name ?? '—' }}</td>
                                <td>{{ number_format($sponsorship->quantity_used) }}</td>
                                <td>{{ number_format($sponsorship->quantity_purchased - $sponsorship->quantity_used) }}</td>
                                <td class="text-end"><span class="badge bg-success">Active</span></td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">No active sponsorships.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
