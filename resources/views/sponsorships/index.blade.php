@extends('layouts.admin')

@section('title', 'Sponsorships')
@section('page-title', 'Sponsorships')

@section('content')
    <div class="row row-cards">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        Sponsorships
                        <span class="badge bg-secondary-lt ms-2">{{ $sponsorships->total() }}</span>
                    </div>
                    <div class="card-actions d-flex align-items-center gap-2">
                        <form method="GET" action="{{ route('admin.sponsorships.index') }}" class="d-flex gap-1">
                            <div class="input-icon">
                                <span class="input-icon-addon"><i class="ti ti-search"></i></span>
                                <input type="text" name="search" class="form-control" style="min-width: 220px;"
                                       placeholder="Search sponsorships..."
                                       value="{{ request('search') }}" aria-label="Search sponsorships">
                                @if(request('search'))
                                <a href="{{ route('admin.sponsorships.index') }}" class="input-icon-addon text-decoration-none" title="Clear search">
                                    <i class="ti ti-x"></i>
                                </a>
                                @endif
                            </div>
                            <select name="status" class="form-select" style="width: auto;" aria-label="Filter by status">
                                <option value="">All statuses</option>
                                @foreach(['pending', 'active', 'expired', 'cancelled'] as $status)
                                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-outline-secondary d-inline-flex align-items-center">
                                <i class="ti ti-filter me-1"></i>Filter
                            </button>
                            @if(request('status') || request('search'))
                            <a href="{{ route('admin.sponsorships.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center" title="Clear filters">
                                <i class="ti ti-x"></i>
                            </a>
                            @endif
                        </form>
                        @can('create-sponsorship')
                        <a href="{{ route('admin.sponsorships.create') }}" class="btn btn-primary d-inline-flex align-items-center">
                            <i class="ti ti-plus me-1"></i>New Sponsorship
                        </a>
                        @endcan
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter table-nowrap card-table mb-0">
                        <thead>
                            <tr>
                                <th>Reference</th>
                                <th>Sponsor</th>
                                <th>Type</th>
                                <th>Used / Total</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sponsorships as $sponsorship)
                            @php
                                $statusColor = match($sponsorship->status) {
                                    'active' => 'success',
                                    'pending' => 'warning',
                                    'expired' => 'secondary',
                                    default => 'danger',
                                };
                            @endphp
                            <tr>
                                <td>
                                    <a href="{{ route('admin.sponsorships.show', $sponsorship) }}" class="text-body fw-bold text-decoration-none">{{ $sponsorship->reference }}</a>
                                    <div class="small text-muted">{{ $sponsorship->organization->name ?? '—' }}</div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="avatar avatar-sm bg-primary-lt text-primary me-2">
                                            <i class="ti ti-building"></i>
                                        </span>
                                        <div>
                                            @if($sponsorship->sponsor)
                                            <a href="{{ route('admin.sponsors.show', $sponsorship->sponsor) }}" class="text-body fw-bold text-decoration-none">{{ $sponsorship->sponsor->name }}</a>
                                            <div class="small text-muted">{{ $sponsorship->sponsor->contact_person ?? $sponsorship->sponsor->email }}</div>
                                            @else
                                            <span class="text-muted">—</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge bg-secondary-lt">{{ ucfirst($sponsorship->type) }}</span></td>
                                <td class="small text-muted">
                                    <span class="d-inline-flex align-items-center">
                                        <i class="ti ti-player-play me-1 text-secondary"></i>
                                        {{ number_format($sponsorship->quantity_used) }} / {{ number_format($sponsorship->quantity_purchased) }}
                                    </span>
                                </td>
                                <td class="small text-muted">{{ $sponsorship->currency }} {{ number_format($sponsorship->total_amount, 2) }}</td>
                                <td>
                                    <span class="badge bg-{{ $statusColor }}-lt">
                                        <span class="status-dot @if($sponsorship->status === 'active') status-dot-animated @endif me-1"></span>
                                        {{ ucfirst($sponsorship->status) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ route('admin.sponsorships.show', $sponsorship) }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center" title="View">
                                            <i class="ti ti-eye me-1"></i>View
                                        </a>
                                        @can('update-sponsorship')
                                        <a href="{{ route('admin.sponsorships.edit', $sponsorship) }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center" title="Edit">
                                            <i class="ti ti-edit me-1"></i>Edit
                                        </a>
                                        @endcan
                                        @can('delete-sponsorship')
                                        <form method="POST" action="{{ route('admin.sponsorships.destroy', $sponsorship) }}" class="d-inline" onsubmit="return confirm('Delete this sponsorship?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger d-inline-flex align-items-center" title="Delete">
                                                <i class="ti ti-trash me-1"></i>Delete
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <div class="my-4">
                                        <i class="ti ti-handshake text-secondary" style="font-size: 2.5rem;"></i>
                                        <div class="mt-2">No sponsorships found.</div>
                                        @if(request('search') || request('status'))
                                        <div class="small text-secondary mt-1">
                                            Try a different filter or <a href="{{ route('admin.sponsorships.index') }}" class="text-primary">clear filters</a>.
                                        </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($sponsorships->hasPages())
                <div class="card-footer py-3">
                    {{ $sponsorships->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection
