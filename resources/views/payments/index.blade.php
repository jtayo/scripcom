@extends('layouts.admin')

@section('title', 'Payments')
@section('page-title', 'Payments')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.payments.index') }}" class="row g-3 align-items-center">
                        <div class="col-12 col-md-4">
                            <div class="input-group">
                                <span class="input-group-text"><svg class="icon text-secondary" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path></svg></span>
                                <input type="text" name="search" class="form-control" placeholder="Search phone, receipt, checkout..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-12 col-md-3 col-lg-2">
                            <select name="status" class="form-select">
                                <option value="">All statuses</option>
                                @foreach(['pending', 'success', 'failed', 'cancelled'] as $status)
                                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-3 col-lg-2">
                            <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                        </div>
                        <div class="col-12 col-md-2 col-lg-2">
                            <button type="submit" class="btn btn-dark d-inline-flex align-items-center w-100">Filter</button>
                        </div>
                        <div class="col-12 col-lg-2 text-lg-end">
                            @if(request()->hasAny(['search', 'status', 'date']))
                            <a href="{{ route('admin.payments.index') }}" class="btn btn-link btn-sm text-secondary">Clear</a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-vcenter table-nowrap mb-0">
                            <thead class="">
                                <tr>
                                    <th class="border-0 rounded-start">Payment</th>
                                    <th class="border-0">Phone</th>
                                    <th class="border-0">Sponsorship</th>
                                    <th class="border-0">Amount</th>
                                    <th class="border-0">Receipt</th>
                                    <th class="border-0">Date</th>
                                    <th class="border-0 rounded-end text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payments as $payment)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.payments.show', $payment) }}" class="text-body fw-bold">#{{ $payment->id }}</a>
                                        <div class="small text-muted">{{ $payment->organization->name ?? '—' }}</div>
                                    </td>
                                    <td>{{ $payment->phone }}</td>
                                    <td>{{ $payment->sponsorship->reference ?? '—' }}</td>
                                    <td>{{ $payment->currency }} {{ number_format($payment->amount, 2) }}</td>
                                    <td>{{ $payment->mpesa_receipt_number ?? '—' }}</td>
                                    <td>{{ $payment->transacted_at?->format('M d, H:i') ?? $payment->created_at?->format('M d, H:i') }}</td>
                                    <td class="text-end">
                                        <div class="btn-group">
                                            <a href="{{ route('admin.payments.show', $payment) }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">View</a>
                                            <span class="badge bg-{{ $payment->status === 'success' ? 'success' : ($payment->status === 'pending' ? 'warning' : 'danger') }} align-self-center ms-2">{{ ucfirst($payment->status) }}</span>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="7" class="text-center text-muted py-5">No payments found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($payments->hasPages())
                <div class="card-footer border-0 py-2">
                    {{ $payments->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection
