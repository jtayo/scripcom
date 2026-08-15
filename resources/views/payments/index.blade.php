@extends('layouts.admin')

@section('title', 'Payments')
@section('page-title', 'Payments')

@section('content')
    <div class="row row-cards">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        Payments
                        <span class="badge bg-secondary-lt ms-2">{{ $payments->total() }}</span>
                    </div>
                    <div class="card-actions d-flex align-items-center gap-2">
                        <form method="GET" action="{{ route('admin.payments.index') }}" class="d-flex gap-1">
                            <div class="input-icon">
                                <span class="input-icon-addon"><i class="ti ti-search"></i></span>
                                <input type="text" name="search" class="form-control" style="min-width: 220px;"
                                       placeholder="Search phone, receipt, checkout..."
                                       value="{{ request('search') }}" aria-label="Search payments">
                                @if(request('search'))
                                <a href="{{ route('admin.payments.index') }}" class="input-icon-addon text-decoration-none" title="Clear search">
                                    <i class="ti ti-x"></i>
                                </a>
                                @endif
                            </div>
                            <select name="status" class="form-select" style="width: auto;" aria-label="Filter by status">
                                <option value="">All statuses</option>
                                @foreach(['pending', 'success', 'failed', 'cancelled'] as $status)
                                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                            <input type="date" name="date" class="form-control" style="width: auto;" value="{{ request('date') }}" aria-label="Filter by date">
                            <button type="submit" class="btn btn-outline-secondary d-inline-flex align-items-center">
                                <i class="ti ti-filter me-1"></i>Filter
                            </button>
                            @if(request()->hasAny(['search', 'status', 'date']))
                            <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center" title="Clear filters">
                                <i class="ti ti-x"></i>
                            </a>
                            @endif
                        </form>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter table-nowrap card-table mb-0">
                        <thead>
                            <tr>
                                <th>Payment</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Receipt</th>
                                <th>Date</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payments as $payment)
                            @php $statusColor = match($payment->status) { 'success' => 'success', 'pending' => 'warning', 'failed' => 'danger', 'cancelled' => 'danger', default => 'secondary' }; @endphp
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="avatar avatar-sm bg-primary-lt text-primary me-2">
                                            <i class="ti ti-credit-card"></i>
                                        </span>
                                        <div>
                                            <a href="{{ route('admin.payments.show', $payment) }}" class="text-body fw-bold text-decoration-none">#{{ $payment->id }}</a>
                                            <div class="small text-muted">{{ $payment->organization->name ?? '—' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="avatar avatar-sm bg-azure-lt text-azure me-2">
                                            <i class="ti ti-user"></i>
                                        </span>
                                        <div>
                                            <span class="fw-bold text-body">{{ $payment->phone }}</span>
                                            <div class="small text-muted">{{ $payment->sponsorship->reference ?? '—' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="fw-bold text-body">{{ $payment->currency }} {{ number_format($payment->amount, 2) }}</span></td>
                                <td>
                                    <span class="badge bg-{{ $statusColor }}-lt">
                                        @if($payment->status === 'success')<span class="status-dot status-dot-animated me-2"></span>@endif
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </td>
                                <td><span class="text-muted">{{ $payment->mpesa_receipt_number ?? '—' }}</span></td>
                                <td class="small text-muted">{{ $payment->transacted_at?->format('M d, H:i') ?? $payment->created_at?->format('M d, H:i') }}</td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ route('admin.payments.show', $payment) }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center" title="View">
                                            <i class="ti ti-eye me-1"></i>View
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <div class="my-4">
                                        <i class="ti ti-credit-card text-secondary" style="font-size: 2.5rem;"></i>
                                        <div class="mt-2">No payments found.</div>
                                        @if(request()->hasAny(['search', 'status', 'date']))
                                        <div class="small text-secondary mt-1">
                                            Try a different filter or <a href="{{ route('admin.payments.index') }}" class="text-primary">clear filters</a>.
                                        </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($payments->hasPages())
                <div class="card-footer py-3 border-top-0">
                    {{ $payments->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection
