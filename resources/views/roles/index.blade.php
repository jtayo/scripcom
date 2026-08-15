@extends('layouts.admin')

@section('title', 'Roles')
@section('page-title', 'Roles')
@section('page-subtitle', 'Manage system roles and their permissions')

@section('content')
    <div class="row row-cards">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div>
                        <div class="card-title mb-1">Roles</div>
                        <div class="small text-muted">Manage system roles and their permissions</div>
                    </div>
                    <div class="card-actions d-flex align-items-center gap-2">
                        <form method="GET" action="{{ route('admin.roles.index') }}" class="d-flex gap-1">
                            <div class="input-icon">
                                <span class="input-icon-addon"><i class="ti ti-search"></i></span>
                                <input type="text" name="search" class="form-control" style="min-width: 220px;"
                                       placeholder="Search by role name..."
                                       value="{{ request('search') }}" aria-label="Search roles">
                                @if(request('search'))
                                <a href="{{ route('admin.roles.index') }}" class="input-icon-addon text-decoration-none" title="Clear search">
                                    <i class="ti ti-x"></i>
                                </a>
                                @endif
                            </div>
                            <button type="submit" class="btn btn-outline-secondary d-inline-flex align-items-center">
                                <i class="ti ti-filter me-1"></i>Filter
                            </button>
                            @if(request('search'))
                            <a href="{{ route('admin.roles.index') }}" class="btn btn-link text-muted d-inline-flex align-items-center px-1" title="Clear filters">
                                <i class="ti ti-x me-1"></i>Clear
                            </a>
                            @endif
                        </form>
                        @can('create-role')
                        <a href="{{ route('admin.roles.create') }}" class="btn btn-primary d-inline-flex align-items-center">
                            <i class="ti ti-plus me-1"></i>New Role
                        </a>
                        @endcan
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter table-nowrap card-table mb-0 text-sm">
                        <thead>
                            <tr>
                                <th class="border-0">Role</th>
                                <th class="border-0">Guard</th>
                                <th class="border-0">Permissions</th>
                                <th class="border-0">Users</th>
                                <th class="border-0">Created</th>
                                <th class="border-0 text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($roles as $role)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.roles.show', $role) }}" class="text-body fw-bold text-decoration-none">
                                        <i class="ti ti-shield me-1 text-secondary"></i>{{ $role->name }}
                                    </a>
                                </td>
                                <td>
                                    <span class="badge bg-secondary-lt">{{ $role->guard_name }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-blue-lt">{{ number_format($role->permissions_count) }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-green-lt">{{ number_format($role->users_count) }}</span>
                                </td>
                                <td>{{ $role->created_at?->format('M d, Y') }}</td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ route('admin.roles.show', $role) }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center" title="View">
                                            <i class="ti ti-eye me-1"></i>View
                                        </a>
                                        @can('update-role')
                                        <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center" title="Edit">
                                            <i class="ti ti-edit me-1"></i>Edit
                                        </a>
                                        @endcan
                                        @can('delete-role')
                                        @if($role->name !== 'Super Admin')
                                        <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" class="d-inline" onsubmit="return confirm('Delete role {{ $role->name }}?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger d-inline-flex align-items-center" title="Delete">
                                                <i class="ti ti-trash me-1"></i>Delete
                                            </button>
                                        </form>
                                        @endif
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">
                                    <div class="my-4">
                                        <i class="ti ti-shield text-secondary" style="font-size: 2.5rem;"></i>
                                        <div class="mt-2">No roles found.</div>
                                        @if(request('search'))
                                        <div class="small text-secondary mt-1">
                                            Try a different search term or <a href="{{ route('admin.roles.index') }}" class="text-primary">clear the filter</a>.
                                        </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($roles->hasPages())
                <div class="card-footer py-3 border-top-0">
                    {{ $roles->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection
