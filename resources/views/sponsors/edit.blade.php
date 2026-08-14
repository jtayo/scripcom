@extends('layouts.admin')

@section('title', 'Edit Sponsor')
@section('page-title', 'Edit Sponsor')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-xl-8">
            <div class="card">
                <div class="card-header">
                    <h2 class="h5 mb-0">Sponsor Details</h2>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.sponsors.update', $sponsor) }}">
                        @csrf @method('PUT')

                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label" for="name">Name <span class="text-danger">*</span></label>
                                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $sponsor->name) }}" required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label class="form-label" for="email">Email</label>
                                <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $sponsor->email) }}">
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label class="form-label" for="phone">Phone</label>
                                <input type="text" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $sponsor->phone) }}">
                                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label" for="address">Address</label>
                                <input type="text" id="address" name="address" class="form-control @error('address') is-invalid @enderror" value="{{ old('address', $sponsor->address) }}">
                                @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label class="form-label" for="website">Website</label>
                                <input type="url" id="website" name="website" class="form-control @error('website') is-invalid @enderror" value="{{ old('website', $sponsor->website) }}" placeholder="https://...">
                                @error('website') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label class="form-label" for="contact_person">Contact Person</label>
                                <input type="text" id="contact_person" name="contact_person" class="form-control @error('contact_person') is-invalid @enderror" value="{{ old('contact_person', $sponsor->contact_person) }}">
                                @error('contact_person') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" @checked(old('is_active', $sponsor->is_active))>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.sponsors.index') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary d-inline-flex align-items-center">Update Sponsor</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
