@extends('layouts.admin')

@section('title', 'Sessions')
@section('page-title', 'Sessions')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.sessions.index') }}" class="row g-3 align-items-center">
                        <div class="col-12 col-md-4">
                            <div class="input-group">
                                <span class="input-group-text"><svg class="icon text-secondary" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path></svg></span>
                                <input type="text" name="search" class="form-control" placeholder="Search phone..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-12 col-md-3 col-lg-2">
                            <select name="status" class="form-select">
                                <option value="">All statuses</option>
                                @foreach($statuses as $status)
                                    <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-3 col-lg-2">
                            <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                        </div>
                        <div class="col-12 col-md-2 col-lg-2">
                            <button type="submit" class="btn btn-dark d-inline-flex align-items-center w-100">Filter</button>
                        </div>
                        <div class="col-12 col-lg-2 text-lg-end">
                            @if(request()->hasAny(['search', 'status', 'date']))
                            <a href="{{ route('admin.sessions.index') }}" class="btn btn-link btn-sm text-secondary">Clear</a>
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
                                    <th class="border-0 rounded-start">Session</th>
                                    <th class="border-0">Hotspot</th>
                                    <th class="border-0">Campaign</th>
                                    <th class="border-0">Started</th>
                                    <th class="border-0">Duration</th>
                                    <th class="border-0">Status</th>
                                    <th class="border-0 rounded-end text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sessions as $session)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.sessions.show', $session) }}" class="text-body fw-bold">{{ $session->session_id }}</a>
                                        <div class="small text-muted">{{ $session->phone }}</div>
                                    </td>
                                    <td>{{ $session->hotspot->name ?? '—' }}</td>
                                    <td>{{ $session->campaign->title ?? '—' }}</td>
                                    <td>{{ $session->session_started_at?->format('M d, H:i') }}</td>
                                    <td>{{ gmdate('H:i:s', $session->total_duration ?? 0) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $session->statusObject()->color() }}">{{ $session->statusObject()->label() }}</span>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group">
                                            <a href="{{ route('admin.sessions.show', $session) }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">View</a>
                                            @can('delete-session')
                                            <form method="POST" action="{{ route('admin.sessions.destroy', $session) }}" class="d-inline" onsubmit="return confirm('{{ $session->status === 'active' ? 'Terminate this session?' : 'Delete this session?' }}');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger d-inline-flex align-items-center">{{ $session->status === 'active' ? 'Terminate' : 'Delete' }}</button>
                                            </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="7" class="text-center text-muted py-5">No sessions found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($sessions->hasPages())
                <div class="card-footer border-0 py-2">
                    {{ $sessions->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection
