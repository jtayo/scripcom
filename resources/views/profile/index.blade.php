@extends('layouts.admin')

@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('content')
    @php $user = auth()->user(); @endphp

    <div class="row g-3">
        <div class="col-12 col-xl-4">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <span class="avatar avatar-lg me-3" style="background-image: url('{{ $user->avatar ?? asset('img/team/profile-picture-3.jpg') }}')"></span>
                        <div>
                            <h2 class="h5 mb-1">{{ $user->name }}</h2>
                            <div class="small text-muted mb-1">{{ $user->email }}</div>
                            <span class="badge bg-primary">{{ $user->getRoleNames()->first() ?? 'No role' }}</span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <dl class="mb-0">
                        <dt class="text-muted small">Organization</dt>
                        <dd class="mb-3">{{ $user->organization->name ?? '—' }}</dd>
                        <dt class="text-muted small">Phone</dt>
                        <dd class="mb-3">{{ $user->phone ?? '—' }}</dd>
                        <dt class="text-muted small">Member Since</dt>
                        <dd class="mb-0">{{ $user->created_at?->format('M d, Y') }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-8">
            <div class="card mb-3">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <span class="avatar avatar-sm bg-primary-lt text-primary me-2">
                            <i class="fa-solid fa-user"></i>
                        </span>
                        <div>
                            <h2 class="h5 mb-0">Profile</h2>
                            <div class="small text-muted">Update your contact details</div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.profile.update') }}">
                        @csrf @method('PATCH')

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label" for="name">Name <span class="text-danger">*</span></label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-user"></i></span>
                                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="email">Email</label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-envelope"></i></span>
                                    <input type="email" id="email" class="form-control" value="{{ $user->email }}" disabled>
                                </div>
                                <div class="form-text"><i class="fa-solid fa-circle-info me-1"></i>Email cannot be changed.</div>
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="phone">Phone</label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-phone"></i></span>
                                    <input type="text" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}">
                                    @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary d-inline-flex align-items-center">
                                <i class="fa-solid fa-arrow-left me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary d-inline-flex align-items-center">
                                <i class="fa-solid fa-floppy-disk me-2"></i>Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <span class="avatar avatar-sm bg-warning-lt text-warning me-2">
                            <i class="fa-solid fa-key"></i>
                        </span>
                        <div>
                            <h2 class="h5 mb-0">Change Password</h2>
                            <div class="small text-muted">Use a strong, unique password</div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.profile.password') }}">
                        @csrf @method('PATCH')

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label" for="current_password">Current Password <span class="text-danger">*</span></label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-lock"></i></span>
                                    <input type="password" id="current_password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" required autocomplete="current-password">
                                    @error('current_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="password">New Password <span class="text-danger">*</span></label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-key"></i></span>
                                    <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="new-password">
                                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="password_confirmation">Confirm New Password <span class="text-danger">*</span></label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-key"></i></span>
                                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required autocomplete="new-password">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary d-inline-flex align-items-center">
                                <i class="fa-solid fa-arrow-left me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary d-inline-flex align-items-center">
                                <i class="fa-solid fa-floppy-disk me-2"></i>Change Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
