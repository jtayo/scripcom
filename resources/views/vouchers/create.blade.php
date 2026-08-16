@extends('layouts.admin')

@section('title', 'New Voucher')
@section('page-title', 'New Voucher')

@section('content')
    <form method="POST" action="{{ route('admin.vouchers.store') }}" id="voucher-form">
        @csrf

        <div class="row">
            <div class="col-12 col-lg-8">
                <div class="card mb-3">
                    <div class="card-header">
                        <div class="card-title">Voucher Configuration</div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label" for="type">Type <span class="text-danger">*</span></label>
                                <select class="form-select @error('type') is-invalid @enderror" name="type" id="type" required>
                                    <option value="">Select type…</option>
                                    @foreach(['sessions', 'hours', 'days', 'bandwidth'] as $type)
                                        <option value="{{ $type }}" @selected(old('type') === $type)>{{ ucfirst($type) }}</option>
                                    @endforeach
                                </select>
                                @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <div class="form-hint">"Days" vouchers can be used once per day (leave Use limit blank).</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="value">Value <span class="text-danger">*</span></label>
                                <input type="number" name="value" id="value"
                                       class="form-control @error('value') is-invalid @enderror"
                                       value="{{ old('value') }}" min="1" step="1" required placeholder="e.g. 1">
                                @error('value')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <div class="form-hint">Sessions / hours / days / data (MB) this voucher grants.</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="count">Number of Vouchers <span class="text-danger">*</span></label>
                                <input type="number" name="count" id="count"
                                       class="form-control @error('count') is-invalid @enderror"
                                       value="{{ old('count', 1) }}" min="1" max="1000" step="1" required>
                                @error('count')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <div class="form-hint">Maximum 1,000 per batch.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="package_id">Wi-Fi Package (recommended)</label>
                                <select class="form-select @error('package_id') is-invalid @enderror" name="package_id" id="package_id">
                                    <option value="">— No package —</option>
                                    @foreach($packages as $package)
                                        <option value="{{ $package->id }}" @selected(old('package_id') == $package->id)>
                                            {{ $package->name }} — {{ $package->priceLabel() }} / {{ $package->durationLabel() }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('package_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <div class="form-hint">When set, the voucher grants this package (its duration/bandwidth) instead of the raw value.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="hotspot_id">Restrict to Hotspot</label>
                                <select class="form-select @error('hotspot_id') is-invalid @enderror" name="hotspot_id" id="hotspot_id">
                                    <option value="">— Any hotspot —</option>
                                    @foreach($hotspots as $hotspot)
                                        <option value="{{ $hotspot->id }}" @selected(old('hotspot_id') == $hotspot->id)>{{ $hotspot->name }}</option>
                                    @endforeach
                                </select>
                                @error('hotspot_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="max_uses">Use Limit</label>
                                <input type="number" name="max_uses" id="max_uses"
                                       class="form-control @error('max_uses') is-invalid @enderror"
                                       value="{{ old('max_uses') }}" min="1" max="1000" step="1"
                                       placeholder="Leave blank = single use">
                                @error('max_uses')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <div class="form-hint">How many times each code can be redeemed (1 = single use).</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="expires_at">Expires At</label>
                                <input type="datetime-local" name="expires_at" id="expires_at"
                                       class="form-control @error('expires_at') is-invalid @enderror"
                                       value="{{ old('expires_at') }}">
                                @error('expires_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="sponsorship_id">Sponsorship</label>
                                <select class="form-select @error('sponsorship_id') is-invalid @enderror" name="sponsorship_id" id="sponsorship_id">
                                    <option value="">— None —</option>
                                    @foreach($sponsorships as $sponsorship)
                                        <option value="{{ $sponsorship->id }}" @selected(old('sponsorship_id') == $sponsorship->id)>{{ $sponsorship->reference }}</option>
                                    @endforeach
                                </select>
                                @error('sponsorship_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Sponsor (optional)</div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label" for="sponsor_id">Sponsor</label>
                                <select class="form-select @error('sponsor_id') is-invalid @enderror" name="sponsor_id" id="sponsor_id">
                                    <option value="">— None —</option>
                                    @foreach($sponsors as $sponsor)
                                        <option value="{{ $sponsor->id }}" @selected(old('sponsor_id') == $sponsor->id)>{{ $sponsor->name }}</option>
                                    @endforeach
                                </select>
                                @error('sponsor_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Summary</div>
                    </div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-5 text-muted">Type</dt>
                            <dd class="col-7" id="summary-type">—</dd>
                            <dt class="col-5 text-muted">Value</dt>
                            <dd class="col-7" id="summary-value">—</dd>
                            <dt class="col-5 text-muted">Package</dt>
                            <dd class="col-7" id="summary-package">—</dd>
                            <dt class="col-5 text-muted">Count</dt>
                            <dd class="col-7" id="summary-count">—</dd>
                            <dt class="col-5 text-muted">Use limit</dt>
                            <dd class="col-7" id="summary-max-uses">Single use</dd>
                        </dl>
                        <hr class="my-3">
                        <button type="submit" class="btn btn-primary w-100 d-inline-flex align-items-center justify-content-center">
                            <i class="ti ti-ticket me-1"></i>Generate Vouchers
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('scripts')
<script>
(function () {
    const packages = @json($packages->pluck('name', 'id'));
    const $ = (id) => document.getElementById(id);
    const summary = {
        type: $('summary-type'),
        value: $('summary-value'),
        package: $('summary-package'),
        count: $('summary-count'),
        maxUses: $('summary-max-uses'),
    };

    const render = () => {
        const type = $('type').value;
        const value = $('value').value;
        const count = $('count').value;
        const maxUses = $('max_uses').value;
        const packageId = $('package_id').value;

        summary.type.textContent = type ? type[0].toUpperCase() + type.slice(1) : '—';
        summary.value.textContent = value ? Number(value).toLocaleString() : '—';
        summary.package.textContent = packageId && packages[packageId] ? packages[packageId] : '—';
        summary.count.textContent = count ? Number(count).toLocaleString() : '—';
        summary.maxUses.textContent = maxUses ? `${Number(maxUses).toLocaleString()} uses each` : 'Single use';
    };

    ['type', 'value', 'count', 'max_uses', 'package_id'].forEach((id) => {
        $(id).addEventListener('input', render);
        $(id).addEventListener('change', render);
    });
    render();
})();
</script>
@endsection
