@extends('layouts.admin')

@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('content')
    @php $user = auth()->user(); @endphp

    <div class="row">
        <div class="col-12 col-xl-4 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    <span class="avatar avatar-xl mb-3" style="background-image: url('{{ $user->avatar ?? asset('img/team/profile-picture-3.jpg') }}')"></span>
                    <h2 class="h5 mb-1">{{ $user->name }}</h2>
                    <p class="text-muted mb-2">{{ $user->email }}</p>
                    <span class="badge bg-primary">{{ $user->getRoleNames()->first() ?? 'No role' }}</span>
                    <hr>
                    <dl class="text-start mb-0">
                        <dt class="text-muted small">Organization</dt>
                        <dd class="mb-3">{{ $user->organization->name ?? '—' }}</dd>
                        <dt class="text-muted small">Phone</dt>
                        <dd class="mb-3">{{ $user->phone ?? '—' }}</dd>
                        <dt class="text-muted small">Member Since</dt>
                        <dd>{{ $user->created_at?->format('M d, Y') }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-8">
            <div class="card mb-4">
                <div class="card-header"><h2 class="h5 mb-0">Profile</h2></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.profile.update') }}">
                        @csrf @method('PATCH')

                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label" for="name">Name <span class="text-danger">*</span></label>
                                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label" for="email">Email</label>
                                <input type="email" id="email" class="form-control" value="{{ $user->email }}" disabled>
                                <div class="form-text">Email cannot be changed.</div>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label" for="phone">Phone</label>
                                <input type="text" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}">
                                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary d-inline-flex align-items-center">Update Profile</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h2 class="h5 mb-0">Change Password</h2></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.profile.password') }}">
                        @csrf @method('PATCH')

                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label" for="current_password">Current Password <span class="text-danger">*</span></label>
                                <input type="password" id="current_password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" required>
                                @error('current_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label class="form-label" for="password">New Password <span class="text-danger">*</span></label>
                                <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label class="form-label" for="password_confirmation">Confirm New Password <span class="text-danger">*</span></label>
                                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary d-inline-flex align-items-center">Change Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
