@extends('layouts.admin')

@section('title', 'Organizations')
@section('page-title', 'Organizations')
@section('page-subtitle', 'Manage organizations on the platform')

@section('content')
    <div class="row row-cards">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        Organizations
                        <span class="badge bg-secondary-lt ms-2">{{ $organizations->total() }}</span>
                    </div>
                    <div class="card-actions d-flex align-items-center gap-2">
                        <form method="GET" action="{{ route('admin.organizations.index') }}" class="d-flex gap-1">
                            <div class="input-icon">
                                <span class="input-icon-addon"><i class="ti ti-search"></i></span>
                                <input type="text" name="search" class="form-control" style="min-width: 220px;"
                                       placeholder="Search organizations..."
                                       value="{{ request('search') }}" aria-label="Search organizations">
                                @if(request('search'))
                                <a href="{{ route('admin.organizations.index') }}" class="input-icon-addon text-decoration-none" title="Clear search">
                                    <i class="ti ti-x"></i>
                                </a>
                                @endif
                            </div>
                            <button type="submit" class="btn btn-outline-secondary d-inline-flex align-items-center">
                                <i class="ti ti-filter me-1"></i>Filter
                            </button>
                        </form>
                        @can('create-organization')
                        <a href="{{ route('admin.organizations.create') }}" class="btn btn-primary d-inline-flex align-items-center">
                            <i class="ti ti-plus me-1"></i>New Organization
                        </a>
                        @endcan
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter table-nowrap card-table mb-0">
                        <thead>
                            <tr>
                                <th>Organization</th>
                                <th>Location</th>
                                <th>Type</th>
                                <th class="text-center">Users</th>
                                <th class="text-center">Hotspots</th>
                                <th class="text-center">Campaigns</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($organizations as $organization)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="avatar avatar-sm me-2" style="background-image: url('{{ $organization->logo ?? asset('img/team/profile-picture-1.jpg') }}')"></span>
                                        <div>
                                            <a href="{{ route('admin.organizations.show', $organization) }}" class="text-body fw-bold text-decoration-none">{{ $organization->name }}</a>
                                            @if($organization->email)
                                                <div class="small text-muted">{{ $organization->email }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="d-inline-flex align-items-center text-muted">
                                        <i class="ti ti-map-pin me-1 text-secondary"></i>
                                        {{ $organization->county ?? $organization->city ?? '—' }}
                                    </span>
                                </td>
                                <td>{{ $organization->type ?? '—' }}</td>
                                <td class="text-center"><span class="badge bg-secondary-lt">{{ $organization->users_count }}</span></td>
                                <td class="text-center"><span class="badge bg-secondary-lt">{{ $organization->hotspots_count }}</span></td>
                                <td class="text-center"><span class="badge bg-secondary-lt">{{ $organization->campaigns_count }}</span></td>
                                <td>
                                    <span class="badge bg-{{ $organization->is_active ? 'success' : 'danger' }}-lt">
                                        {{ $organization->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <a href="{{ route('admin.organizations.show', $organization) }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center" title="View">
                                            <i class="ti ti-eye me-1"></i>View
                                        </a>
                                        @can('update-organization')
                                        <a href="{{ route('admin.organizations.edit', $organization) }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center" title="Edit">
                                            <i class="ti ti-edit me-1"></i>Edit
                                        </a>
                                        @endcan
                                        @can('delete-organization')
                                        <form method="POST" action="{{ route('admin.organizations.destroy', $organization) }}" class="d-inline" onsubmit="return confirm('Delete this organization? This cannot be undone.');">
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
                                <td colspan="8" class="text-center text-muted py-5">
                                    <div class="my-4">
                                        <i class="ti ti-building-community text-secondary" style="font-size: 2.5rem;"></i>
                                        <div class="mt-2">No organizations found.</div>
                                        @if(request('search'))
                                        <div class="small text-secondary mt-1">
                                            Try a different search term or <a href="{{ route('admin.organizations.index') }}" class="text-primary">clear the filter</a>.
                                        </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($organizations->hasPages())
                <div class="card-footer py-3">
                    {{ $organizations->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection
