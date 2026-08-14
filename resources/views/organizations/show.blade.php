@extends('layouts.admin')

@section('title', $organization->name)
@section('page-title', $organization->name)

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                    <div class="d-flex align-items-center mb-3 mb-md-0">
                        <span class="avatar avatar-lg me-3" style="background-image: url('{{ $organization->logo ?? asset('img/team/profile-picture-1.jpg') }}')"></span>
                        <div>
                            <h1 class="h4 mb-0">{{ $organization->name }}</h1>
                            <span class="text-muted">{{ $organization->type ?? 'Organization' }}@if($organization->county) · {{ $organization->county }}@endif</span>
                        </div>
                    </div>
                    <div class="d-flex">
                        <span class="badge bg-{{ $organization->is_active ? 'success' : 'danger' }} me-2 align-self-center">{{ $organization->is_active ? 'Active' : 'Inactive' }}</span>
                        @can('update-organization')
                        <a href="{{ route('admin.organizations.edit', $organization) }}" class="btn btn-sm btn-primary d-inline-flex align-items-center">Edit</a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-xl-3 mb-4">
            <div class="card">
                <div class="card-body">
                    <h2 class="h6 text-uppercase text-muted mb-3">Contact</h2>
                    <div class="mb-2"><strong>Email:</strong> <span class="text-muted">{{ $organization->email ?? '—' }}</span></div>
                    <div class="mb-2"><strong>Phone:</strong> <span class="text-muted">{{ $organization->phone ?? '—' }}</span></div>
                    <div class="mb-2"><strong>Address:</strong> <span class="text-muted">{{ $organization->address ?? '—' }}</span></div>
                    <div class="mb-2"><strong>Location:</strong> <span class="text-muted">{{ collect([$organization->city, $organization->county, $organization->country])->filter()->join(', ') ?: '—' }}</span></div>
                    @if($organization->website)
                    <div class="mb-2"><strong>Website:</strong> <a href="{{ $organization->website }}" target="_blank" rel="noopener">{{ $organization->website }}</a></div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-9">
            <div class="row">
                <div class="col-12 col-md-3 mb-4">
                    <div class="card">
                        <div class="card-body p-3">
                            <h3 class="h6 text-muted mb-1">Users</h3>
                            <span class="fs-4 fw-bold">{{ number_format($organization->users_count) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-3 mb-4">
                    <div class="card">
                        <div class="card-body p-3">
                            <h3 class="h6 text-muted mb-1">Hotspots</h3>
                            <span class="fs-4 fw-bold">{{ number_format($organization->hotspots_count) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-3 mb-4">
                    <div class="card">
                        <div class="card-body p-3">
                            <h3 class="h6 text-muted mb-1">Campaigns</h3>
                            <span class="fs-4 fw-bold">{{ number_format($organization->campaigns_count) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-3 mb-4">
                    <div class="card">
                        <div class="card-body p-3">
                            <h3 class="h6 text-muted mb-1">Sponsorships</h3>
                            <span class="fs-4 fw-bold">{{ number_format($organization->sponsorships_count) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2 class="h5 mb-0">Details</h2>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 col-md-3 mb-3">
                            <div class="small text-muted">Postal Code</div>
                            <div class="fw-bold">{{ $organization->postal_code ?? '—' }}</div>
                        </div>
                        <div class="col-6 col-md-3 mb-3">
                            <div class="small text-muted">Slug</div>
                            <div class="fw-bold">{{ $organization->slug }}</div>
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
