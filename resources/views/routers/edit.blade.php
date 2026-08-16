@extends('layouts.admin')

@section('title', 'Edit Router')
@section('page-title', 'Edit Router')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <span class="avatar avatar-sm bg-warning-lt text-warning me-2">
                            <i class="fa-solid fa-device-router"></i>
                        </span>
                        <div>
                            <h2 class="h5 mb-0">Edit Router</h2>
                            <div class="small text-muted">Update {{ $router->name }} and its network settings</div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.routers.update', $router) }}">
                        @csrf @method('PUT')

                        <div class="row g-3">
                            <div class="col-12">
                                <div class="text-uppercase small fw-bold text-secondary mb-1">Router Details</div>
                                <hr class="mt-1 mb-3">
                            </div>

                            @if($organizations)
                                <div class="col-12 col-md-6">
                                    <label class="form-label" for="organization_id">Organization</label>
                                    <div class="input-icon">
                                        <span class="input-icon-addon"><i class="fa-solid fa-building"></i></span>
                                        <select id="organization_id" name="organization_id" class="form-select @error('organization_id') is-invalid @enderror" style="padding-left: 2.5rem;">
                                            <option value="">Select organization...</option>
                                            @foreach($organizations as $organization)
                                                <option value="{{ $organization->id }}" @selected(old('organization_id', $router->organization_id) == $organization->id)>{{ $organization->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('organization_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            @endif
                            <div class="col-12 col-md-6 @if(!$organizations) offset-md-3 @endif">
                                <label class="form-label" for="hotspot_id">Hotspot</label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-wifi"></i></span>
                                    <select id="hotspot_id" name="hotspot_id" class="form-select @error('hotspot_id') is-invalid @enderror" style="padding-left: 2.5rem;">
                                        <option value="">No linked hotspot...</option>
                                        @foreach($hotspots as $hotspot)
                                            <option value="{{ $hotspot->id }}" @selected(old('hotspot_id', $router->hotspot_id) == $hotspot->id)>
                                                {{ $hotspot->name }}{{ $hotspot->organization_id ? ' — ' . optional($hotspot->organization)->name : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('hotspot_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="name">Name <span class="text-danger">*</span></label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-tag"></i></span>
                                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $router->name) }}" required>
                                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="model">Model</label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-server"></i></span>
                                    <input type="text" id="model" name="model" class="form-control @error('model') is-invalid @enderror" value="{{ old('model', $router->model) }}">
                                    @error('model') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="firmware_version">Firmware Version</label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-microchip"></i></span>
                                    <input type="text" id="firmware_version" name="firmware_version" class="form-control @error('firmware_version') is-invalid @enderror" value="{{ old('firmware_version', $router->firmware_version) }}">
                                    @error('firmware_version') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="text-uppercase small fw-bold text-secondary mb-1">Connection</div>
                                <hr class="mt-1 mb-3">
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label" for="ip_address">IP Address</label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-ethernet"></i></span>
                                    <input type="text" id="ip_address" name="ip_address" class="form-control @error('ip_address') is-invalid @enderror" value="{{ old('ip_address', $router->ip_address) }}">
                                    @error('ip_address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label" for="port">Port</label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-plug"></i></span>
                                    <input type="number" id="port" name="port" class="form-control @error('port') is-invalid @enderror" value="{{ old('port', $router->port ?? 8728) }}" min="1" max="65535">
                                    @error('port') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label" for="username">API Username</label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-user"></i></span>
                                    <input type="text" id="username" name="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username', $router->username) }}">
                                    @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="password">API Password</label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-key"></i></span>
                                    <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror">
                                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="form-text">Stored encrypted. Leave blank to keep unchanged.</div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="status">Status</label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-circle-check"></i></span>
                                    <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" style="padding-left: 2.5rem;">
                                        @foreach(['online', 'degraded', 'offline', 'maintenance'] as $status)
                                            <option value="{{ $status }}" @selected(old('status', $router->status) === $status)>{{ ucfirst($status) }}</option>
                                        @endforeach
                                    </select>
                                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" @checked(old('is_active', $router->is_active))>
                                    <label class="form-check-label" for="is_active">Active</label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                            <a href="{{ route('admin.routers.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center">
                                <i class="fa-solid fa-arrow-left me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary d-inline-flex align-items-center">
                                <i class="fa-solid fa-floppy-disk me-2"></i>Update Router
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
