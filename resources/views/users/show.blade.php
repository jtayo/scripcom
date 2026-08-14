@extends('layouts.admin')

@section('title', $user->name)
@section('page-title', $user->name)

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                    <div class="d-flex align-items-center mb-3 mb-md-0">
                        <span class="avatar avatar-lg me-3" style="background-image: url('{{ $user->avatar ?? asset('img/team/profile-picture-3.jpg') }}')"></span>
                        <div>
                            <h1 class="h4 mb-0">{{ $user->name }}</h1>
                            <span class="text-muted">{{ $user->email }}</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-{{ $user->status === 'active' ? 'success' : 'danger' }} me-2">{{ ucfirst($user->status ?? 'active') }}</span>
                        @can('update-user')
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-primary d-inline-flex align-items-center">Edit</a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-lg-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h2 class="h5 mb-0">Roles</h2>
                </div>
                <div class="card-body">
                    @forelse($user->roles as $role)
                        <span class="badge bg-secondary me-1">{{ $role->name }}</span>
                    @empty
                        <span class="text-muted">No roles assigned.</span>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-8 mb-4">
            <div class="card">
                <div class="card-header">
                    <h2 class="h5 mb-0">Details</h2>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 col-md-3 mb-3">
                            <div class="small text-muted">Phone</div>
                            <div class="fw-bold">{{ $user->phone ?? '—' }}</div>
                        </div>
                        <div class="col-6 col-md-3 mb-3">
                            <div class="small text-muted">Organization</div>
                            <div class="fw-bold">{{ $user->organization->name ?? '—' }}</div>
                        </div>
                        <div class="col-6 col-md-3 mb-3">
                            <div class="small text-muted">Sponsor</div>
                            <div class="fw-bold">{{ $user->sponsor->name ?? '—' }}</div>
                        </div>
                        <div class="col-6 col-md-3 mb-3">
                            <div class="small text-muted">Joined</div>
                            <div class="fw-bold">{{ $user->created_at?->format('M d, Y') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
