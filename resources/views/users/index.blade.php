@extends('layouts.admin')

@section('title', 'Users')
@section('page-title', 'Users')
@section('page-subtitle', 'Manage system users')

@section('content')
    <div class="row row-cards">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div>
                        <div class="card-title mb-1">Users</div>
                        <div class="small text-muted">Manage system users</div>
                    </div>
                    <div class="card-actions d-flex align-items-center gap-2">
                        <form method="GET" action="{{ route('admin.users.index') }}" class="d-flex gap-1">
                            <div class="input-icon">
                                <span class="input-icon-addon"><i class="ti ti-search"></i></span>
                                <input type="text" name="search" class="form-control" style="min-width: 220px;"
                                       placeholder="Search by name or email..."
                                       value="{{ request('search') }}" aria-label="Search users">
                                @if(request('search'))
                                <a href="{{ route('admin.users.index') }}" class="input-icon-addon text-decoration-none" title="Clear search">
                                    <i class="ti ti-x"></i>
                                </a>
                                @endif
                            </div>
                            <button type="submit" class="btn btn-outline-secondary d-inline-flex align-items-center">
                                <i class="ti ti-filter me-1"></i>Filter
                            </button>
                            @if(request('search'))
                            <a href="{{ route('admin.users.index') }}" class="btn btn-link text-muted d-inline-flex align-items-center px-1" title="Clear filters">
                                <i class="ti ti-x me-1"></i>Clear
                            </a>
                            @endif
                        </form>
                        @can('create-user')
                        <a href="{{ route('admin.users.create') }}" class="btn btn-primary d-inline-flex align-items-center">
                            <i class="ti ti-plus me-1"></i>New User
                        </a>
                        @endcan
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter table-nowrap card-table mb-0 text-sm">
                        <thead>
                            <tr>
                                <th class="border-0">User</th>
                                <th class="border-0">Organization</th>
                                <th class="border-0">Phone</th>
                                <th class="border-0">Roles</th>
                                <th class="border-0">Status</th>
                                <th class="border-0 text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($user->avatar)
                                        <img src="{{ $user->avatar }}" class="avatar avatar-sm me-2" alt="{{ $user->name }}">
                                        @else
                                        <span class="avatar avatar-sm bg-primary-lt text-primary me-2"><i class="ti ti-user"></i></span>
                                        @endif
                                        <div>
                                            <a href="{{ route('admin.users.show', $user) }}" class="text-body fw-bold text-decoration-none">{{ $user->name }}</a>
                                            <div class="small text-muted">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $user->organization->name ?? '—' }}</td>
                                <td>{{ $user->phone ?? '—' }}</td>
                                <td>
                                    @forelse($user->roles as $role)
                                    <span class="badge bg-secondary-lt">{{ $role->name }}</span>
                                    @empty
                                    <span class="text-muted">—</span>
                                    @endforelse
                                </td>
                                <td>
                                    <span class="badge bg-{{ $user->status === 'active' ? 'success' : 'danger' }}-lt">
                                        <span class="status-dot @if($user->status === 'active') status-dot-animated @endif bg-{{ $user->status === 'active' ? 'success' : 'danger' }} me-1 d-inline-block"></span>
                                        {{ ucfirst($user->status ?? 'active') }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center" title="View">
                                            <i class="ti ti-eye me-1"></i>View
                                        </a>
                                        @can('update-user')
                                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center" title="Edit">
                                            <i class="ti ti-edit me-1"></i>Edit
                                        </a>
                                        @endcan
                                        @can('delete-user')
                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="d-inline" onsubmit="return confirm('Delete this user?');">
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
                                        <i class="ti ti-users text-secondary" style="font-size: 2.5rem;"></i>
                                        <div class="mt-2">No users found.</div>
                                        @if(request('search'))
                                        <div class="small text-secondary mt-1">
                                            Try a different search term or <a href="{{ route('admin.users.index') }}" class="text-primary">clear the filter</a>.
                                        </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($users->hasPages())
                <div class="card-footer py-3 border-top-0">
                    {{ $users->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection
