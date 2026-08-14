@extends('layouts.admin')

@section('title', $sponsor->name)
@section('page-title', $sponsor->name)

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                    <div class="d-flex align-items-center">
                        <span class="avatar avatar-lg me-3" style="background-image: url('{{ asset('img/team/profile-picture-3.jpg') }}')"></span>
                        <div>
                            <h1 class="h4 mb-1">{{ $sponsor->name }}</h1>
                            <span class="text-muted">{{ $sponsor->contact_person ?: '—' }}</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-{{ $sponsor->is_active ? 'success' : 'secondary' }} me-2">{{ $sponsor->is_active ? 'Active' : 'Inactive' }}</span>
                        @can('update-sponsor')
                        <a href="{{ route('admin.sponsors.edit', $sponsor) }}" class="btn btn-sm btn-primary d-inline-flex align-items-center">Edit</a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-md-4 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <h3 class="h6 text-muted mb-1">Sessions Used</h3>
                    <span class="fs-4 fw-bold">{{ number_format($stats['total_sessions']) }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <h3 class="h6 text-muted mb-1">Total Revenue</h3>
                    <span class="fs-4 fw-bold">KSh {{ number_format($stats['revenue'], 2) }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <h3 class="h6 text-muted mb-1">Active Sponsorships</h3>
                    <span class="fs-4 fw-bold">{{ number_format($stats['active_sponsorships']) }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-xl-4 mb-4">
            <div class="card">
                <div class="card-header"><h2 class="h5 mb-0">Contact</h2></div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-4 text-muted">Email</dt>
                        <dd class="col-8 text-truncate">{{ $sponsor->email ?? '—' }}</dd>
                        <dt class="col-4 text-muted">Phone</dt>
                        <dd class="col-8">{{ $sponsor->phone ?? '—' }}</dd>
                        <dt class="col-4 text-muted">Address</dt>
                        <dd class="col-8">{{ $sponsor->address ?? '—' }}</dd>
                        <dt class="col-4 text-muted">Website</dt>
                        <dd class="col-8 text-truncate">@if($sponsor->website)<a href="{{ $sponsor->website }}" target="_blank" rel="noopener">{{ $sponsor->website }}</a>@else—@endif</dd>
                        <dt class="col-4 text-muted">Joined</dt>
                        <dd class="col-8">{{ $sponsor->created_at->format('M d, Y') }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="h5 mb-0">Sponsorships ({{ $sponsor->sponsorships_count }})</h2>
                    @can('create-sponsorship')
                    <a href="{{ route('admin.sponsorships.create', ['sponsor_id' => $sponsor->id]) }}" class="btn btn-sm btn-primary d-inline-flex align-items-center">New Sponsorship</a>
                    @endcan
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table mb-0">
                        <thead class="">
                            <tr>
                                <th class="border-bottom">Reference</th>
                                <th class="border-bottom">Pack</th>
                                <th class="border-bottom">Used / Total</th>
                                <th class="border-bottom">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sponsor->sponsorships()->latest()->limit(10)->get() as $sponsorship)
                            <tr>
                                <td><a href="{{ route('admin.sponsorships.show', $sponsorship) }}" class="fw-bold text-body">{{ $sponsorship->reference_code }}</a></td>
                                <td>{{ ucfirst($sponsorship->pack_type) }} · {{ number_format($sponsorship->quantity_purchased) }} sessions</td>
                                <td>{{ number_format($sponsorship->quantity_used) }} / {{ number_format($sponsorship->quantity_purchased) }}</td>
                                <td><span class="badge bg-{{ $sponsorship->status === 'active' ? 'success' : ($sponsorship->status === 'pending' ? 'warning' : 'secondary') }}">{{ ucfirst($sponsorship->status) }}</span></td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">No sponsorships yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h2 class="h5 mb-0">Campaigns ({{ $sponsor->campaigns_count }})</h2></div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table mb-0">
                        <thead class="">
                            <tr>
                                <th class="border-bottom">Campaign</th>
                                <th class="border-bottom">Type</th>
                                <th class="border-bottom">Plays</th>
                                <th class="border-bottom">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sponsor->campaigns()->latest()->limit(10)->get() as $campaign)
                            <tr>
                                <td><a href="{{ route('admin.campaigns.show', $campaign) }}" class="fw-bold text-body">{{ $campaign->title }}</a></td>
                                <td>{{ ucfirst($campaign->type) }}</td>
                                <td>{{ number_format($campaign->current_plays) }}</td>
                                <td><span class="badge bg-{{ $campaign->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($campaign->status) }}</span></td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">No campaigns.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
