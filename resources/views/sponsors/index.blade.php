@extends('layouts.admin')

@section('title', 'Sponsors')
@section('page-title', 'Sponsors')
@section('page-subtitle', 'Manage sponsors on the platform')

@section('content')
    <div class="row row-cards">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        Sponsors
                        <span class="badge bg-secondary-lt ms-2">{{ $sponsors->total() }}</span>
                    </div>
                    <div class="card-actions d-flex align-items-center gap-2">
                        <form method="GET" action="{{ route('admin.sponsors.index') }}" class="d-flex gap-1">
                            <div class="input-icon">
                                <span class="input-icon-addon"><i class="ti ti-search"></i></span>
                                <input type="text" name="search" class="form-control" style="min-width: 220px;"
                                       placeholder="Search sponsors..."
                                       value="{{ request('search') }}" aria-label="Search sponsors">
                                @if(request('search'))
                                <a href="{{ route('admin.sponsors.index') }}" class="input-icon-addon text-decoration-none" title="Clear search">
                                    <i class="ti ti-x"></i>
                                </a>
                                @endif
                            </div>
                            <button type="submit" class="btn btn-outline-secondary d-inline-flex align-items-center">
                                <i class="ti ti-filter me-1"></i>Filter
                            </button>
                        </form>
                        @if(request('search'))
                        <a href="{{ route('admin.sponsors.index') }}" class="btn btn-sm btn-link text-muted text-decoration-none d-inline-flex align-items-center">
                            <i class="ti ti-x me-1"></i>Clear filters
                        </a>
                        @endif
                        @can('create-sponsor')
                        <a href="{{ route('admin.sponsors.create') }}" class="btn btn-primary d-inline-flex align-items-center">
                            <i class="ti ti-plus me-1"></i>New Sponsor
                        </a>
                        @endcan
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter table-nowrap card-table mb-0">
                        <thead>
                            <tr>
                                <th>Sponsor</th>
                                <th>Contact</th>
                                <th class="text-center">Sponsorships</th>
                                <th class="text-center">Campaigns</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sponsors as $sponsor)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($sponsor->logo)
                                            <img src="{{ asset('storage/' . $sponsor->logo) }}" alt="{{ $sponsor->name }}" class="avatar avatar-sm me-2" style="object-fit: contain; background: var(--tblr-bg-surface); border: 1px solid var(--tblr-border-color);">
                                        @else
                                            <span class="avatar avatar-sm me-2 bg-primary-lt text-primary">
                                                <i class="ti ti-building"></i>
                                            </span>
                                        @endif
                                        <div>
                                            <a href="{{ route('admin.sponsors.show', $sponsor) }}" class="text-body fw-bold text-decoration-none">{{ $sponsor->name }}</a>
                                            @if($sponsor->contact_person)
                                                <div class="small text-muted">{{ $sponsor->contact_person }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-medium">{{ $sponsor->contact_person ?? '—' }}</div>
                                    <div class="small text-muted">
                                        @if($sponsor->email){{ $sponsor->email }}@endif
                                        @if($sponsor->email && $sponsor->phone)<span class="mx-1">·</span>@endif
                                        @if($sponsor->phone){{ $sponsor->phone }}@endif
                                        @unless($sponsor->email || $sponsor->phone)—@endunless
                                    </div>
                                </td>
                                <td class="text-center"><span class="badge bg-secondary-lt">{{ number_format($sponsor->sponsorships_count) }}</span></td>
                                <td class="text-center"><span class="badge bg-secondary-lt">{{ number_format($sponsor->campaigns_count) }}</span></td>
                                <td>
                                    <span class="badge bg-{{ $sponsor->is_active ? 'success' : 'secondary' }}-lt">
                                        <span class="status-dot @if($sponsor->is_active) status-dot-animated @endif bg-{{ $sponsor->is_active ? 'success' : 'secondary' }} me-1 d-inline-block"></span>
                                        {{ $sponsor->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ route('admin.sponsors.show', $sponsor) }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center" title="View">
                                            <i class="ti ti-eye me-1"></i>View
                                        </a>
                                        @can('update-sponsor')
                                        <a href="{{ route('admin.sponsors.edit', $sponsor) }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center" title="Edit">
                                            <i class="ti ti-edit me-1"></i>Edit
                                        </a>
                                        @endcan
                                        @can('delete-sponsor')
                                        <form method="POST" action="{{ route('admin.sponsors.destroy', $sponsor) }}" class="d-inline" onsubmit="return confirm('Delete this sponsor? This cannot be undone.');">
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
                                <td colspan="6" class="text-center text-muted py-5">
                                    <div class="my-4">
                                        <i class="ti ti-building text-secondary" style="font-size: 2.5rem;"></i>
                                        <div class="mt-2">No sponsors found.</div>
                                        @if(request('search'))
                                        <div class="small text-secondary mt-1">
                                            Try a different search term or <a href="{{ route('admin.sponsors.index') }}" class="text-primary">clear the filter</a>.
                                        </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($sponsors->hasPages())
                <div class="card-footer py-3">
                    {{ $sponsors->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection
