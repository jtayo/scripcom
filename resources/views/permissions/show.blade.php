@extends('layouts.admin')

@section('title', $permission->name)
@section('page-title', $permission->name)

@section('content')
    <div class="mb-3">
        <a href="{{ route('admin.permissions.index') }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">
            <i class="fa-solid fa-arrow-left me-1"></i>Back to Permissions
        </a>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                    <div class="d-flex align-items-center mb-3 mb-md-0">
                        <span class="avatar avatar-lg bg-primary-lt text-primary me-3"><i class="ti ti-lock"></i></span>
                        <div>
                            <h1 class="h4 mb-1">{{ $permission->name }}</h1>
                            <div class="text-muted d-flex align-items-center flex-wrap">
                                <span class="d-inline-flex align-items-center me-3">
                                    <i class="fa-solid fa-fingerprint me-1 text-secondary"></i>Guard: {{ $permission->guard_name }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-primary-lt me-2">{{ number_format($permission->roles_count) }} role(s)</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cards">
        <div class="col-12 col-xl-6">
            <div class="card dashboard-card h-100">
                <div class="card-header">
                    <h2 class="card-title mb-0">
                        <i class="fa-solid fa-user-tag text-primary me-2"></i>Roles Using This Permission
                    </h2>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-1">
                        @forelse($permission->roles as $role)
                        <a href="{{ route('admin.roles.show', $role) }}" class="badge bg-secondary-lt text-decoration-none">
                            <i class="ti ti-shield me-1"></i>{{ $role->name }}
                        </a>
                        @empty
                        <span class="text-muted">No roles currently use this permission.</span>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-6">
            <div class="card dashboard-card">
                <div class="card-header">
                    <h2 class="card-title mb-0">
                        <i class="fa-solid fa-circle-info text-primary me-2"></i>Details
                    </h2>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <div class="col-12 col-md-6 mb-3">
                            <dt class="small text-muted">Name</dt>
                            <dd class="fw-bold mb-0">{{ $permission->name }}</dd>
                        </div>
                        <div class="col-12 col-md-6 mb-3">
                            <dt class="small text-muted">Guard</dt>
                            <dd class="fw-bold mb-0">{{ $permission->guard_name }}</dd>
                        </div>
                        <div class="col-12 col-md-6 mb-3">
                            <dt class="small text-muted">Roles</dt>
                            <dd class="fw-bold mb-0">{{ number_format($permission->roles_count) }}</dd>
                        </div>
                        <div class="col-12 col-md-6 mb-3">
                            <dt class="small text-muted">Created</dt>
                            <dd class="fw-bold mb-0">{{ $permission->created_at?->format('M d, Y H:i') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .dashboard-card .card-header {
            background: transparent;
            border-bottom: 1px solid var(--tblr-border-color);
            padding: .9rem 1.25rem;
            min-height: 0;
        }
    </style>
@endpush
