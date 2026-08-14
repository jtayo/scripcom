@extends('layouts.admin')

@section('title', 'New User')
@section('page-title', 'New User')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-xl-8">
            <div class="card">
                <div class="card-header">
                    <h2 class="h5 mb-0">User Details</h2>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.users.store') }}">
                        @csrf

                        <div class="row">
                            @if($organizations)
                            <div class="col-12 mb-3">
                                <label class="form-label" for="organization_id">Organization</label>
                                <select id="organization_id" name="organization_id" class="form-select @error('organization_id') is-invalid @enderror">
                                    <option value="">None (platform-wide)</option>
                                    @foreach($organizations as $organization)
                                        <option value="{{ $organization->id }}" @selected(old('organization_id') == $organization->id)>{{ $organization->name }}</option>
                                    @endforeach
                                </select>
                                @error('organization_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            @endif

                            <div class="col-12 col-md-6 mb-3">
                                <label class="form-label" for="name">Name <span class="text-danger">*</span></label>
                                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label class="form-label" for="email">Email <span class="text-danger">*</span></label>
                                <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label class="form-label" for="phone">Phone</label>
                                <input type="text" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}">
                                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label class="form-label" for="status">Status <span class="text-danger">*</span></label>
                                <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="active" @selected(old('status', 'active') === 'active')>Active</option>
                                    <option value="inactive" @selected(old('status') === 'inactive')>Inactive</option>
                                </select>
                                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label class="form-label" for="password">Password <span class="text-danger">*</span></label>
                                <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="new-password">
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label class="form-label" for="password_confirmation">Confirm Password <span class="text-danger">*</span></label>
                                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required autocomplete="new-password">
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label d-block">Roles</label>
                                @foreach($roles as $role)
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="roles[]" id="role_{{ $role->id }}" value="{{ $role->name }}" @checked(in_array($role->name, old('roles', ['Viewer'])) )>
                                    <label class="form-check-label" for="role_{{ $role->id }}">{{ $role->name }}</label>
                                </div>
                                @endforeach
                                @error('roles') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary d-inline-flex align-items-center">Create User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
