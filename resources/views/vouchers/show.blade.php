@extends('layouts.admin')

@section('title', 'Voucher ' . $voucher->code)
@section('page-title', 'Voucher Details')

@section('content')
    @php
        $expiredUnused = $voucher->isExpired() && $voucher->status === 'unused';
        $statusColor = $expiredUnused ? 'danger'
            : match($voucher->status) {
                'unused' => 'warning',
                'used' => 'success',
                'expired' => 'danger',
                'revoked' => 'secondary',
                default => 'dark',
            };
    @endphp

    <div class="row">
        <div class="col-12 col-lg-8">
            <div class="card mb-3">
                <div class="card-header">
                    <div class="card-title">
                        Voucher
                        <span class="badge bg-{{ $statusColor }}-lt ms-2">
                            {{ $expiredUnused ? 'Expired' : ucfirst($voucher->status) }}
                        </span>
                    </div>
                    <div class="card-actions">
                        @can('delete-voucher')
                        <form method="POST" action="{{ route('admin.vouchers.destroy', $voucher) }}" class="d-inline" onsubmit="return confirm('Delete this voucher?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger d-inline-flex align-items-center">
                                <i class="ti ti-trash me-1"></i>Delete
                            </button>
                        </form>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between bg-light rounded p-3 mb-3">
                        <code class="font-monospace fs-3 fw-bold" id="voucher-code">{{ $voucher->code }}</code>
                        <div class="d-flex gap-1">
                            <button type="button" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center" id="btn-copy">
                                <i class="ti ti-copy me-1"></i>Copy
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center" id="btn-print">
                                <i class="ti ti-printer me-1"></i>Print
                            </button>
                        </div>
                    </div>

                    <dl class="row mb-0">
                        <dt class="col-4 text-muted">Batch</dt>
                        <dd class="col-8">
                            <span class="d-inline-flex align-items-center">
                                <i class="ti ti-layers-intersect me-1 text-secondary"></i>{{ $voucher->batch_id }}
                            </span>
                        </dd>

                        <dt class="col-4 text-muted">Type</dt>
                        <dd class="col-8"><span class="badge bg-secondary-lt">{{ ucfirst($voucher->type) }}</span></dd>

                        <dt class="col-4 text-muted">Value</dt>
                        <dd class="col-8">
                            <span class="d-inline-flex align-items-center">
                                <i class="ti ti-coins me-1 text-secondary"></i>{{ number_format($voucher->value) }}
                            </span>
                        </dd>

                        <dt class="col-4 text-muted">Package</dt>
                        <dd class="col-8">
                            @if($voucher->package)
                                <a href="{{ route('admin.packages.show', $voucher->package) }}" class="text-body text-decoration-none d-inline-flex align-items-center">
                                    <i class="ti ti-wifi me-1 text-secondary"></i>{{ $voucher->package->name }}
                                </a>
                                <div class="small text-muted">{{ $voucher->package->priceLabel() }} / {{ $voucher->package->durationLabel() }}</div>
                            @else
                                <span class="text-secondary">—</span>
                            @endif
                        </dd>

                        <dt class="col-4 text-muted">Use Limit</dt>
                        <dd class="col-8">
                            @if($voucher->max_uses)
                                <span class="text-muted">{{ $voucher->used_count }} / {{ $voucher->max_uses }} uses</span>
                            @else
                                <span class="text-secondary">Single use</span>
                            @endif
                        </dd>

                        <dt class="col-4 text-muted">Expires At</dt>
                        <dd class="col-8">
                            @if($voucher->expires_at)
                                {{ $voucher->expires_at->format('M j, Y H:i') }}
                            @else
                                <span class="text-secondary">Never</span>
                            @endif
                        </dd>

                        <dt class="col-4 text-muted">Sponsor</dt>
                        <dd class="col-8">{{ $voucher->sponsor->name ?? '—' }}</dd>

                        <dt class="col-4 text-muted">Sponsorship</dt>
                        <dd class="col-8">{{ $voucher->sponsorship->reference ?? '—' }}</dd>

                        <dt class="col-4 text-muted">Hotspot</dt>
                        <dd class="col-8">{{ $voucher->hotspot->name ?? '— (Any hotspot)' }}</dd>

                        <dt class="col-4 text-muted">Created By</dt>
                        <dd class="col-8">
                            @if($voucher->creator)
                                {{ $voucher->creator->name }}
                            @else
                                <span class="text-secondary">System</span>
                            @endif
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="card mb-3">
                <div class="card-header">
                    <div class="card-title">Redemption</div>
                </div>
                <div class="card-body">
                    @if($voucher->redeemed_at)
                        <div class="mb-3">
                            <div class="small text-muted">Redeemed</div>
                            <div class="fw-bold">{{ $voucher->redeemed_at->format('M j, Y H:i') }}</div>
                        </div>
                        @if($voucher->redeemed_phone)
                        <div class="mb-3">
                            <div class="small text-muted">Phone</div>
                            <div class="fw-bold">{{ $voucher->redeemed_phone }}</div>
                        </div>
                        @endif
                        @if($voucher->session)
                        <div class="mb-3">
                            <div class="small text-muted">Session</div>
                            <a href="{{ route('admin.sessions.show', $voucher->session) }}"
                               class="text-body text-decoration-none d-inline-flex align-items-center">
                                <i class="ti ti-wifi me-1 text-secondary"></i>
                                <code class="font-monospace">{{ $voucher->session->session_id }}</code>
                            </a>
                            <div class="small text-muted mt-1">
                                {{ ucfirst($voucher->session->status) }} · expires {{ $voucher->session->expires_at?->format('M j, H:i') ?? '—' }}
                            </div>
                        </div>
                        @endif
                    @else
                        <div class="text-center text-muted py-3">
                            <i class="ti ti-qrcode text-secondary" style="font-size: 2rem;"></i>
                            <div class="mt-2">Not yet redeemed.</div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-title">Actions</div>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.vouchers.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center">
                            <i class="ti ti-arrow-left me-1"></i>Back to Vouchers
                        </a>
                        <a href="{{ route('admin.vouchers.create') }}" class="btn btn-outline-primary d-inline-flex align-items-center justify-content-center">
                            <i class="ti ti-plus me-1"></i>New Voucher
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
(function () {
    const codeEl = document.getElementById('voucher-code');

    document.getElementById('btn-copy').addEventListener('click', async () => {
        try {
            await navigator.clipboard.writeText(codeEl.textContent.trim());
            toastr.success('Voucher code copied to clipboard');
        } catch (e) {
            const input = document.createElement('input');
            input.value = codeEl.textContent.trim();
            document.body.appendChild(input);
            input.select();
            document.execCommand('copy');
            document.body.removeChild(input);
            toastr.success('Voucher code copied to clipboard');
        }
    });

    document.getElementById('btn-print').addEventListener('click', () => {
        const printWindow = window.open('', '_blank', 'width=400,height=300');
        printWindow.document.write('<!doctype html><html><head><title>Voucher</title>');
        printWindow.document.write('<style>body{font-family:sans-serif;text-align:center;padding-top:40px}code{font-size:28px;letter-spacing:2px}</style>');
        printWindow.document.write('</head><body>');
        printWindow.document.write('<h3>Wi-Fi Voucher</h3>');
        printWindow.document.write('<code>' + codeEl.textContent.trim() + '</code>');
        printWindow.document.write('<p style="color:#888;font-size:12px">Redeem at any of our Wi-Fi hotspots</p>');
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        printWindow.print();
    });
})();
</script>
@endsection
