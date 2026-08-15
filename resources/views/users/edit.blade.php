@extends('layouts.admin')

@section('title', 'Edit User')
@section('page-title', 'Edit User')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-xl-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <span class="avatar avatar-sm bg-warning-lt text-warning me-2">
                            <i class="fa-solid fa-user-pen"></i>
                        </span>
                        <div>
                            <h2 class="h5 mb-0">Edit User</h2>
                            <div class="small text-muted">Update {{ $user->name }}'s details and permissions</div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.users.update', $user) }}">
                        @csrf @method('PUT')

                        @if($organizations)
                        <div class="mb-3">
                            <label class="form-label" for="organization_id">Organization</label>
                            <div class="input-icon">
                                <span class="input-icon-addon"><i class="fa-solid fa-building"></i></span>
                                <select id="organization_id" name="organization_id" class="form-select @error('organization_id') is-invalid @enderror" style="padding-left: 2.5rem;">
                                    <option value="">None (platform-wide)</option>
                                    @foreach($organizations as $organization)
                                        <option value="{{ $organization->id }}" @selected(old('organization_id', $user->organization_id) == $organization->id)>{{ $organization->name }}</option>
                                    @endforeach
                                </select>
                                @error('organization_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        @endif

                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="name">Name <span class="text-danger">*</span></label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-user"></i></span>
                                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="email">Email <span class="text-danger">*</span></label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-envelope"></i></span>
                                    <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="phone">Phone</label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-phone"></i></span>
                                    <input type="text" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}">
                                    @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="status">Status <span class="text-danger">*</span></label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-circle-check"></i></span>
                                    <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" style="padding-left: 2.5rem;" required>
                                        <option value="active" @selected(old('status', $user->status) === 'active')>Active</option>
                                        <option value="inactive" @selected(old('status', $user->status) === 'inactive')>Inactive</option>
                                    </select>
                                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="password">New Password</label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-key"></i></span>
                                    <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Leave blank to keep current" autocomplete="new-password">
                                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="form-text"><i class="fa-solid fa-circle-info me-1"></i>Only fill this in to set a new password.</div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="password_confirmation">Confirm New Password</label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-key"></i></span>
                                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" autocomplete="new-password">
                                </div>
                            </div>

                            @if(auth()->user()->isSuperAdmin())
                            <div class="col-12">
                                <label class="form-label d-block">Roles</label>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($roles as $role)
                                    <label class="form-check form-check-pill me-0">
                                        <input class="form-check-input" type="checkbox" name="roles[]" id="role_{{ $role->id }}" value="{{ $role->name }}" @checked(in_array($role->name, old('roles', $user->roles->pluck('name')->all())))>
                                        <span class="form-check-label">{{ $role->name }}</span>
                                    </label>
                                    @endforeach
                                </div>
                                @error('roles') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>
                            @endif
                        </div>

                        <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center">
                                <i class="fa-solid fa-arrow-left me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary d-inline-flex align-items-center">
                                <i class="fa-solid fa-floppy-disk me-2"></i>Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
