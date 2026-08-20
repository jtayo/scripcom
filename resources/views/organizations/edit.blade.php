@extends('layouts.admin')

@section('title', 'Edit Organization')
@section('page-title', 'Edit Organization')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <span class="avatar avatar-sm bg-warning-lt text-warning me-2">
                            <i class="fa-solid fa-building"></i>
                        </span>
                        <div>
                            <h2 class="h5 mb-0">Edit Organization</h2>
                            <div class="small text-muted">Update {{ $organization->name }}'s details</div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.organizations.update', $organization) }}" enctype="multipart/form-data">
                        @csrf @method('PUT')

                        <div class="row g-3">
                            <div class="col-12">
                                <div class="text-uppercase small fw-bold text-secondary mb-1">Core Info</div>
                                <hr class="mt-1 mb-3">
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label" for="name">Name <span class="text-danger">*</span></label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-building"></i></span>
                                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $organization->name) }}" required>
                                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="slug">Slug</label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-link"></i></span>
                                    <input type="text" id="slug" name="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug', $organization->slug) }}">
                                    @error('slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="type">Type</label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-tags"></i></span>
                                    <select id="type" name="type" class="form-select @error('type') is-invalid @enderror" style="padding-left: 2.5rem;">
                                        <option value="">Select type...</option>
                                        @foreach(\App\Models\Organization::types() as $value => $label)
                                            <option value="{{ $value }}" @selected(old('type', $organization->type) === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="website">Website</label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-globe"></i></span>
                                    <input type="url" id="website" name="website" class="form-control @error('website') is-invalid @enderror" value="{{ old('website', $organization->website) }}" placeholder="https://...">
                                    @error('website') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="text-uppercase small fw-bold text-secondary mb-1">Contact</div>
                                <hr class="mt-1 mb-3">
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label" for="email">Email</label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-envelope"></i></span>
                                    <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $organization->email) }}">
                                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="phone">Phone</label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-phone"></i></span>
                                    <input type="text" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $organization->phone) }}">
                                    @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="address">Address</label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-location-dot"></i></span>
                                    <input type="text" id="address" name="address" class="form-control @error('address') is-invalid @enderror" value="{{ old('address', $organization->address) }}">
                                    @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label" for="city">City</label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-city"></i></span>
                                    <input type="text" id="city" name="city" class="form-control @error('city') is-invalid @enderror" value="{{ old('city', $organization->city) }}">
                                    @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label" for="county">County</label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-map"></i></span>
                                    <input type="text" id="county" name="county" class="form-control @error('county') is-invalid @enderror" value="{{ old('county', $organization->county) }}">
                                    @error('county') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label" for="country">Country</label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-earth-africa"></i></span>
                                    <input type="text" id="country" name="country" class="form-control @error('country') is-invalid @enderror" value="{{ old('country', $organization->country) }}">
                                    @error('country') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="postal_code">Postal Code</label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-hashtag"></i></span>
                                    <input type="text" id="postal_code" name="postal_code" class="form-control @error('postal_code') is-invalid @enderror" value="{{ old('postal_code', $organization->postal_code) }}">
                                    @error('postal_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="text-uppercase small fw-bold text-secondary mb-1">Branding</div>
                                <hr class="mt-1 mb-3">
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label" for="logo">Logo</label>
                                @if($organization->logo)
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/' . $organization->logo) }}" alt="Current logo" style="max-height: 48px; max-width: 160px; object-fit: contain;">
                                    </div>
                                @endif
                                <input type="file" id="logo" name="logo" class="form-control @error('logo') is-invalid @enderror" accept="image/*">
                                <div class="form-text">PNG, JPG or SVG. Max 2MB. Leave empty to keep current logo.</div>
                                @error('logo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label" for="primary_color">Primary Color</label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-palette"></i></span>
                                    <input type="text" id="primary_color" name="primary_color" class="form-control @error('primary_color') is-invalid @enderror" value="{{ old('primary_color', $organization->primary_color) }}">
                                    @error('primary_color') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="secondary_color">Secondary Color</label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-palette"></i></span>
                                    <input type="text" id="secondary_color" name="secondary_color" class="form-control @error('secondary_color') is-invalid @enderror" value="{{ old('secondary_color', $organization->secondary_color) }}">
                                    @error('secondary_color') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" @checked(old('is_active', $organization->is_active))>
                                    <label class="form-check-label" for="is_active">Active</label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                            <a href="{{ route('admin.organizations.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center">
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
