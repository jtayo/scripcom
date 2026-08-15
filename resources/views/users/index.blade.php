@extends('layouts.admin')

@section('title', 'Users')
@section('page-title', 'Users')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.users.index') }}" class="row g-3 align-items-center">
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="input-group">
                                <span class="input-group-text"><svg class="icon text-secondary" fill="currentColor"
                                        viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                            clip-rule="evenodd"></path>
                                    </svg></span>
                                <input type="text" name="search" class="form-control"
                                    placeholder="Search by name or email..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-12 col-md-4 col-lg-2">
                            <button type="submit" class="btn btn-dark d-inline-flex align-items-center">Filter</button>
                        </div>
                        <div class="col-12 col-lg-6 text-lg-end">
                            @can('create-user')
                                <a href="{{ route('admin.users.create') }}"
                                    class="btn btn-primary d-inline-flex align-items-center">
                                    <svg class="icon me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    New User
                                </a>
                            @endcan
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
                                    <th class="border-0 rounded-start">User</th>
                                    <th class="border-0">Organization</th>
                                    <th class="border-0">Phone</th>
                                    <th class="border-0">Roles</th>
                                    <th class="border-0">Status</th>
                                    <th class="border-0 rounded-end text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    <tr>
                                        <td class="text-body fw-bold">
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $user->avatar ?? asset('img/team/profile-picture-3.jpg') }}"
                                                    class="avatar rounded-circle me-2" alt="{{ $user->name }}">
                                                <div>
                                                    <a href="{{ route('admin.users.show', $user) }}">{{ $user->name }}</a>
                                                    <div class="small text-muted">{{ $user->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $user->organization->name ?? '—' }}</td>
                                        <td>{{ $user->phone ?? '—' }}</td>
                                        <td>
                                            @forelse($user->roles as $role)
                                                <span class="badge bg-secondary text-white">{{ $role->name }}</span>
                                            @empty
                                                <span class="text-muted">—</span>
                                            @endforelse
                                        </td>
                                        <td>
                                            <span
                                                class="badge text-white bg-{{ $user->status === 'active' ? 'success' : 'danger' }}">{{ ucfirst($user->status ?? 'active') }}</span>
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group">
                                                <a href="{{ route('admin.users.show', $user) }}"
                                                    class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">View</a>
                                                @can('update-user')
                                                    <a href="{{ route('admin.users.edit', $user) }}"
                                                        class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">Edit</a>
                                                @endcan
                                                @can('delete-user')
                                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                                        class="d-inline" onsubmit="return confirm('Delete this user?');">
                                                        @csrf @method('DELETE')
                                                        <button type="submit"
                                                            class="btn btn-sm btn-outline-danger d-inline-flex align-items-center">Delete</button>
                                                    </form>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-5">No users found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($users->hasPages())
                    <div class="card-footer border-0 py-2">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
