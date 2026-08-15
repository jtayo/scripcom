@extends('layouts.admin')

@section('title', $organization->name)
@section('page-title', $organization->name)

@section('content')
    <div class="mb-3">
        <a href="{{ route('admin.organizations.index') }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">
            <i class="fa-solid fa-arrow-left me-1"></i>Back to Organizations
        </a>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                    <div class="d-flex align-items-center mb-3 mb-md-0">
                        <span class="avatar avatar-lg me-3" style="background-image: url('{{ $organization->logo ?? asset('img/team/profile-picture-1.jpg') }}')"></span>
                        <div>
                            <h1 class="h4 mb-1">{{ $organization->name }}</h1>
                            <div class="text-muted d-flex align-items-center flex-wrap">
                                <span class="badge bg-secondary-lt me-2">{{ $organization->type ?? 'Organization' }}</span>
                                @if($organization->county)
                                <span class="d-inline-flex align-items-center">
                                    <i class="fa-solid fa-map-pin me-1 text-secondary"></i>{{ $organization->county }}
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-{{ $organization->is_active ? 'success' : 'danger' }}-lt me-2">
                            <span class="status-dot @if($organization->is_active) status-dot-animated @endif bg-{{ $organization->is_active ? 'success' : 'danger' }} me-1 d-inline-block"></span>
                            {{ $organization->is_active ? 'Active' : 'Inactive' }}
                        </span>
                        @can('update-organization')
                        <a href="{{ route('admin.organizations.edit', $organization) }}" class="btn btn-sm btn-primary d-inline-flex align-items-center">
                            <i class="fa-solid fa-pen me-1"></i>Edit
                        </a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cards">
        <div class="col-12 col-xl-3">
            <div class="card dashboard-card h-100">
                <div class="card-header">
                    <h2 class="card-title mb-0">
                        <i class="fa-solid fa-address-book text-primary me-2"></i>Contact
                    </h2>
                </div>
                <div class="card-body">
                    <dl class="row mb-0 contact-list">
                        <div class="contact-item">
                            <dt class="contact-label">
                                <span class="stat-icon-sm bg-primary-lt text-primary me-2"><i class="fa-solid fa-envelope"></i></span>Email
                            </dt>
                            <dd class="fw-bold text-truncate mb-0">{{ $organization->email ?? '—' }}</dd>
                        </div>
                        <div class="contact-item">
                            <dt class="contact-label">
                                <span class="stat-icon-sm bg-primary-lt text-primary me-2"><i class="fa-solid fa-phone"></i></span>Phone
                            </dt>
                            <dd class="fw-bold mb-0">{{ $organization->phone ?? '—' }}</dd>
                        </div>
                        <div class="contact-item">
                            <dt class="contact-label">
                                <span class="stat-icon-sm bg-primary-lt text-primary me-2"><i class="fa-solid fa-location-dot"></i></span>Address
                            </dt>
                            <dd class="fw-bold mb-0">{{ $organization->address ?? '—' }}</dd>
                        </div>
                        <div class="contact-item">
                            <dt class="contact-label">
                                <span class="stat-icon-sm bg-primary-lt text-primary me-2"><i class="fa-solid fa-map"></i></span>Location
                            </dt>
                            <dd class="fw-bold mb-0">{{ collect([$organization->city, $organization->county, $organization->country])->filter()->join(', ') ?: '—' }}</dd>
                        </div>
                        @if($organization->website)
                        <div class="contact-item">
                            <dt class="contact-label">
                                <span class="stat-icon-sm bg-primary-lt text-primary me-2"><i class="fa-solid fa-globe"></i></span>Website
                            </dt>
                            <dd class="fw-bold text-truncate mb-0"><a href="{{ $organization->website }}" target="_blank" rel="noopener" class="text-body text-decoration-none">{{ $organization->website }}</a></dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-9">
            <div class="row row-cards">
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="card stat-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon bg-primary-lt text-primary me-3">
                                    <i class="fa-solid fa-users"></i>
                                </div>
                                <div>
                                    <div class="stat-label text-muted mb-1">Users</div>
                                    <div class="stat-value fw-bolder text-body">{{ number_format($organization->users_count) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="card stat-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon bg-success-lt text-success me-3">
                                    <i class="fa-solid fa-wifi"></i>
                                </div>
                                <div>
                                    <div class="stat-label text-muted mb-1">Hotspots</div>
                                    <div class="stat-value fw-bolder text-body">{{ number_format($organization->hotspots_count) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="card stat-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon bg-info-lt text-info me-3">
                                    <i class="fa-solid fa-bullhorn"></i>
                                </div>
                                <div>
                                    <div class="stat-label text-muted mb-1">Campaigns</div>
                                    <div class="stat-value fw-bolder text-body">{{ number_format($organization->campaigns_count) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="card stat-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon bg-warning-lt text-warning me-3">
                                    <i class="fa-solid fa-handshake"></i>
                                </div>
                                <div>
                                    <div class="stat-label text-muted mb-1">Sponsorships</div>
                                    <div class="stat-value fw-bolder text-body">{{ number_format($organization->sponsorships_count) }}</div>
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
                    <div class="row">
                        <div class="col-6 col-md-3 mb-3">
                            <div class="small text-muted">Postal Code</div>
                            <div class="fw-bold">{{ $organization->postal_code ?? '—' }}</div>
                        </div>
                        <div class="col-6 col-md-3 mb-3">
                            <div class="small text-muted">Slug</div>
                            <div class="fw-bold"><code>{{ $organization->slug }}</code></div>
                        </div>
                        <div class="col-6 col-md-3 mb-3">
                            <div class="small text-muted">Sessions</div>
                            <div class="fw-bold">{{ number_format($organization->sessions_count) }}</div>
                        </div>
                        <div class="col-6 col-md-3 mb-3">
                            <div class="small text-muted">Created</div>
                            <div class="fw-bold">{{ $organization->created_at?->format('M d, Y') }}</div>
                        </div>
                    </div>
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

        .contact-list {
            display: grid;
            grid-template-columns: 1fr;
            gap: .75rem;
            min-width: 0;
        }

        .contact-item {
            display: flex;
            align-items: center;
            min-width: 0;
        }

        .contact-label {
            display: flex;
            align-items: center;
            flex-shrink: 0;
            width: 8.5rem;
            font-size: .8125rem;
            color: var(--tblr-secondary-color);
            font-weight: 400;
            margin: 0;
        }

        .contact-item dd {
            min-width: 0;
            font-size: .8125rem;
        }
    </style>
@endpush
