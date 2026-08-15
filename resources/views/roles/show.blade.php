@extends('layouts.admin')

@section('title', $role->name)
@section('page-title', $role->name)

@section('content')
    <div class="mb-3">
        <a href="{{ route('admin.roles.index') }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">
            <i class="fa-solid fa-arrow-left me-1"></i>Back to Roles
        </a>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                    <div class="d-flex align-items-center mb-3 mb-md-0">
                        <span class="avatar avatar-lg bg-primary-lt text-primary me-3"><i class="ti ti-shield"></i></span>
                        <div>
                            <h1 class="h4 mb-1">{{ $role->name }}</h1>
                            <div class="text-muted d-flex align-items-center flex-wrap">
                                <span class="d-inline-flex align-items-center me-3">
                                    <i class="fa-solid fa-fingerprint me-1 text-secondary"></i>Guard: {{ $role->guard_name }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-primary-lt me-2">{{ number_format($role->users_count) }} user(s)</span>
                        @can('update-role')
                        <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-sm btn-primary d-inline-flex align-items-center">
                            <i class="fa-solid fa-pen me-1"></i>Edit
                        </a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cards">
        <div class="col-12 col-xl-4">
            <div class="card dashboard-card h-100">
                <div class="card-header">
                    <h2 class="card-title mb-0">
                        <i class="fa-solid fa-user-tag text-primary me-2"></i>Assigned Users
                    </h2>
                </div>
                <div class="card-body">
                    @php
                        $users = $role->users()->select(['users.id', 'users.name', 'users.email'])->limit(10)->get();
                    @endphp
                    @forelse($users as $user)
                    <div class="d-flex align-items-center py-1">
                        <span class="avatar avatar-sm bg-secondary-lt text-secondary me-2"><i class="ti ti-user"></i></span>
                        <div class="text-truncate">
                            <a href="{{ route('admin.users.show', $user) }}" class="text-body fw-bold text-decoration-none">{{ $user->name }}</a>
                            <div class="small text-muted text-truncate">{{ $user->email }}</div>
                        </div>
                    </div>
                    @empty
                    <span class="text-muted">No users assigned.</span>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-8">
            <div class="row row-cards">
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="card stat-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon bg-primary-lt text-primary me-3">
                                    <i class="fa-solid fa-shield-halved"></i>
                                </div>
                                <div>
                                    <div class="stat-label text-muted mb-1">Permissions</div>
                                    <div class="stat-value fw-bolder text-body">{{ number_format($role->permissions_count) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="card stat-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon bg-info-lt text-info me-3">
                                    <i class="fa-solid fa-users"></i>
                                </div>
                                <div>
                                    <div class="stat-label text-muted mb-1">Users</div>
                                    <div class="stat-value fw-bolder text-body">{{ number_format($role->users_count) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="card stat-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon bg-green-lt text-green me-3">
                                    <i class="fa-solid fa-fingerprint"></i>
                                </div>
                                <div>
                                    <div class="stat-label text-muted mb-1">Guard</div>
                                    <div class="stat-value fw-bolder text-body text-truncate">{{ $role->guard_name }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card dashboard-card mt-4">
                <div class="card-header">
                    <h2 class="card-title mb-0">
                        <i class="fa-solid fa-lock text-primary me-2"></i>Permissions
                    </h2>
                </div>
                <div class="card-body">
                    @php
                        $groups = $role->permissions->groupBy(fn ($p) => \App\Http\Controllers\PermissionController::groupOf($p));
                    @endphp
                    @forelse($groups as $group => $permissions)
                    <div class="mb-3">
                        <div class="text-uppercase small fw-bold text-secondary mb-1">{{ $group }}</div>
                        <div class="d-flex flex-wrap gap-1">
                            @foreach($permissions as $permission)
                            <span class="badge bg-secondary-lt">{{ $permission->name }}</span>
                            @endforeach
                        </div>
                    </div>
                    @empty
                    <span class="text-muted">No permissions assigned to this role.</span>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .stat-card {
            transition: transform .2s ease, box-shadow .2s ease;
            height: 100%;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 1rem 2rem rgba(17, 24, 39, .08) !important;
        }

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

        .stat-icon-sm {
            width: 2rem;
            height: 2rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: .5rem;
            font-size: .9rem;
            flex-shrink: 0;
        }

        .stat-value {
            font-size: 1.5rem;
            line-height: 1.15;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .stat-label {
            font-size: .75rem;
            text-transform: uppercase;
            letter-spacing: .04em;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .stat-card .card-body > .d-flex {
            min-width: 0;
        }

        .stat-card .card-body > .d-flex > div:last-child {
            min-width: 0;
        }

        .dashboard-card .card-header {
            background: transparent;
            border-bottom: 1px solid var(--tblr-border-color);
            padding: .9rem 1.25rem;
            min-height: 0;
        }
    </style>
@endpush
