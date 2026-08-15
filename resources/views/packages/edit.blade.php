@extends('layouts.admin')

@section('title', "Edit {$package->name}")
@section('page-title', "Edit {$package->name}")

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-xl-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <span class="avatar avatar-sm bg-primary-lt text-primary me-2">
                            <i class="fa-solid fa-wifi"></i>
                        </span>
                        <div>
                            <h2 class="h5 mb-0">Edit Package</h2>
                            <div class="small text-muted">{{ $package->code }}</div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.packages.update', $package) }}">
                        @csrf @method('PUT')

                        <div class="row g-3">
                            @if($organizations)
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="organization_id">Organization</label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-building"></i></span>
                                    <select id="organization_id" name="organization_id" class="form-select @error('organization_id') is-invalid @enderror" style="padding-left: 2.5rem;">
                                        <option value="">Select organization...</option>
                                        @foreach($organizations as $organization)
                                            <option value="{{ $organization->id }}" @selected(old('organization_id', $package->organization_id) == $organization->id)>{{ $organization->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('organization_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            @endif

                            <div class="col-12 col-md-6">
                                <label class="form-label" for="name">Name <span class="text-danger">*</span></label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-tag"></i></span>
                                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $package->name) }}" required>
                                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label" for="code">Code <span class="text-danger">*</span></label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-barcode"></i></span>
                                    <input type="text" id="code" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code', $package->code) }}" required>
                                    @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label" for="access_type">Access Type <span class="text-danger">*</span></label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-shield-halved"></i></span>
                                    <select id="access_type" name="access_type" class="form-select @error('access_type') is-invalid @enderror" style="padding-left: 2.5rem;" required>
                                        @foreach(['free', 'paid', 'sponsored'] as $type)
                                            <option value="{{ $type }}" @selected(old('access_type', $package->access_type) === $type)>{{ ucfirst($type) }}</option>
                                        @endforeach
                                    </select>
                                    @error('access_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label" for="duration_minutes">Duration (minutes) <span class="text-danger">*</span></label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-clock"></i></span>
                                    <input type="number" id="duration_minutes" name="duration_minutes" class="form-control @error('duration_minutes') is-invalid @enderror" value="{{ old('duration_minutes', $package->duration_minutes) }}" min="1" required>
                                    @error('duration_minutes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label" for="price">Price (KES) <span class="text-danger">*</span></label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-money-bill"></i></span>
                                    <input type="number" id="price" name="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price', $package->price) }}" min="0" step="0.01" required>
                                    @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label" for="bandwidth_down_kbps">Downstream (kbps)</label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-arrow-down"></i></span>
                                    <input type="number" id="bandwidth_down_kbps" name="bandwidth_down_kbps" class="form-control @error('bandwidth_down_kbps') is-invalid @enderror" value="{{ old('bandwidth_down_kbps', $package->bandwidth_down_kbps) }}" min="0">
                                    @error('bandwidth_down_kbps') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label" for="bandwidth_up_kbps">Upstream (kbps)</label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-arrow-up"></i></span>
                                    <input type="number" id="bandwidth_up_kbps" name="bandwidth_up_kbps" class="form-control @error('bandwidth_up_kbps') is-invalid @enderror" value="{{ old('bandwidth_up_kbps', $package->bandwidth_up_kbps) }}" min="0">
                                    @error('bandwidth_up_kbps') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label" for="data_limit_mb">Data Limit (MB)</label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-hard-drive"></i></span>
                                    <input type="number" id="data_limit_mb" name="data_limit_mb" class="form-control @error('data_limit_mb') is-invalid @enderror" value="{{ old('data_limit_mb', $package->data_limit_mb) }}" min="0" placeholder="Leave empty for unlimited">
                                    @error('data_limit_mb') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label" for="simultaneous_devices">Simultaneous Devices</label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-tablet-screen-button"></i></span>
                                    <input type="number" id="simultaneous_devices" name="simultaneous_devices" class="form-control @error('simultaneous_devices') is-invalid @enderror" value="{{ old('simultaneous_devices', $package->simultaneous_devices) }}" min="1">
                                    @error('simultaneous_devices') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label" for="is_active">Status</label>
                                <div class="form-selectgroup">
                                    <label class="form-selectgroup-item">
                                        <input type="radio" name="is_active" value="1" class="form-selectgroup-input" @checked(old('is_active', $package->is_active) == 1)>
                                        <span class="form-selectgroup-label"><i class="fa-solid fa-circle-check text-success me-1"></i>Active</span>
                                    </label>
                                    <label class="form-selectgroup-item">
                                        <input type="radio" name="is_active" value="0" class="form-selectgroup-input" @checked(old('is_active', $package->is_active) == 0)>
                                        <span class="form-selectgroup-label"><i class="fa-solid fa-circle-xmark text-secondary me-1"></i>Inactive</span>
                                    </label>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label" for="description">Description</label>
                                <div class="input-icon input-icon-wrapper">
                                    <span class="input-icon-addon"><i class="fa-solid fa-align-left"></i></span>
                                    <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror" rows="3" style="padding-left: 2.5rem;">{{ old('description', $package->description) }}</textarea>
                                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                            <a href="{{ route('admin.packages.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center">
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
