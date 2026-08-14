@extends('layouts.admin')

@section('title', 'Events')
@section('page-title', 'Events')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.events.index') }}" class="row g-3 align-items-center">
                        <div class="col-12 col-md-4 col-lg-3">
                            <select name="event_type" class="form-select">
                                <option value="">All event types</option>
                                @foreach($eventTypes as $type)
                                    <option value="{{ $type }}" @selected(request('event_type') === $type)>{{ \App\Enums\EventType::tryFrom($type)?->label() ?? $type }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-3 col-lg-2">
                            <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                        </div>
                        <div class="col-12 col-md-3 col-lg-2">
                            <button type="submit" class="btn btn-dark d-inline-flex align-items-center">Filter</button>
                        </div>
                        <div class="col-12 col-md-2 col-lg-2">
                            @if(request()->hasAny(['event_type', 'date']))
                            <a href="{{ route('admin.events.index') }}" class="btn btn-link btn-sm text-secondary">Clear</a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-vcenter table-nowrap mb-0">
                            <thead class="">
                                <tr>
                                    <th class="border-0 rounded-start">Event</th>
                                    <th class="border-0">Type</th>
                                    <th class="border-0">Hotspot</th>
                                    <th class="border-0">Campaign</th>
                                    <th class="border-0">IP</th>
                                    <th class="border-0">Occurred</th>
                                    <th class="border-0 rounded-end text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($events as $event)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.events.show', $event) }}" class="text-body fw-bold">#{{ $event->id }}</a>
                                        <div class="small text-muted">{{ $event->session_id ?? '' }}</div>
                                    </td>
                                    <td>
                                        @php $evt = \App\Enums\EventType::tryFrom($event->event_type); @endphp
                                        <span class="badge bg-{{ $evt && in_array($evt, [\App\Enums\EventType::ErrorOccurred, \App\Enums\EventType::SessionFailed]) ? 'danger' : 'secondary' }}">{{ $evt?->label() ?? $event->event_type }}</span>
                                    </td>
                                    <td>{{ $event->hotspot->name ?? '—' }}</td>
                                    <td>{{ $event->campaign->title ?? '—' }}</td>
                                    <td>{{ $event->ip_address ?? '—' }}</td>
                                    <td>{{ $event->occurred_at?->format('M d, H:i:s') }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.events.show', $event) }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">View</a>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="7" class="text-center text-muted py-5">No events found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($events->hasPages())
                <div class="card-footer border-0 py-2">
                    {{ $events->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection
