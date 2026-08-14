@extends('layouts.admin')

@section('title', 'New Campaign')
@section('page-title', 'New Campaign')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <div class="card">
                <div class="card-header">
                    <h2 class="h5 mb-0">Campaign Details</h2>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.campaigns.store') }}">
                        @csrf

                        @if($organizations)
                        <div class="row mb-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="organization_id">Organization</label>
                                <select id="organization_id" name="organization_id" class="form-select @error('organization_id') is-invalid @enderror">
                                    <option value="">Select organization...</option>
                                    @foreach($organizations as $organization)
                                        <option value="{{ $organization->id }}" @selected(old('organization_id') == $organization->id)>{{ $organization->name }}</option>
                                    @endforeach
                                </select>
                                @error('organization_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="sponsor_id">Sponsor</label>
                                <select id="sponsor_id" name="sponsor_id" class="form-select @error('sponsor_id') is-invalid @enderror">
                                    <option value="">No sponsor (public)</option>
                                    @foreach($sponsors as $sponsor)
                                        <option value="{{ $sponsor->id }}" @selected(old('sponsor_id') == $sponsor->id)>{{ $sponsor->name }}</option>
                                    @endforeach
                                </select>
                                @error('sponsor_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        @else
                        <div class="row mb-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="sponsor_id">Sponsor</label>
                                <select id="sponsor_id" name="sponsor_id" class="form-select @error('sponsor_id') is-invalid @enderror">
                                    <option value="">No sponsor (public)</option>
                                    @foreach($sponsors as $sponsor)
                                        <option value="{{ $sponsor->id }}" @selected(old('sponsor_id') == $sponsor->id)>{{ $sponsor->name }}</option>
                                    @endforeach
                                </select>
                                @error('sponsor_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        @endif

                        <div class="row">
                            <div class="col-12 col-md-6 mb-3">
                                <label class="form-label" for="title">Title <span class="text-danger">*</span></label>
                                <input type="text" id="title" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required>
                                @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label class="form-label" for="type">Type <span class="text-danger">*</span></label>
                                <select id="type" name="type" class="form-select @error('type') is-invalid @enderror" required>
                                    @foreach($types as $type)
                                        <option value="{{ $type->value }}" @selected(old('type') === $type->value)>{{ $type->label() }}</option>
                                    @endforeach
                                </select>
                                @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label" for="description">Description</label>
                                <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description') }}</textarea>
                                @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-4 mb-3">
                                <label class="form-label" for="content_type">Content Type <span class="text-danger">*</span></label>
                                <select id="content_type" name="content_type" class="form-select @error('content_type') is-invalid @enderror" required>
                                    @foreach(['image', 'video', 'html'] as $contentType)
                                        <option value="{{ $contentType }}" @selected(old('content_type', 'image') === $contentType)>{{ ucfirst($contentType) }}</option>
                                    @endforeach
                                </select>
                                @error('content_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-4 mb-3">
                                <label class="form-label" for="content_url">Content URL</label>
                                <input type="url" id="content_url" name="content_url" class="form-control @error('content_url') is-invalid @enderror" value="{{ old('content_url') }}">
                                @error('content_url') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-4 mb-3">
                                <label class="form-label" for="thumbnail">Thumbnail</label>
                                <input type="text" id="thumbnail" name="thumbnail" class="form-control @error('thumbnail') is-invalid @enderror" value="{{ old('thumbnail') }}">
                                @error('thumbnail') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label class="form-label" for="redirect_url">Redirect URL</label>
                                <input type="url" id="redirect_url" name="redirect_url" class="form-control @error('redirect_url') is-invalid @enderror" value="{{ old('redirect_url') }}" placeholder="https://...">
                                @error('redirect_url') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label class="form-label" for="duration_seconds">Duration (seconds) <span class="text-danger">*</span></label>
                                <input type="number" id="duration_seconds" name="duration_seconds" class="form-control @error('duration_seconds') is-invalid @enderror" value="{{ old('duration_seconds', 15) }}" min="1" max="600" required>
                                @error('duration_seconds') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-4 mb-3">
                                <label class="form-label" for="priority">Priority</label>
                                <input type="number" id="priority" name="priority" class="form-control @error('priority') is-invalid @enderror" value="{{ old('priority', 0) }}">
                                @error('priority') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-4 mb-3">
                                <label class="form-label" for="max_plays">Max Plays</label>
                                <input type="number" id="max_plays" name="max_plays" class="form-control @error('max_plays') is-invalid @enderror" value="{{ old('max_plays') }}" min="1" placeholder="Unlimited if empty">
                                @error('max_plays') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-4 mb-3">
                                <label class="form-label" for="status">Status <span class="text-danger">*</span></label>
                                <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    @foreach(['draft', 'active', 'paused', 'ended'] as $status)
                                        <option value="{{ $status }}" @selected(old('status', 'draft') === $status)>{{ ucfirst($status) }}</option>
                                    @endforeach
                                </select>
                                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label class="form-label" for="starts_at">Starts At</label>
                                <input type="datetime-local" id="starts_at" name="starts_at" class="form-control @error('starts_at') is-invalid @enderror" value="{{ old('starts_at') }}">
                                @error('starts_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label class="form-label" for="ends_at">Ends At</label>
                                <input type="datetime-local" id="ends_at" name="ends_at" class="form-control @error('ends_at') is-invalid @enderror" value="{{ old('ends_at') }}">
                                @error('ends_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12">
                                <label class="form-label d-block">Hotspots</label>
                                <select name="hotspot_ids[]" id="hotspot_ids" class="form-select @error('hotspot_ids') is-invalid @enderror" multiple size="6">
                                    @foreach($hotspots as $hotspot)
                                        <option value="{{ $hotspot->id }}" @selected(in_array($hotspot->id, old('hotspot_ids', [])))>{{ $hotspot->name }}</option>
                                    @endforeach
                                </select>
                                <div class="form-text">Hold Ctrl (Cmd on Mac) to select multiple hotspots. Leave empty to target all.</div>
                                @error('hotspot_ids') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12 col-md-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="skip_allowed" id="skip_allowed" value="1">
                                    <label class="form-check-label" for="skip_allowed">Skip Allowed</label>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_mandatory" id="is_mandatory" value="1" checked>
                                    <label class="form-check-label" for="is_mandatory">Mandatory</label>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" checked>
                                    <label class="form-check-label" for="is_active">Active</label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.campaigns.index') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary d-inline-flex align-items-center">Create Campaign</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
