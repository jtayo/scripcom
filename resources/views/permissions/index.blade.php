@extends('layouts.admin')

@section('title', 'Permissions')
@section('page-title', 'Permissions')
@section('page-subtitle', 'View permissions available on the platform')

@section('content')
    <div class="row row-cards">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div>
                        <div class="card-title mb-1">Permissions</div>
                        <div class="small text-muted">View permissions available on the platform</div>
                    </div>
                    <div class="card-actions d-flex align-items-center gap-2">
                        <form method="GET" action="{{ route('admin.permissions.index') }}" class="d-flex gap-1">
                            <div class="input-icon">
                                <span class="input-icon-addon"><i class="ti ti-search"></i></span>
                                <input type="text" name="search" class="form-control" style="min-width: 220px;"
                                       placeholder="Search by permission name..."
                                       value="{{ request('search') }}" aria-label="Search permissions">
                                @if(request('search'))
                                <a href="{{ route('admin.permissions.index') }}" class="input-icon-addon text-decoration-none" title="Clear search">
                                    <i class="ti ti-x"></i>
                                </a>
                                @endif
                            </div>
                            <button type="submit" class="btn btn-outline-secondary d-inline-flex align-items-center">
                                <i class="ti ti-filter me-1"></i>Filter
                            </button>
                            @if(request('search'))
                            <a href="{{ route('admin.permissions.index') }}" class="btn btn-link text-muted d-inline-flex align-items-center px-1" title="Clear filters">
                                <i class="ti ti-x me-1"></i>Clear
                            </a>
                            @endif
                        </form>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter table-nowrap card-table mb-0 text-sm">
                        <thead>
                            <tr>
                                <th class="border-0">Permission</th>
                                <th class="border-0">Guard</th>
                                <th class="border-0">Roles</th>
                                <th class="border-0">Created</th>
                                <th class="border-0 text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($permissions as $permission)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.permissions.show', $permission) }}" class="text-body fw-bold text-decoration-none">
                                        <i class="ti ti-lock me-1 text-secondary"></i>{{ $permission->name }}
                                    </a>
                                </td>
                                <td>
                                    <span class="badge bg-secondary-lt">{{ $permission->guard_name }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-blue-lt">{{ number_format($permission->roles_count) }}</span>
                                </td>
                                <td>{{ $permission->created_at?->format('M d, Y') }}</td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ route('admin.permissions.show', $permission) }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center" title="View">
                                            <i class="ti ti-eye me-1"></i>View
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-5">
                                    <div class="my-4">
                                        <i class="ti ti-lock text-secondary" style="font-size: 2.5rem;"></i>
                                        <div class="mt-2">No permissions found.</div>
                                        @if(request('search'))
                                        <div class="small text-secondary mt-1">
                                            Try a different search term or <a href="{{ route('admin.permissions.index') }}" class="text-primary">clear the filter</a>.
                                        </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($permissions->hasPages())
                <div class="card-footer py-3 border-top-0">
                    {{ $permissions->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection
