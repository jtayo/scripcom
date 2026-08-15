@extends('layouts.admin')

@section('title', 'Campaigns')
@section('page-title', 'Campaigns')

@section('content')
    <div class="row row-cards">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        Campaigns
                        <span class="badge bg-secondary-lt ms-2">{{ $campaigns->total() }}</span>
                    </div>
                    <div class="card-actions d-flex align-items-center gap-2">
                        <form method="GET" action="{{ route('admin.campaigns.index') }}" class="d-flex gap-1">
                            <div class="input-icon">
                                <span class="input-icon-addon"><i class="ti ti-search"></i></span>
                                <input type="text" name="search" class="form-control" style="min-width: 220px;"
                                       placeholder="Search campaigns..."
                                       value="{{ request('search') }}" aria-label="Search campaigns">
                                @if(request('search'))
                                <a href="{{ route('admin.campaigns.index') }}" class="input-icon-addon text-decoration-none" title="Clear search">
                                    <i class="ti ti-x"></i>
                                </a>
                                @endif
                            </div>
                            <select name="status" class="form-select" style="width: auto;" aria-label="Filter by status">
                                <option value="">All statuses</option>
                                @foreach(['active', 'paused', 'ended', 'draft'] as $status)
                                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-outline-secondary d-inline-flex align-items-center">
                                <i class="ti ti-filter me-1"></i>Filter
                            </button>
                            @if(request('status') || request('search'))
                            <a href="{{ route('admin.campaigns.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center" title="Clear filters">
                                <i class="ti ti-x"></i>
                            </a>
                            @endif
                        </form>
                        @can('create-campaign')
                        <a href="{{ route('admin.campaigns.create') }}" class="btn btn-primary d-inline-flex align-items-center">
                            <i class="ti ti-plus me-1"></i>New Campaign
                        </a>
                        @endcan
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter table-nowrap card-table mb-0">
                        <thead>
                            <tr>
                                <th>Campaign</th>
                                <th>Type</th>
                                <th>Sponsor</th>
                                <th class="text-center">Plays</th>
                                <th class="text-center">Sessions</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($campaigns as $campaign)
                            @php $statusColor = match($campaign->status) { 'active' => 'success', 'paused' => 'warning', 'ended' => 'secondary', default => 'dark' }; @endphp
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="avatar avatar-sm bg-primary-lt text-primary me-2">
                                            <i class="ti ti-speakerphone"></i>
                                        </span>
                                        <div>
                                            <a href="{{ route('admin.campaigns.show', $campaign) }}" class="text-body fw-bold text-decoration-none">{{ $campaign->title }}</a>
                                            <div class="small text-muted">
                                                {{ $campaign->content_type }} &middot; {{ $campaign->duration_seconds }}s
                                                @if($campaign->is_mandatory)
                                                    <span class="text-primary">&middot; mandatory</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge bg-secondary-lt">{{ ucfirst($campaign->type) }}</span></td>
                                <td>
                                    <span class="d-inline-flex align-items-center text-muted">
                                        <i class="ti ti-building-community me-1 text-secondary"></i>
                                        {{ $campaign->sponsor->name ?? '—' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="d-inline-flex align-items-center text-muted">
                                        <i class="ti ti-player-play me-1 text-secondary"></i>
                                        {{ number_format($campaign->current_plays) }}
                                    </span>
                                </td>
                                <td class="text-center"><span class="badge bg-secondary-lt">{{ number_format($campaign->sessions_count) }}</span></td>
                                <td>
                                    <span class="badge bg-{{ $statusColor }}-lt">{{ ucfirst($campaign->status) }}</span>
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ route('admin.campaigns.show', $campaign) }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center" title="View">
                                            <i class="ti ti-eye me-1"></i>View
                                        </a>
                                        @can('update-campaign')
                                        <a href="{{ route('admin.campaigns.edit', $campaign) }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center" title="Edit">
                                            <i class="ti ti-edit me-1"></i>Edit
                                        </a>
                                        @endcan
                                        @can('delete-campaign')
                                        <form method="POST" action="{{ route('admin.campaigns.destroy', $campaign) }}" class="d-inline" onsubmit="return confirm('Delete this campaign?');">
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
                                <td colspan="7" class="text-center text-muted py-5">
                                    <div class="my-4">
                                        <i class="ti ti-speakerphone text-secondary" style="font-size: 2.5rem;"></i>
                                        <div class="mt-2">No campaigns found.</div>
                                        @if(request('search') || request('status'))
                                        <div class="small text-secondary mt-1">
                                            Try a different filter or <a href="{{ route('admin.campaigns.index') }}" class="text-primary">clear filters</a>.
                                        </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($campaigns->hasPages())
                <div class="card-footer py-3">
                    {{ $campaigns->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection
