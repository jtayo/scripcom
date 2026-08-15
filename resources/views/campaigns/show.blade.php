@extends('layouts.admin')

@section('title', $campaign->title)
@section('page-title', $campaign->title)

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                    <div>
                        <h1 class="h4 mb-1">{{ $campaign->title }}</h1>
                        <div class="text-muted">
                            <span class="badge bg-secondary">{{ ucfirst($campaign->type) }}</span>
                            @if($campaign->sponsor)<span class="ms-2">Sponsored by <strong>{{ $campaign->sponsor->name }}</strong></span>@endif
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-{{ $campaign->status === 'active' ? 'success' : ($campaign->status === 'paused' ? 'warning' : 'secondary') }} me-2">{{ ucfirst($campaign->status) }}</span>
                        @can('update-campaign')
                        <a href="{{ route('admin.campaigns.edit', $campaign) }}" class="btn btn-sm btn-primary d-inline-flex align-items-center">Edit</a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-md-6 col-xl-3 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <h3 class="h6 text-muted mb-1">Total Plays</h3>
                    <span class="fs-4 fw-bold">{{ number_format($stats['total_plays']) }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <h3 class="h6 text-muted mb-1">Sessions</h3>
                    <span class="fs-4 fw-bold">{{ number_format($stats['total_sessions']) }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <h3 class="h6 text-muted mb-1">Completions</h3>
                    <span class="fs-4 fw-bold">{{ number_format($stats['completions']) }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <h3 class="h6 text-muted mb-1">Avg Watch (s)</h3>
                    <span class="fs-4 fw-bold">{{ number_format($stats['avg_watch'], 1) }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-xl-8 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="h5 mb-0">Plays (14 days)</h2>
                </div>
                <div class="card-body">
                    <div class="chart" id="chart-daily" style="height: 250px;"></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-4 mb-4">
            <div class="card">
                <div class="card-header"><h2 class="h5 mb-0">Details</h2></div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-5 text-muted">Organization</dt>
                        <dd class="col-7">{{ $campaign->organization->name ?? '—' }}</dd>
                        <dt class="col-5 text-muted">Content</dt>
                        <dd class="col-7 text-truncate">{{ $campaign->content_type }}@if($campaign->content_url) · <a href="{{ $campaign->content_url }}" target="_blank" rel="noopener">view</a>@endif</dd>
                        <dt class="col-5 text-muted">Duration</dt>
                        <dd class="col-7">{{ $campaign->duration_seconds }}s</dd>
                        <dt class="col-5 text-muted">Priority</dt>
                        <dd class="col-7">{{ $campaign->priority ?? 0 }}</dd>
                        <dt class="col-5 text-muted">Plays</dt>
                        <dd class="col-7">{{ number_format($campaign->current_plays) }}@if($campaign->max_plays) / {{ number_format($campaign->max_plays) }}@endif</dd>
                        <dt class="col-5 text-muted">Created By</dt>
                        <dd class="col-7">{{ $campaign->creator->name ?? '—' }}</dd>
                        <dt class="col-5 text-muted">Starts</dt>
                        <dd class="col-7">{{ $campaign->starts_at?->format('M d, Y H:i') ?? '—' }}</dd>
                        <dt class="col-5 text-muted">Ends</dt>
                        <dd class="col-7">{{ $campaign->ends_at?->format('M d, Y H:i') ?? '—' }}</dd>
                        <dt class="col-5 text-muted">Skip / Mandatory</dt>
                        <dd class="col-7">{{ $campaign->skip_allowed ? 'Yes' : 'No' }} / {{ $campaign->is_mandatory ? 'Yes' : 'No' }}</dd>
                        @if($campaign->redirect_url)
                        <dt class="col-5 text-muted">Redirect</dt>
                        <dd class="col-7 text-truncate"><a href="{{ $campaign->redirect_url }}" target="_blank" rel="noopener">{{ $campaign->redirect_url }}</a></dd>
                        @endif
                    </dl>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header"><h2 class="h5 mb-0">Targeted Hotspots</h2></div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table mb-0">
                        <tbody>
                            @forelse($campaign->hotspots as $hotspot)
                            <tr>
                                <td>
                                    <span class="fw-bold text-body">{{ $hotspot->name }}</span>
                                    <span class="d-block small text-muted">#{{ $hotspot->router_id ?? '—' }}</span>
                                </td>
                                <td class="text-end"><span class="badge bg-{{ $hotspot->status === 'online' ? 'success' : 'secondary' }}">{{ ucfirst($hotspot->status) }}</span></td>
                            </tr>
                            @empty
                            <tr><td class="text-center text-muted py-4">All hotspots</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('chart-daily').getContext('2d');
    const gradient = ctx.createLinearGradient(0, 0, 0, 250);
    gradient.addColorStop(0, 'rgba(32, 107, 196, .3)');
    gradient.addColorStop(1, 'rgba(32, 107, 196, 0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($daily->keys()->map(fn ($d) => \Illuminate\Support\Carbon::parse($d)->format('d M'))),
            datasets: [{
                label: 'Plays',
                data: @json($daily->values()),
                borderColor: '#206bc4',
                backgroundColor: gradient,
                borderWidth: 2,
                pointRadius: 0,
                pointBackgroundColor: '#206bc4',
                fill: true,
                tension: .35,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(17, 24, 39, .9)',
                    padding: 10,
                    displayColors: false,
                    callbacks: { title: items => items[0].label, label: item => ' ' + item.parsed.y + ' plays' }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: '#9aa7b0', maxTicksLimit: 7, font: { size: 11 } }
                },
                y: {
                    beginAtZero: true,
                    ticks: { color: '#9aa7b0', precision: 0, font: { size: 11 } },
                    grid: { color: 'rgba(17, 24, 39, .06)' }
                }
            }
        }
    });
});
</script>
@endpush
