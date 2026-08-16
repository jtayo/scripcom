@extends('layouts.admin')

@section('title', 'Revenue Management')
@section('page-title', 'Revenue Management')
@section('page-subtitle', $organization?->name ?? 'Platform-wide financial overview')

@section('content')
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body py-2">
                    <form method="GET" action="{{ route('admin.revenue') }}" class="d-flex flex-wrap align-items-center gap-2">
                        <div class="btn-group" role="group" aria-label="Quick ranges">
                            @foreach ([30 => '30d', 90 => '90d', 365 => '12m'] as $days => $label)
                                <a href="{{ route('admin.revenue', ['from' => now()->subDays($days)->toDateString(), 'to' => now()->toDateString()]) }}"
                                   class="btn btn-sm {{ $from >= now()->subDays($days)->toDateString() ? 'btn-primary' : 'btn-outline-secondary' }}">{{ $label }}</a>
                            @endforeach
                        </div>
                        <div class="vr d-none d-sm-block"></div>
                        <label class="form-label mb-0 small text-muted">From</label>
                        <input type="date" name="from" class="form-control form-control-sm" style="width: auto;" value="{{ $from }}">
                        <label class="form-label mb-0 small text-muted">To</label>
                        <input type="date" name="to" class="form-control form-control-sm" style="width: auto;" value="{{ $to }}">
                        <button type="submit" class="btn btn-sm btn-primary d-inline-flex align-items-center">
                            <i class="ti ti-filter me-1"></i>Apply
                        </button>
                        <a href="{{ route('admin.revenue') }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center" title="Reset range">
                            <i class="ti ti-x"></i>
                        </a>
                        <span class="ms-auto d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center" data-bs-toggle="modal" data-bs-target="#addRevenueModal">
                                <i class="ti ti-plus me-1"></i>Record Entry
                            </button>
                            @can('create-revenue')
                            <form method="POST" action="{{ route('admin.revenue.rebuild') }}" onsubmit="return confirm('Rebuild the ledger from payments and invoices? Manual entries will be kept.');">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center">
                                    <i class="ti ti-refresh me-1"></i>Rebuild Ledger
                                </button>
                            </form>
                            @endcan
                        </span>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @php $o = $overview; @endphp

    <div class="row row-cards mb-3">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-primary-lt text-primary me-3">
                            <i class="ti ti-coin"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="text-muted small">Total Revenue (Gross)</div>
                            <div class="h3 mb-0">KSh {{ number_format($o['total_gross'], 2) }}</div>
                            <div class="small text-muted">fees KSh {{ number_format($o['total_fees'], 2) }} · net KSh {{ number_format($o['total_net'], 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-success-lt text-success me-3">
                            <i class="ti ti-receipt-2"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="text-muted small">Gross Margin</div>
                            <div class="h3 mb-0">KSh {{ number_format($o['gross_margin'], 2) }}</div>
                            <div class="small text-muted">bandwidth cost KSh {{ number_format($o['bandwidth_cost'], 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-warning-lt text-warning me-3">
                            <i class="ti ti-chart-line"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="text-muted small">EBITDA</div>
                            <div class="h3 mb-0">KSh {{ number_format($o['ebitda'], 2) }}</div>
                            <div class="small text-muted">opex KSh {{ number_format($o['operating_expenses'], 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-info-lt text-info me-3">
                            <i class="ti ti-users"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="text-muted small">Sessions</div>
                            <div class="h3 mb-0">{{ number_format($o['sponsored_sessions']) }}</div>
                            <div class="small text-muted">{{ number_format($o['paid_sessions']) }} paid · {{ number_format($o['bandwidth_gb']) }} GB used</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cards mb-3">
        <div class="col-12 col-xl-8">
            <div class="card h-100">
                <div class="card-header">
                    <h2 class="card-title mb-0"><i class="ti ti-chart-area text-primary me-2"></i>Monthly Revenue</h2>
                </div>
                <div class="card-body">
                    <canvas id="chart-revenue" style="height: 280px; width: 100%;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-4">
            <div class="card h-100">
                <div class="card-header">
                    <h2 class="card-title mb-0"><i class="ti ti-chart-donut text-primary me-2"></i>Revenue by Source</h2>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table mb-0">
                        <thead>
                            <tr>
                                <th>Source</th>
                                <th class="text-end">Records</th>
                                <th class="text-end">Gross</th>
                                <th class="text-end">Net</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bySource as $row)
                                <tr>
                                    <td>
                                        <span class="badge bg-{{ $row['source']->color() }}-lt">{{ $row['source']->label() }}</span>
                                    </td>
                                    <td class="text-end text-muted">{{ number_format($row['records']) }}</td>
                                    <td class="text-end">KSh {{ number_format($row['gross'], 2) }}</td>
                                    <td class="text-end text-muted">KSh {{ number_format($row['net'], 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-5">
                                        No revenue recorded. Use "Rebuild Ledger" to populate from payments and invoices.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @if($byOrganization->isNotEmpty())
    <div class="row row-cards mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title mb-0"><i class="ti ti-building text-primary me-2"></i>Revenue by Organization</h2>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table mb-0">
                        <thead>
                            <tr>
                                <th>Organization</th>
                                <th class="text-end">Records</th>
                                <th class="text-end">Gross</th>
                                <th class="text-end">Net</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($byOrganization as $row)
                                <tr>
                                    <td class="fw-bold text-body">{{ $row['organization']?->name ?? '—' }}</td>
                                    <td class="text-end text-muted">{{ number_format($row['records']) }}</td>
                                    <td class="text-end">KSh {{ number_format($row['gross'], 2) }}</td>
                                    <td class="text-end text-muted">KSh {{ number_format($row['net'], 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="row row-cards mb-3">
        <div class="col-12 col-xl-6">
            <div class="card h-100">
                <div class="card-header">
                    <h2 class="card-title mb-0"><i class="ti ti-map-pin text-primary me-2"></i>Revenue by Hotspot</h2>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table mb-0">
                        <thead>
                            <tr>
                                <th>Hotspot</th>
                                <th class="text-end">Records</th>
                                <th class="text-end">Gross</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($byHotspot as $row)
                                <tr>
                                    <td class="fw-bold text-body">{{ $row['hotspot']?->name ?? '—' }}</td>
                                    <td class="text-end text-muted">{{ number_format($row['records']) }}</td>
                                    <td class="text-end">KSh {{ number_format($row['gross'], 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-5">No hotspot-attributed revenue in this period.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-6">
            <div class="card h-100">
                <div class="card-header">
                    <h2 class="card-title mb-0"><i class="ti ti-speakerphone text-primary me-2"></i>Revenue by Campaign</h2>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table mb-0">
                        <thead>
                            <tr>
                                <th>Campaign</th>
                                <th class="text-end">Records</th>
                                <th class="text-end">Gross</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($byCampaign as $row)
                                <tr>
                                    <td class="fw-bold text-body">{{ $row['campaign']?->title ?? '—' }}</td>
                                    <td class="text-end text-muted">{{ number_format($row['records']) }}</td>
                                    <td class="text-end">KSh {{ number_format($row['gross'], 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-5">No campaign-attributed revenue in this period.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2 class="card-title mb-0"><i class="ti ti-history text-primary me-2"></i>Recent Revenue Entries</h2>
            <div class="card-actions">
                <span class="text-muted small">Auto entries are rebuilt from payments and invoices.</span>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-vcenter table-nowrap card-table mb-0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Source</th>
                        <th>Description</th>
                        <th>Organization</th>
                        <th class="text-end">Gross</th>
                        <th class="text-end">Fee</th>
                        <th class="text-end">Net</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recent as $record)
                        <tr>
                            <td class="text-muted">{{ $record->revenue_date?->format('d M Y') }}</td>
                            <td><span class="badge bg-{{ $record->sourceObject()->color() }}-lt">{{ $record->sourceObject()->label() }}</span></td>
                            <td class="text-body">{{ $record->description ?? '—' }}</td>
                            <td class="text-muted">{{ $record->organization?->name ?? '—' }}</td>
                            <td class="text-end">KSh {{ number_format((float) $record->gross_amount, 2) }}</td>
                            <td class="text-end text-muted">KSh {{ number_format((float) $record->payment_fee, 2) }}</td>
                            <td class="text-end">KSh {{ number_format((float) $record->net_amount, 2) }}</td>
                            <td class="text-end">
                                @if(! ($record->metadata['auto'] ?? false))
                                    @can('delete-revenue')
                                    <form method="POST" action="{{ route('admin.revenue.destroy', $record) }}"
                                        onsubmit="return confirm('Delete this revenue entry?');" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </form>
                                    @endcan
                                @else
                                    <span class="text-muted small"><i class="ti ti-refresh"></i> auto</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">No revenue entries yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @can('create-revenue')
    <div class="modal modal-blur fade" id="addRevenueModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.revenue.store') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Record Revenue Entry</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label" for="revenue-source">Source</label>
                            <select id="revenue-source" name="source" class="form-select" required>
                                @foreach(\App\Enums\RevenueSource::cases() as $source)
                                    <option value="{{ $source->value }}">{{ $source->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="revenue-date">Date</label>
                            <input type="date" id="revenue-date" name="revenue_date" class="form-control" value="{{ now()->toDateString() }}" required>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col">
                                <label class="form-label" for="revenue-gross">Gross Amount (KES)</label>
                                <input type="number" id="revenue-gross" name="gross_amount" class="form-control" min="0" step="0.01" required>
                            </div>
                            <div class="col">
                                <label class="form-label" for="revenue-fee">Payment Fee (KES)</label>
                                <input type="number" id="revenue-fee" name="payment_fee" class="form-control" min="0" step="0.01" value="0">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="revenue-description">Description</label>
                            <textarea id="revenue-description" name="description" class="form-control" rows="2" maxlength="500"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary ms-auto">Save Entry</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endcan
@endsection

@push('styles')
    <style>
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
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('chart-revenue');
            if (!ctx) return;

            const isDark = document.body.classList.contains('dashboard-dark');
            const tickColor = isDark ? '#8b98a9' : '#9aa7b0';
            const gridColor = isDark ? 'rgba(255, 255, 255, .08)' : 'rgba(17, 24, 39, .06)';
            const pointColor = isDark ? '#1c2735' : '#ffffff';

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: @json($series['labels']),
                    datasets: [{
                        label: 'Gross Revenue',
                        data: @json($series['gross']),
                        backgroundColor: 'rgba(32, 107, 196, .65)',
                        borderColor: '#206bc4',
                        borderWidth: 1,
                        borderRadius: 3,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: { color: tickColor, boxWidth: 12 }
                        },
                        tooltip: {
                            backgroundColor: isDark ? 'rgba(255, 255, 255, .12)' : 'rgba(17, 24, 39, .9)',
                            padding: 10,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': KSh ' + Number(context.parsed.y).toLocaleString();
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { color: tickColor, maxTicksLimit: 12, font: { size: 11 } }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: tickColor,
                                font: { size: 11 },
                                callback: function(value) { return 'KSh ' + Number(value).toLocaleString(); }
                            },
                            grid: { color: gridColor }
                        }
                    }
                }
            });
        });
    </script>
@endpush
