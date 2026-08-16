@extends('layouts.admin')

@section('title', 'New Contract')
@section('page-title', 'New Contract')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <span class="avatar avatar-sm bg-primary-lt text-primary me-2">
                            <i class="fa-solid fa-file-contract"></i>
                        </span>
                        <div>
                            <h2 class="h5 mb-0">Create Contract</h2>
                            <div class="small text-muted">Define a sponsored session contract and its campaigns</div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.contracts.store') }}">
                        @csrf

                        <div class="row g-3">
                            <div class="col-12">
                                <div class="text-uppercase small fw-bold text-secondary mb-1">Contract Details</div>
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
                                                <option value="{{ $organization->id }}" @selected(old('organization_id') == $organization->id)>{{ $organization->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('organization_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            @endif
                            <div class="col-12 col-md-6 @if(!$organizations) offset-md-3 @endif">
                                <label class="form-label" for="sponsor_id">Sponsor</label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-heart-handshake"></i></span>
                                    <select id="sponsor_id" name="sponsor_id" class="form-select @error('sponsor_id') is-invalid @enderror" style="padding-left: 2.5rem;">
                                        <option value="">No sponsor (county-funded)...</option>
                                        @foreach($sponsors as $sponsor)
                                            <option value="{{ $sponsor->id }}" @selected(old('sponsor_id') == $sponsor->id)>{{ $sponsor->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('sponsor_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="title">Title <span class="text-danger">*</span></label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-tag"></i></span>
                                    <input type="text" id="title" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required>
                                    @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="type">Type <span class="text-danger">*</span></label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-layer-group"></i></span>
                                    <select id="type" name="type" class="form-select @error('type') is-invalid @enderror" style="padding-left: 2.5rem;" required>
                                        @foreach(['county', 'corporate', 'advertising'] as $type)
                                            <option value="{{ $type }}" @selected(old('type') === $type)>{{ ucfirst($type) }}</option>
                                        @endforeach
                                    </select>
                                    @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="status">Status <span class="text-danger">*</span></label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-circle-check"></i></span>
                                    <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" style="padding-left: 2.5rem;" required>
                                        @foreach(['draft', 'active', 'completed', 'cancelled'] as $status)
                                            <option value="{{ $status }}" @selected(old('status', 'draft') === $status)>{{ ucfirst($status) }}</option>
                                        @endforeach
                                    </select>
                                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="start_date">Start Date <span class="text-danger">*</span></label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-calendar-day"></i></span>
                                    <input type="date" id="start_date" name="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date') }}" required>
                                    @error('start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="end_date">End Date <span class="text-danger">*</span></label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-calendar-xmark"></i></span>
                                    <input type="date" id="end_date" name="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date') }}" required>
                                    @error('end_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="text-uppercase small fw-bold text-secondary mb-1">Pricing</div>
                                <hr class="mt-1 mb-3">
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label" for="sessions_allocated">Sessions Allocated (monthly) <span class="text-danger">*</span></label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-users"></i></span>
                                    <input type="number" id="sessions_allocated" name="sessions_allocated" class="form-control @error('sessions_allocated') is-invalid @enderror" value="{{ old('sessions_allocated', 0) }}" min="0" required>
                                    @error('sessions_allocated') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label" for="unit_price">Unit Price (KSh) <span class="text-danger">*</span></label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-coins"></i></span>
                                    <input type="number" id="unit_price" name="unit_price" class="form-control @error('unit_price') is-invalid @enderror" value="{{ old('unit_price', 0) }}" min="0" step="0.01" required>
                                    @error('unit_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label" for="tax_rate">Tax Rate (%)</label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-percent"></i></span>
                                    <input type="number" id="tax_rate" name="tax_rate" class="form-control @error('tax_rate') is-invalid @enderror" value="{{ old('tax_rate', 16) }}" min="0" max="100" step="0.01">
                                    @error('tax_rate') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label d-block">Campaigns</label>
                                <select name="campaign_ids[]" id="campaign_ids" class="form-select @error('campaign_ids') is-invalid @enderror" multiple size="5">
                                    @foreach($campaigns as $campaign)
                                        <option value="{{ $campaign->id }}" @selected(in_array($campaign->id, old('campaign_ids', $selectedCampaignIds)))>{{ $campaign->title }} ({{ $campaign->status }})</option>
                                    @endforeach
                                </select>
                                <div class="form-text"><i class="fa-solid fa-circle-info me-1"></i>Hold Ctrl (Cmd on Mac) to select multiple campaigns.</div>
                                @error('campaign_ids') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label" for="notes">Notes</label>
                                <textarea id="notes" name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes') }}</textarea>
                                @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                            <a href="{{ route('admin.contracts.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center">
                                <i class="fa-solid fa-arrow-left me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary d-inline-flex align-items-center">
                                <i class="fa-solid fa-floppy-disk me-2"></i>Create Contract
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
