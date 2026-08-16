@extends('layouts.admin')

@section('title', 'Notifications')
@section('page-title', 'Notifications')

@section('content')
    <div class="row row-cards">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        Notifications
                        <span class="badge bg-secondary-lt ms-2">{{ $notifications->total() }}</span>
                    </div>
                    <div class="card-actions d-flex align-items-center gap-2">
                        <form method="GET" action="{{ route('admin.notifications.index') }}" class="d-flex gap-1">
                            <select name="status" class="form-select" style="width: auto;" aria-label="Filter by read status">
                                <option value="">All</option>
                                <option value="unread" @selected(request('status') === 'unread')>Unread</option>
                                <option value="read" @selected(request('status') === 'read')>Read</option>
                            </select>
                            <button type="submit" class="btn btn-outline-secondary d-inline-flex align-items-center">
                                <i class="ti ti-filter me-1"></i>Filter
                            </button>
                            @if(request('status'))
                                <a href="{{ route('admin.notifications.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center" title="Clear filter">
                                    <i class="ti ti-x"></i>
                                </a>
                            @endif
                        </form>
                        @if($unreadCount > 0)
                            <form method="POST" action="{{ route('admin.notifications.read-all') }}">
                                @csrf
                                <button type="submit" class="btn btn-outline-secondary d-inline-flex align-items-center">
                                    <i class="ti ti-check-all me-1"></i>Mark all as read
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
                <div class="list-group list-group-flush">
                    @forelse($notifications as $notification)
                        @php
                            $data = $notification->data;
                            $levelColors = [
                                'danger' => 'text-danger bg-danger-lt',
                                'warning' => 'text-warning bg-warning-lt',
                                'success' => 'text-success bg-success-lt',
                                'info' => 'text-primary bg-primary-lt',
                            ];
                            $level = $data['level'] ?? 'info';
                            $color = $levelColors[$level] ?? $levelColors['info'];
                        @endphp
                        <div class="list-group-item d-flex {{ $notification->read_at ? '' : 'bg-azure-lt' }}">
                            <span class="avatar avatar-sm rounded {{ $color }} me-3">
                                <i class="ti ti-{{ $data['icon'] ?? 'bell' }}"></i>
                            </span>
                            <div class="flex-fill">
                                <div class="d-flex align-items-center">
                                    <a href="{{ route('admin.notifications.show', $notification) }}" class="fw-semibold text-body text-decoration-none">
                                        {{ $data['title'] ?? 'Notification' }}
                                    </a>
                                    <span class="ms-auto small text-secondary">{{ $notification->created_at->format('d M Y, H:i') }}</span>
                                </div>
                                <div class="text-secondary small mt-1">{{ $data['message'] ?? '' }}</div>
                            </div>
                            <div class="d-inline-flex align-items-center gap-1 ms-3">
                                @unless($notification->read_at)
                                    <form method="POST" action="{{ route('admin.notifications.read', $notification) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-secondary" title="Mark as read">
                                            <i class="ti ti-check"></i>
                                        </button>
                                    </form>
                                @endunless
                                <form method="POST" action="{{ route('admin.notifications.destroy', $notification) }}"
                                    onsubmit="return confirm('Delete this notification?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="list-group-item text-center text-muted py-5">
                            <div class="my-4">
                                <i class="ti ti-bell-off text-secondary" style="font-size: 2.5rem;"></i>
                                <div class="mt-2">No notifications found.</div>
                            </div>
                        </div>
                    @endforelse
                </div>
                @if($notifications->hasPages())
                    <div class="card-footer py-3">
                        {{ $notifications->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
