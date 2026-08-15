@extends('layouts.admin')

@section('title', $user->name)
@section('page-title', $user->name)

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                    <div class="d-flex align-items-center mb-3 mb-md-0">
                        @if($user->avatar)
                        <span class="avatar avatar-lg me-3" style="background-image: url('{{ $user->avatar }}')"></span>
                        @else
                        <span class="avatar avatar-lg bg-primary-lt text-primary me-3"><i class="ti ti-user"></i></span>
                        @endif
                        <div>
                            <h1 class="h4 mb-1">{{ $user->name }}</h1>
                            <div class="text-muted d-flex align-items-center flex-wrap">
                                <span class="d-inline-flex align-items-center me-3">
                                    <i class="fa-solid fa-envelope me-1 text-secondary"></i>{{ $user->email }}
                                </span>
                                @if($user->phone)
                                <span class="d-inline-flex align-items-center">
                                    <i class="fa-solid fa-phone me-1 text-secondary"></i>{{ $user->phone }}
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-{{ $user->status === 'active' ? 'success' : 'danger' }}-lt me-2">
                            <span class="status-dot @if($user->status === 'active') status-dot-animated @endif bg-{{ $user->status === 'active' ? 'success' : 'danger' }} me-1 d-inline-block"></span>
                            {{ ucfirst($user->status ?? 'active') }}
                        </span>
                        @can('update-user')
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-primary d-inline-flex align-items-center">
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
                        <i class="fa-solid fa-user-tag text-primary me-2"></i>Roles
                    </h2>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-1">
                        @forelse($user->roles as $role)
                        <span class="badge bg-secondary-lt">{{ $role->name }}</span>
                        @empty
                        <span class="text-muted">No roles assigned.</span>
                        @endforelse
                    </div>
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
                                    <i class="fa-solid fa-users"></i>
                                </div>
                                <div>
                                    <div class="stat-label text-muted mb-1">Roles</div>
                                    <div class="stat-value fw-bolder text-body">{{ number_format($user->roles->count()) }}</div>
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
                                    <i class="fa-solid fa-building"></i>
                                </div>
                                <div>
                                    <div class="stat-label text-muted mb-1">Organizations</div>
                                    <div class="stat-value fw-bolder text-body">{{ number_format($user->organization ? 1 : 0) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="card stat-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon bg-{{ $user->status === 'active' ? 'success' : 'danger' }}-lt text-{{ $user->status === 'active' ? 'success' : 'danger' }} me-3">
                                    <i class="fa-solid fa-badge-check"></i>
                                </div>
                                <div>
                                    <div class="stat-label text-muted mb-1">Status</div>
                                    <div class="stat-value fw-bolder text-body">{{ ucfirst($user->status ?? 'active') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card dashboard-card mt-4">
                <div class="card-header">
                    <h2 class="card-title mb-0">
                        <i class="fa-solid fa-circle-info text-primary me-2"></i>Details
                    </h2>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <div class="col-12 col-md-6 mb-3">
                            <dt class="small text-muted">Email</dt>
                            <dd class="fw-bold text-truncate mb-0">{{ $user->email }}</dd>
                        </div>
                        <div class="col-12 col-md-6 mb-3">
                            <dt class="small text-muted">Phone</dt>
                            <dd class="fw-bold mb-0">{{ $user->phone ?? '—' }}</dd>
                        </div>
                        <div class="col-12 col-md-6 mb-3">
                            <dt class="small text-muted">Organization</dt>
                            <dd class="fw-bold mb-0">{{ $user->organization->name ?? '—' }}</dd>
                        </div>
                        <div class="col-12 col-md-6 mb-3">
                            <dt class="small text-muted">Sponsor</dt>
                            <dd class="fw-bold mb-0">{{ $user->sponsor->name ?? '—' }}</dd>
                        </div>
                        <div class="col-12 col-md-6 mb-3">
                            <dt class="small text-muted">Joined</dt>
                            <dd class="fw-bold mb-0">{{ $user->created_at?->format('M d, Y') }}</dd>
                        </div>
                    </dl>
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
