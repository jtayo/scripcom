@extends('layouts.admin')

@section('title', 'New Organization')
@section('page-title', 'New Organization')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <div class="card">
                <div class="card-header">
                    <h2 class="h5 mb-0">Organization Details</h2>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.organizations.store') }}">
                        @csrf

                        <div class="row">
                            <div class="col-12 col-md-6 mb-3">
                                <label class="form-label" for="name">Name <span class="text-danger">*</span></label>
                                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label class="form-label" for="slug">Slug</label>
                                <input type="text" id="slug" name="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug') }}" placeholder="auto-generated if empty">
                                @error('slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label class="form-label" for="email">Email</label>
                                <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label class="form-label" for="phone">Phone</label>
                                <input type="text" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}">
                                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label" for="address">Address</label>
                                <input type="text" id="address" name="address" class="form-control @error('address') is-invalid @enderror" value="{{ old('address') }}">
                                @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-4 mb-3">
                                <label class="form-label" for="city">City</label>
                                <input type="text" id="city" name="city" class="form-control @error('city') is-invalid @enderror" value="{{ old('city') }}">
                                @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-4 mb-3">
                                <label class="form-label" for="county">County</label>
                                <input type="text" id="county" name="county" class="form-control @error('county') is-invalid @enderror" value="{{ old('county') }}">
                                @error('county') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-4 mb-3">
                                <label class="form-label" for="country">Country</label>
                                <input type="text" id="country" name="country" class="form-control @error('country') is-invalid @enderror" value="{{ old('country', 'Kenya') }}">
                                @error('country') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-4 mb-3">
                                <label class="form-label" for="postal_code">Postal Code</label>
                                <input type="text" id="postal_code" name="postal_code" class="form-control @error('postal_code') is-invalid @enderror" value="{{ old('postal_code') }}">
                                @error('postal_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-4 mb-3">
                                <label class="form-label" for="website">Website</label>
                                <input type="url" id="website" name="website" class="form-control @error('website') is-invalid @enderror" value="{{ old('website') }}">
                                @error('website') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-4 mb-3">
                                <label class="form-label" for="type">Type</label>
                                <select id="type" name="type" class="form-select @error('type') is-invalid @enderror">
                                    <option value="">Select type...</option>
                                    @foreach(['county', 'institution', 'municipality', 'ngo', 'corporate', 'other'] as $type)
                                        <option value="{{ $type }}" @selected(old('type') === $type)>{{ ucfirst($type) }}</option>
                                    @endforeach
                                </select>
                                @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-4 mb-3">
                                <label class="form-label" for="primary_color">Primary Color</label>
                                <input type="text" id="primary_color" name="primary_color" class="form-control @error('primary_color') is-invalid @enderror" value="{{ old('primary_color', '#DB1F2A') }}" placeholder="#DB1F2A">
                                @error('primary_color') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-4 mb-3">
                                <label class="form-label" for="secondary_color">Secondary Color</label>
                                <input type="text" id="secondary_color" name="secondary_color" class="form-control @error('secondary_color') is-invalid @enderror" value="{{ old('secondary_color', '#262B40') }}" placeholder="#262B40">
                                @error('secondary_color') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 mb-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" checked>
                                    <label class="form-check-label" for="is_active">Active</label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.organizations.index') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary d-inline-flex align-items-center">Create Organization</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
