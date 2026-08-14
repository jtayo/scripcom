@extends('layouts.admin')

@section('title', 'New Sponsorship')
@section('page-title', 'New Sponsorship')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-xl-8">
            <div class="card">
                <div class="card-header">
                    <h2 class="h5 mb-0">Sponsorship Details</h2>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.sponsorships.store') }}">
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
                                <label class="form-label" for="sponsor_id">Sponsor <span class="text-danger">*</span></label>
                                <select id="sponsor_id" name="sponsor_id" class="form-select @error('sponsor_id') is-invalid @enderror" required>
                                    <option value="">Select sponsor...</option>
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
                                <label class="form-label" for="sponsor_id">Sponsor <span class="text-danger">*</span></label>
                                <select id="sponsor_id" name="sponsor_id" class="form-select @error('sponsor_id') is-invalid @enderror" required>
                                    <option value="">Select sponsor...</option>
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
                                <label class="form-label" for="type">Type <span class="text-danger">*</span></label>
                                <select id="type" name="type" class="form-select @error('type') is-invalid @enderror" required>
                                    @foreach(['sessions', 'hours', 'campaign', 'bandwidth'] as $type)
                                        <option value="{{ $type }}" @selected(old('type', 'sessions') === $type)>{{ ucfirst($type) }}</option>
                                    @endforeach
                                </select>
                                @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label class="form-label" for="status">Status <span class="text-danger">*</span></label>
                                <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    @foreach(['pending', 'active', 'expired', 'cancelled'] as $status)
                                        <option value="{{ $status }}" @selected(old('status', 'pending') === $status)>{{ ucfirst($status) }}</option>
                                    @endforeach
                                </select>
                                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-4 mb-3">
                                <label class="form-label" for="quantity_purchased">Quantity <span class="text-danger">*</span></label>
                                <input type="number" id="quantity_purchased" name="quantity_purchased" class="form-control @error('quantity_purchased') is-invalid @enderror" value="{{ old('quantity_purchased', 100) }}" min="1" required>
                                @error('quantity_purchased') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-4 mb-3">
                                <label class="form-label" for="unit_price">Unit Price <span class="text-danger">*</span></label>
                                <input type="number" id="unit_price" name="unit_price" class="form-control @error('unit_price') is-invalid @enderror" value="{{ old('unit_price') }}" min="0" step="0.01" required>
                                @error('unit_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-4 mb-3">
                                <label class="form-label" for="currency">Currency <span class="text-danger">*</span></label>
                                <input type="text" id="currency" name="currency" class="form-control @error('currency') is-invalid @enderror" value="{{ old('currency', 'KES') }}" maxlength="10" required>
                                @error('currency') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label class="form-label" for="starts_at">Starts At</label>
                                <input type="datetime-local" id="starts_at" name="starts_at" class="form-control @error('starts_at') is-invalid @enderror" value="{{ old('starts_at') }}">
                                @error('starts_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label class="form-label" for="expires_at">Expires At</label>
                                <input type="datetime-local" id="expires_at" name="expires_at" class="form-control @error('expires_at') is-invalid @enderror" value="{{ old('expires_at') }}">
                                @error('expires_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label" for="notes">Notes</label>
                                <textarea id="notes" name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes') }}</textarea>
                                @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="alert alert-info mb-0 small">
                                Total amount will be computed as <strong>quantity &times; unit price</strong>.
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.sponsorships.index') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary d-inline-flex align-items-center">Create Sponsorship</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
