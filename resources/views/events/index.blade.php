@extends('layouts.admin')

@section('title', 'Events')
@section('page-title', 'Events')

@section('content')
    <div class="row row-cards">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        Events
                        <span class="badge bg-secondary-lt ms-2">{{ $events->total() }}</span>
                    </div>
                    <div class="card-actions d-flex align-items-center gap-2">
                        <form method="GET" action="{{ route('admin.events.index') }}" class="d-flex gap-1">
                            <select name="event_type" class="form-select" style="width: auto;" aria-label="Filter by event type">
                                <option value="">All event types</option>
                                @foreach($eventTypes as $type)
                                    <option value="{{ $type }}" @selected(request('event_type') === $type)>{{ \App\Enums\EventType::tryFrom($type)?->label() ?? $type }}</option>
                                @endforeach
                            </select>
                            <input type="date" name="date" class="form-control" style="width: auto;"
                                   value="{{ request('date') }}" aria-label="Filter by date">
                            <button type="submit" class="btn btn-outline-secondary d-inline-flex align-items-center">
                                <i class="ti ti-filter me-1"></i>Filter
                            </button>
                            @if(request('event_type') || request('date'))
                            <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center" title="Clear filters">
                                <i class="ti ti-x"></i>
                            </a>
                            @endif
                        </form>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter table-nowrap card-table mb-0">
                        <thead>
                            <tr>
                                <th>Event</th>
                                <th>Type</th>
                                <th>Hotspot</th>
                                <th>Campaign</th>
                                <th>IP</th>
                                <th>Occurred</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($events as $event)
                            @php $evt = \App\Enums\EventType::tryFrom($event->event_type); @endphp
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="avatar avatar-sm bg-primary-lt text-primary me-2">
                                            <i class="ti ti-calendar-event"></i>
                                        </span>
                                        <div>
                                            <a href="{{ route('admin.events.show', $event) }}" class="text-body fw-bold text-decoration-none">#{{ $event->id }}</a>
                                            @if($event->session_id)
                                            <div class="small text-muted">{{ $event->session_id }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $evt && $evt === \App\Enums\EventType::ErrorOccurred ? 'danger' : 'secondary' }}-lt">{{ $evt?->label() ?? $event->event_type }}</span>
                                </td>
                                <td>
                                    <span class="d-inline-flex align-items-center text-muted">
                                        <i class="ti ti-map-pin me-1 text-secondary"></i>
                                        {{ $event->hotspot->name ?? '—' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="d-inline-flex align-items-center text-muted">
                                        <i class="ti ti-speakerphone me-1 text-secondary"></i>
                                        {{ $event->campaign->title ?? '—' }}
                                    </span>
                                </td>
                                <td class="text-muted">{{ $event->ip_address ?? '—' }}</td>
                                <td class="small text-muted">{{ $event->occurred_at?->format('M d, H:i:s') }}</td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <a href="{{ route('admin.events.show', $event) }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center" title="View">
                                            <i class="ti ti-eye me-1"></i>View
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <div class="my-4">
                                        <i class="ti ti-calendar-event text-secondary" style="font-size: 2.5rem;"></i>
                                        <div class="mt-2">No events found.</div>
                                        @if(request('event_type') || request('date'))
                                        <div class="small text-secondary mt-1">
                                            Try a different filter or <a href="{{ route('admin.events.index') }}" class="text-primary">clear filters</a>.
                                        </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($events->hasPages())
                <div class="card-footer py-3 border-top-0">
                    {{ $events->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection
