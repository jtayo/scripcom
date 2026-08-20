@extends('layouts.admin')

@section('title', $sponsor->name)
@section('page-title', $sponsor->name)

@section('content')
    <div class="mb-3">
        <a href="{{ route('admin.sponsors.index') }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">
            <i class="fa-solid fa-arrow-left me-1"></i>Back to Sponsors
        </a>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                    <div class="d-flex align-items-center mb-3 mb-md-0">
                        @if($sponsor->logo)
                            <img src="{{ asset('storage/' . $sponsor->logo) }}" alt="{{ $sponsor->name }}" class="me-3" style="width: 3.5rem; height: 3.5rem; object-fit: contain; border-radius: .75rem; border: 1px solid var(--tblr-border-color); padding: .25rem;">
                        @else
                            <span class="avatar avatar-lg bg-primary-lt text-primary me-3">
                                <i class="fa-solid fa-building"></i>
                            </span>
                        @endif
                        <div>
                            <h1 class="h4 mb-1">{{ $sponsor->name }}</h1>
                            <div class="text-muted d-flex align-items-center flex-wrap">
                                @if($sponsor->contact_person)
                                <span class="d-inline-flex align-items-center">
                                    <i class="fa-solid fa-user-tie me-1 text-secondary"></i>{{ $sponsor->contact_person }}
                                </span>
                                @endif
                                @if($sponsor->email)
                                <span class="mx-2 text-secondary">·</span>
                                <span class="d-inline-flex align-items-center">
                                    <i class="fa-solid fa-envelope me-1 text-secondary"></i>{{ $sponsor->email }}
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-{{ $sponsor->is_active ? 'success' : 'secondary' }}-lt me-2">
                            <span class="status-dot @if($sponsor->is_active) status-dot-animated @endif bg-{{ $sponsor->is_active ? 'success' : 'secondary' }} me-1 d-inline-block"></span>
                            {{ $sponsor->is_active ? 'Active' : 'Inactive' }}
                        </span>
                        @can('update-sponsor')
                        <a href="{{ route('admin.sponsors.edit', $sponsor) }}" class="btn btn-sm btn-primary d-inline-flex align-items-center">
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
                        <i class="fa-solid fa-circle-info text-primary me-2"></i>Details
                    </h2>
                </div>
                <div class="card-body">
                    <dl class="row mb-0 contact-list">
                        <div class="contact-item">
                            <dt class="contact-label">
                                <span class="stat-icon-sm bg-primary-lt text-primary me-2"><i class="fa-solid fa-user-tie"></i></span>Contact Person
                            </dt>
                            <dd class="fw-bold mb-0">{{ $sponsor->contact_person ?? '—' }}</dd>
                        </div>
                        <div class="contact-item">
                            <dt class="contact-label">
                                <span class="stat-icon-sm bg-primary-lt text-primary me-2"><i class="fa-solid fa-envelope"></i></span>Email
                            </dt>
                            <dd class="fw-bold text-truncate mb-0">
                                @if($sponsor->email)<a href="mailto:{{ $sponsor->email }}" class="text-body text-decoration-none">{{ $sponsor->email }}</a>@else—@endif
                            </dd>
                        </div>
                        <div class="contact-item">
                            <dt class="contact-label">
                                <span class="stat-icon-sm bg-primary-lt text-primary me-2"><i class="fa-solid fa-phone"></i></span>Phone
                            </dt>
                            <dd class="fw-bold mb-0">{{ $sponsor->phone ?? '—' }}</dd>
                        </div>
                        <div class="contact-item">
                            <dt class="contact-label">
                                <span class="stat-icon-sm bg-primary-lt text-primary me-2"><i class="fa-solid fa-map-pin"></i></span>Address
                            </dt>
                            <dd class="fw-bold mb-0">{{ $sponsor->address ?? '—' }}</dd>
                        </div>
                        @if($sponsor->website)
                        <div class="contact-item">
                            <dt class="contact-label">
                                <span class="stat-icon-sm bg-primary-lt text-primary me-2"><i class="fa-solid fa-globe"></i></span>Website
                            </dt>
                            <dd class="fw-bold text-truncate mb-0"><a href="{{ $sponsor->website }}" target="_blank" rel="noopener" class="text-body text-decoration-none">{{ $sponsor->website }}</a></dd>
                        </div>
                        @endif
                        <div class="contact-item">
                            <dt class="contact-label">
                                <span class="stat-icon-sm bg-primary-lt text-primary me-2"><i class="fa-solid fa-palette"></i></span>Portal Brand
                            </dt>
                            <dd class="fw-bold mb-0">
                                <span class="d-inline-flex align-items-center">
                                    <span class="d-inline-block rounded me-2" style="width: 1rem; height: 1rem; background: {{ $sponsor->brandColor() }};"></span>
                                    <code class="font-monospace">{{ strtoupper($sponsor->brandColor()) }}</code>
                                </span>
                            </dd>
                        </div>
                        <div class="contact-item">
                            <dt class="contact-label">
                                <span class="stat-icon-sm bg-primary-lt text-primary me-2"><i class="fa-solid fa-calendar-days"></i></span>Joined
                            </dt>
                            <dd class="fw-bold mb-0">{{ $sponsor->created_at?->format('M d, Y') }}</dd>
                        </div>
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
                                    <i class="fa-solid fa-handshake"></i>
                                </div>
                                <div>
                                    <div class="stat-label text-muted mb-1">Sponsorships</div>
                                    <div class="stat-value fw-bolder text-body">{{ number_format($sponsor->sponsorships_count) }}</div>
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
                                    <i class="fa-solid fa-bullhorn"></i>
                                </div>
                                <div>
                                    <div class="stat-label text-muted mb-1">Campaigns</div>
                                    <div class="stat-value fw-bolder text-body">{{ number_format($sponsor->campaigns_count) }}</div>
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
                                    <i class="fa-solid fa-signal"></i>
                                </div>
                                <div>
                                    <div class="stat-label text-muted mb-1">Sessions Used</div>
                                    <div class="stat-value fw-bolder text-body">{{ number_format($stats['total_sessions']) }}</div>
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
                                    <i class="fa-solid fa-coins"></i>
                                </div>
                                <div>
                                    <div class="stat-label text-muted mb-1">Total Revenue</div>
                                    <div class="stat-value fw-bolder text-body">KSh {{ number_format($stats['revenue'], 2) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card dashboard-card mt-4">
                <div class="card-header">
                    <h2 class="card-title mb-0">
                        <i class="fa-solid fa-circle-info text-primary me-2"></i>Overview
                    </h2>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 col-md-3 mb-3">
                            <div class="small text-muted">Active Sponsorships</div>
                            <div class="fw-bold">{{ number_format($stats['active_sponsorships']) }}</div>
                        </div>
                        <div class="col-6 col-md-3 mb-3">
                            <div class="small text-muted">Status</div>
                            <div class="fw-bold">
                                <span class="badge bg-{{ $sponsor->is_active ? 'success' : 'secondary' }}-lt">{{ $sponsor->is_active ? 'Active' : 'Inactive' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cards mt-2">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="h5 mb-0">Sponsorships ({{ $sponsor->sponsorships_count }})</h2>
                    @can('create-sponsorship')
                    <a href="{{ route('admin.sponsorships.create', ['sponsor_id' => $sponsor->id]) }}" class="btn btn-sm btn-primary d-inline-flex align-items-center">
                        <i class="fa-solid fa-plus me-1"></i>New Sponsorship
                    </a>
                    @endcan
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter table-nowrap card-table mb-0">
                        <thead>
                            <tr>
                                <th>Reference</th>
                                <th>Pack</th>
                                <th class="text-center">Used / Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sponsor->sponsorships()->latest()->limit(10)->get() as $sponsorship)
                            <tr>
                                <td><a href="{{ route('admin.sponsorships.show', $sponsorship) }}" class="fw-bold text-body text-decoration-none">{{ $sponsorship->reference }}</a></td>
                                <td>{{ ucfirst($sponsorship->type) }} · {{ number_format($sponsorship->quantity_purchased) }} sessions</td>
                                <td class="text-center">{{ number_format($sponsorship->quantity_used) }} / {{ number_format($sponsorship->quantity_purchased) }}</td>
                                <td>
                                    <span class="badge bg-{{ $sponsorship->status === 'active' ? 'success' : ($sponsorship->status === 'pending' ? 'warning' : 'secondary') }}-lt">{{ ucfirst($sponsorship->status) }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">No sponsorships yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cards mt-2">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h2 class="h5 mb-0">Campaigns ({{ $sponsor->campaigns_count }})</h2>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter table-nowrap card-table mb-0">
                        <thead>
                            <tr>
                                <th>Campaign</th>
                                <th>Type</th>
                                <th class="text-center">Plays</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sponsor->campaigns()->latest()->limit(10)->get() as $campaign)
                            <tr>
                                <td><a href="{{ route('admin.campaigns.show', $campaign) }}" class="fw-bold text-body text-decoration-none">{{ $campaign->title }}</a></td>
                                <td>{{ ucfirst($campaign->type) }}</td>
                                <td class="text-center">{{ number_format($campaign->current_plays) }}</td>
                                <td>
                                    <span class="badge bg-{{ $campaign->status === 'active' ? 'success' : 'secondary' }}-lt">{{ ucfirst($campaign->status) }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">No campaigns.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
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
