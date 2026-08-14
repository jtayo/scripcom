@extends('layouts.admin')

@section('title', 'Sponsorships')
@section('page-title', 'Sponsorships')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.sponsorships.index') }}" class="row g-3 align-items-center">
                        <div class="col-12 col-md-5 col-lg-4">
                            <div class="input-group">
                                <span class="input-group-text"><svg class="icon text-secondary" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path></svg></span>
                                <input type="text" name="search" class="form-control" placeholder="Search reference..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-12 col-md-3 col-lg-2">
                            <select name="status" class="form-select">
                                <option value="">All statuses</option>
                                @foreach(['pending', 'active', 'expired', 'cancelled'] as $status)
                                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-4 col-lg-2">
                            <button type="submit" class="btn btn-dark d-inline-flex align-items-center">Filter</button>
                        </div>
                        <div class="col-12 col-lg-4 text-lg-end">
                            @can('create-sponsorship')
                            <a href="{{ route('admin.sponsorships.create') }}" class="btn btn-primary d-inline-flex align-items-center">
                                <svg class="icon me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                New Sponsorship
                            </a>
                            @endcan
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
                                    <th class="border-0 rounded-start">Reference</th>
                                    <th class="border-0">Sponsor</th>
                                    <th class="border-0">Type</th>
                                    <th class="border-0">Used / Total</th>
                                    <th class="border-0">Amount</th>
                                    <th class="border-0">Status</th>
                                    <th class="border-0 rounded-end text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sponsorships as $sponsorship)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.sponsorships.show', $sponsorship) }}" class="text-body fw-bold">{{ $sponsorship->reference }}</a>
                                        <div class="small text-muted">{{ $sponsorship->organization->name ?? '—' }}</div>
                                    </td>
                                    <td>{{ $sponsorship->sponsor->name ?? '—' }}</td>
                                    <td><span class="badge bg-secondary">{{ ucfirst($sponsorship->type) }}</span></td>
                                    <td>{{ number_format($sponsorship->quantity_used) }} / {{ number_format($sponsorship->quantity_purchased) }}</td>
                                    <td>{{ $sponsorship->currency }} {{ number_format($sponsorship->total_amount, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $sponsorship->status === 'active' ? 'success' : ($sponsorship->status === 'pending' ? 'warning' : 'secondary') }}">{{ ucfirst($sponsorship->status) }}</span>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group">
                                            <a href="{{ route('admin.sponsorships.show', $sponsorship) }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">View</a>
                                            @can('update-sponsorship')
                                            <a href="{{ route('admin.sponsorships.edit', $sponsorship) }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">Edit</a>
                                            @endcan
                                            @can('delete-sponsorship')
                                            <form method="POST" action="{{ route('admin.sponsorships.destroy', $sponsorship) }}" class="d-inline" onsubmit="return confirm('Delete this sponsorship?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger d-inline-flex align-items-center">Delete</button>
                                            </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="7" class="text-center text-muted py-5">No sponsorships found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($sponsorships->hasPages())
                <div class="card-footer border-0 py-2">
                    {{ $sponsorships->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection
