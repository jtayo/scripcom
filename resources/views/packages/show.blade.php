@extends('layouts.admin')

@section('title', $package->name)
@section('page-title', $package->name)

@section('content')
    <div class="mb-3">
        <a href="{{ route('admin.packages.index') }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">
            <i class="fa-solid fa-arrow-left me-1"></i>Back to Wi-Fi Packages
        </a>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                    <div class="d-flex align-items-center">
                        <span class="avatar avatar-lg bg-primary-lt text-primary me-3">
                            <i class="fa-solid fa-wifi"></i>
                        </span>
                        <div>
                            <h1 class="h4 mb-1">{{ $package->name }}</h1>
                            <div class="text-muted d-flex align-items-center flex-wrap">
                                <span class="badge bg-{{ $package->accessType()->color() }}-lt me-2">{{ $package->accessType()->label() }}</span>
                                <span class="d-inline-flex align-items-center me-2"><i class="fa-solid fa-barcode me-1 text-secondary"></i>{{ $package->code }}</span>
                                @if($package->organization)
                                    <span class="d-inline-flex align-items-center"><i class="fa-solid fa-building me-1 text-secondary"></i>{{ $package->organization->name }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mt-3 mt-md-0">
                        <span class="badge bg-{{ $package->is_active ? 'success' : 'secondary' }}-lt me-2">{{ $package->is_active ? 'Active' : 'Inactive' }}</span>
                        @can('update-package')
                        <a href="{{ route('admin.packages.edit', $package) }}" class="btn btn-sm btn-primary d-inline-flex align-items-center">
                            <i class="fa-solid fa-pen me-1"></i>Edit
                        </a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cards">
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-primary-lt text-primary me-3">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                        <div>
                            <div class="stat-label text-muted mb-1">Duration</div>
                            <div class="stat-value fw-bolder text-body">{{ $package->durationLabel() }}</div>
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
                            <i class="fa-solid fa-money-bill"></i>
                        </div>
                        <div>
                            <div class="stat-label text-muted mb-1">Price</div>
                            <div class="stat-value fw-bolder text-body">{{ $package->priceLabel() }}</div>
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
                            <i class="fa-solid fa-tower-broadcast"></i>
                        </div>
                        <div>
                            <div class="stat-label text-muted mb-1">Bandwidth</div>
                            <div class="stat-value fw-bolder text-body">
                                {{ $package->bandwidth_down_kbps ? number_format($package->bandwidth_down_kbps / 1024, 0) . ' Mbps' : '—' }}
                            </div>
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
                            <i class="fa-solid fa-hard-drive"></i>
                        </div>
                        <div>
                            <div class="stat-label text-muted mb-1">Data Limit</div>
                            <div class="stat-value fw-bolder text-body">
                                {{ $package->data_limit_mb ? number_format($package->data_limit_mb) . ' MB' : 'Unlimited' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cards">
        <div class="col-12 col-xl-5">
            <div class="card dashboard-card">
                <div class="card-header">
                    <h2 class="card-title mb-0">
                        <i class="fa-solid fa-circle-info text-primary me-2"></i>Details
                    </h2>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-5 text-muted">Organization</dt>
                        <dd class="col-7">
                            <span class="d-inline-flex align-items-center">
                                <i class="fa-solid fa-building me-1 text-secondary"></i>
                                {{ $package->organization->name ?? '—' }}
                            </span>
                        </dd>
                        <dt class="col-5 text-muted">Access Type</dt>
                        <dd class="col-7">
                            <span class="badge bg-{{ $package->accessType()->color() }}-lt">{{ $package->accessType()->label() }}</span>
                        </dd>
                        <dt class="col-5 text-muted">Code</dt>
                        <dd class="col-7">{{ $package->code }}</dd>
                        <dt class="col-5 text-muted">Duration</dt>
                        <dd class="col-7">{{ $package->durationLabel() }}</dd>
                        <dt class="col-5 text-muted">Price</dt>
                        <dd class="col-7">{{ $package->priceLabel() }}</dd>
                        <dt class="col-5 text-muted">Downstream</dt>
                        <dd class="col-7">{{ $package->bandwidth_down_kbps ? number_format($package->bandwidth_down_kbps / 1024, 0) . ' Mbps' : '—' }}</dd>
                        <dt class="col-5 text-muted">Upstream</dt>
                        <dd class="col-7">{{ $package->bandwidth_up_kbps ? number_format($package->bandwidth_up_kbps / 1024, 0) . ' Mbps' : '—' }}</dd>
                        <dt class="col-5 text-muted">Data Limit</dt>
                        <dd class="col-7">{{ $package->data_limit_mb ? number_format($package->data_limit_mb) . ' MB' : 'Unlimited' }}</dd>
                        <dt class="col-5 text-muted">Devices</dt>
                        <dd class="col-7">{{ $package->simultaneous_devices }}</dd>
                        <dt class="col-5 text-muted">Status</dt>
                        <dd class="col-7">
                            <span class="badge bg-{{ $package->is_active ? 'success' : 'secondary' }}-lt">{{ $package->is_active ? 'Active' : 'Inactive' }}</span>
                        </dd>
                        <dt class="col-5 text-muted">Created</dt>
                        <dd class="col-7">{{ $package->created_at?->format('M d, Y') ?? '—' }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-7">
            <div class="card dashboard-card h-100">
                <div class="card-header">
                    <h2 class="card-title mb-0">
                        <i class="fa-solid fa-tower-broadcast text-primary me-2"></i>Recent Sessions ({{ $package->sessions->count() }})
                    </h2>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter table-nowrap card-table mb-0">
                        <thead>
                            <tr>
                                <th>Phone</th>
                                <th>Hotspot</th>
                                <th>Started</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($package->sessions as $session)
                            <tr>
                                <td class="text-body">{{ $session->phone ?? '—' }}</td>
                                <td>{{ $session->hotspot->name ?? '—' }}</td>
                                <td class="small text-muted">{{ $session->session_started_at?->format('M d, Y H:i') }}</td>
                                <td>
                                    <span class="badge bg-{{ $session->status === 'active' ? 'success' : 'secondary' }}-lt">
                                        <span class="status-dot @if($session->status === 'active') status-dot-animated @endif me-1"></span>
                                        {{ ucfirst($session->status) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    <i class="fa-solid fa-wifi text-secondary mb-1 d-block" style="font-size: 1.5rem;"></i>
                                    No sessions yet.
                                </td>
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

        .stat-value {
            font-size: 1.5rem;
            line-height: 1.15;
        }

        .stat-label {
            font-size: .75rem;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .dashboard-card .card-header {
            background: transparent;
            border-bottom: 1px solid var(--tblr-border-color);
            padding: .9rem 1.25rem;
            min-height: 0;
        }
    </style>
@endpush
