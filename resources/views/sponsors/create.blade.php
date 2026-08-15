@extends('layouts.admin')

@section('title', 'New Sponsor')
@section('page-title', 'New Sponsor')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <span class="avatar avatar-sm bg-primary-lt text-primary me-2">
                            <i class="fa-solid fa-building"></i>
                        </span>
                        <div>
                            <h2 class="h5 mb-0">New Sponsor</h2>
                            <div class="small text-muted">Register a new sponsor on the platform</div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.sponsors.store') }}">
                        @csrf

                        <div class="row g-3">
                            <div class="col-12">
                                <div class="text-uppercase small fw-bold text-secondary mb-1">Core Info</div>
                                <hr class="mt-1 mb-3">
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label" for="name">Name <span class="text-danger">*</span></label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-building"></i></span>
                                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="e.g. County Government" required>
                                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="contact_person">Contact Person</label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-user-tie"></i></span>
                                    <input type="text" id="contact_person" name="contact_person" class="form-control @error('contact_person') is-invalid @enderror" value="{{ old('contact_person') }}" placeholder="Full name">
                                    @error('contact_person') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
                                    <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="info@example.com">
                                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="phone">Phone</label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-phone"></i></span>
                                    <input type="text" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="+254 700 000 000">
                                    @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="address">Address</label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-map-pin"></i></span>
                                    <input type="text" id="address" name="address" class="form-control @error('address') is-invalid @enderror" value="{{ old('address') }}" placeholder="Street, building, floor">
                                    @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="website">Website</label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-globe"></i></span>
                                    <input type="url" id="website" name="website" class="form-control @error('website') is-invalid @enderror" value="{{ old('website') }}" placeholder="https://...">
                                    @error('website') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="text-uppercase small fw-bold text-secondary mb-1">Status</div>
                                <hr class="mt-1 mb-3">
                            </div>

                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" checked>
                                    <label class="form-check-label" for="is_active">Active</label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                            <a href="{{ route('admin.sponsors.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center">
                                <i class="fa-solid fa-arrow-left me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary d-inline-flex align-items-center">
                                <i class="fa-solid fa-floppy-disk me-2"></i>Create Sponsor
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
