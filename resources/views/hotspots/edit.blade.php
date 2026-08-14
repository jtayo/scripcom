@extends('layouts.admin')

@section('title', 'Edit Hotspot')
@section('page-title', 'Edit Hotspot')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <div class="card">
                <div class="card-header">
                    <h2 class="h5 mb-0">Hotspot Details</h2>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.hotspots.update', $hotspot) }}">
                        @csrf @method('PUT')

                        @if($organizations)
                        <div class="row mb-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="organization_id">Organization</label>
                                <select id="organization_id" name="organization_id" class="form-select @error('organization_id') is-invalid @enderror">
                                    <option value="">Select organization...</option>
                                    @foreach($organizations as $organization)
                                        <option value="{{ $organization->id }}" @selected(old('organization_id', $hotspot->organization_id) == $organization->id)>{{ $organization->name }}</option>
                                    @endforeach
                                </select>
                                @error('organization_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="router_id">Router ID</label>
                                <input type="number" id="router_id" name="router_id" class="form-control @error('router_id') is-invalid @enderror" value="{{ old('router_id', $hotspot->router_id) }}" placeholder="e.g. 1">
                                @error('router_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        @else
                        <div class="row mb-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="router_id">Router ID</label>
                                <input type="number" id="router_id" name="router_id" class="form-control @error('router_id') is-invalid @enderror" value="{{ old('router_id', $hotspot->router_id) }}" placeholder="e.g. 1">
                                @error('router_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        @endif

                        <div class="row">
                            <div class="col-12 col-md-6 mb-3">
                                <label class="form-label" for="name">Name <span class="text-danger">*</span></label>
                                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $hotspot->name) }}" required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label class="form-label" for="ssid">SSID</label>
                                <input type="text" id="ssid" name="ssid" class="form-control @error('ssid') is-invalid @enderror" value="{{ old('ssid', $hotspot->ssid) }}">
                                @error('ssid') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label class="form-label" for="device_model">Device Model</label>
                                <input type="text" id="device_model" name="device_model" class="form-control @error('device_model') is-invalid @enderror" value="{{ old('device_model', $hotspot->device_model) }}">
                                @error('device_model') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label class="form-label" for="firmware_version">Firmware Version</label>
                                <input type="text" id="firmware_version" name="firmware_version" class="form-control @error('firmware_version') is-invalid @enderror" value="{{ old('firmware_version', $hotspot->firmware_version) }}">
                                @error('firmware_version') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label class="form-label" for="ip_address">IP Address</label>
                                <input type="text" id="ip_address" name="ip_address" class="form-control @error('ip_address') is-invalid @enderror" value="{{ old('ip_address', $hotspot->ip_address) }}">
                                @error('ip_address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label class="form-label" for="mac_address">MAC Address</label>
                                <input type="text" id="mac_address" name="mac_address" class="form-control @error('mac_address') is-invalid @enderror" value="{{ old('mac_address', $hotspot->mac_address) }}">
                                @error('mac_address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-4 mb-3">
                                <label class="form-label" for="isp">ISP</label>
                                <input type="text" id="isp" name="isp" class="form-control @error('isp') is-invalid @enderror" value="{{ old('isp', $hotspot->isp) }}">
                                @error('isp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-4 mb-3">
                                <label class="form-label" for="bandwidth_up">Bandwidth Up (Mbps)</label>
                                <input type="number" id="bandwidth_up" name="bandwidth_up" class="form-control @error('bandwidth_up') is-invalid @enderror" value="{{ old('bandwidth_up', $hotspot->bandwidth_up) }}" min="0">
                                @error('bandwidth_up') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-4 mb-3">
                                <label class="form-label" for="bandwidth_down">Bandwidth Down (Mbps)</label>
                                <input type="number" id="bandwidth_down" name="bandwidth_down" class="form-control @error('bandwidth_down') is-invalid @enderror" value="{{ old('bandwidth_down', $hotspot->bandwidth_down) }}" min="0">
                                @error('bandwidth_down') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label class="form-label" for="latitude">Latitude</label>
                                <input type="text" id="latitude" name="latitude" class="form-control @error('latitude') is-invalid @enderror" value="{{ old('latitude', $hotspot->latitude) }}" step="any">
                                @error('latitude') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label class="form-label" for="longitude">Longitude</label>
                                <input type="text" id="longitude" name="longitude" class="form-control @error('longitude') is-invalid @enderror" value="{{ old('longitude', $hotspot->longitude) }}" step="any">
                                @error('longitude') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label class="form-label" for="ward">Ward</label>
                                <input type="text" id="ward" name="ward" class="form-control @error('ward') is-invalid @enderror" value="{{ old('ward', $hotspot->ward) }}">
                                @error('ward') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label class="form-label" for="sub_county">Sub-County</label>
                                <input type="text" id="sub_county" name="sub_county" class="form-control @error('sub_county') is-invalid @enderror" value="{{ old('sub_county', $hotspot->sub_county) }}">
                                @error('sub_county') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label class="form-label" for="status">Status <span class="text-danger">*</span></label>
                                <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    @foreach(['online', 'offline', 'degraded', 'maintenance'] as $status)
                                        <option value="{{ $status }}" @selected(old('status', $hotspot->status) === $status)>{{ ucfirst($status) }}</option>
                                    @endforeach
                                </select>
                                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label class="form-label" for="max_clients">Max Clients</label>
                                <input type="number" id="max_clients" name="max_clients" class="form-control @error('max_clients') is-invalid @enderror" value="{{ old('max_clients', $hotspot->max_clients) }}" min="1">
                                @error('max_clients') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12">
                                <label class="form-label d-block">Campaigns</label>
                                <select name="campaign_ids[]" id="campaign_ids" class="form-select @error('campaign_ids') is-invalid @enderror" multiple size="5">
                                    @foreach($campaigns as $campaign)
                                        <option value="{{ $campaign->id }}" @selected(in_array($campaign->id, old('campaign_ids', $hotspot->campaigns->pluck('id')->all())))>{{ $campaign->title }} ({{ $campaign->type }})</option>
                                    @endforeach
                                </select>
                                <div class="form-text">Hold Ctrl (Cmd on Mac) to select multiple campaigns.</div>
                                @error('campaign_ids') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" @checked(old('is_active', $hotspot->is_active))>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.hotspots.index') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary d-inline-flex align-items-center">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
