@extends('layouts.admin')

@section('title', 'Edit Sponsorship')
@section('page-title', 'Edit Sponsorship')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-xl-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <span class="avatar avatar-sm bg-warning-lt text-warning me-2">
                            <i class="fa-solid fa-handshake"></i>
                        </span>
                        <div>
                            <h2 class="h5 mb-0">Edit Sponsorship</h2>
                            <div class="small text-muted">Update {{ $sponsorship->reference }} and its terms</div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.sponsorships.update', $sponsorship) }}">
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
                                            <option value="{{ $organization->id }}" @selected(old('organization_id', $sponsorship->organization_id) == $organization->id)>{{ $organization->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('organization_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="sponsor_id">Sponsor <span class="text-danger">*</span></label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-handshake"></i></span>
                                    <select id="sponsor_id" name="sponsor_id" class="form-select @error('sponsor_id') is-invalid @enderror" style="padding-left: 2.5rem;" required>
                                        <option value="">Select sponsor...</option>
                                        @foreach($sponsors as $sponsor)
                                            <option value="{{ $sponsor->id }}" @selected(old('sponsor_id', $sponsorship->sponsor_id) == $sponsor->id)>{{ $sponsor->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('sponsor_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            @else
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="sponsor_id">Sponsor <span class="text-danger">*</span></label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-handshake"></i></span>
                                    <select id="sponsor_id" name="sponsor_id" class="form-select @error('sponsor_id') is-invalid @enderror" style="padding-left: 2.5rem;" required>
                                        <option value="">Select sponsor...</option>
                                        @foreach($sponsors as $sponsor)
                                            <option value="{{ $sponsor->id }}" @selected(old('sponsor_id', $sponsorship->sponsor_id) == $sponsor->id)>{{ $sponsor->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('sponsor_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            @endif

                            <div class="col-12 col-md-6">
                                <label class="form-label" for="type">Type <span class="text-danger">*</span></label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-tags"></i></span>
                                    <select id="type" name="type" class="form-select @error('type') is-invalid @enderror" style="padding-left: 2.5rem;" required>
                                        @foreach(['sessions', 'hours', 'campaign', 'bandwidth'] as $type)
                                            <option value="{{ $type }}" @selected(old('type', $sponsorship->type) === $type)>{{ ucfirst($type) }}</option>
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
                                        @foreach(['pending', 'active', 'expired', 'cancelled'] as $status)
                                            <option value="{{ $status }}" @selected(old('status', $sponsorship->status) === $status)>{{ ucfirst($status) }}</option>
                                        @endforeach
                                    </select>
                                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label" for="quantity_purchased">Quantity <span class="text-danger">*</span></label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-hashtag"></i></span>
                                    <input type="number" id="quantity_purchased" name="quantity_purchased" class="form-control @error('quantity_purchased') is-invalid @enderror" value="{{ old('quantity_purchased', $sponsorship->quantity_purchased) }}" min="1" required>
                                    @error('quantity_purchased') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label" for="unit_price">Unit Price <span class="text-danger">*</span></label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-money-bill"></i></span>
                                    <input type="number" id="unit_price" name="unit_price" class="form-control @error('unit_price') is-invalid @enderror" value="{{ old('unit_price', $sponsorship->unit_price) }}" min="0" step="0.01" required>
                                    @error('unit_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label" for="currency">Currency <span class="text-danger">*</span></label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-coins"></i></span>
                                    <input type="text" id="currency" name="currency" class="form-control @error('currency') is-invalid @enderror" value="{{ old('currency', $sponsorship->currency) }}" maxlength="10" required>
                                    @error('currency') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="starts_at">Starts At</label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-calendar"></i></span>
                                    <input type="datetime-local" id="starts_at" name="starts_at" class="form-control @error('starts_at') is-invalid @enderror" value="{{ old('starts_at', $sponsorship->starts_at?->format('Y-m-d\TH:i')) }}">
                                    @error('starts_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="expires_at">Expires At</label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-calendar-check"></i></span>
                                    <input type="datetime-local" id="expires_at" name="expires_at" class="form-control @error('expires_at') is-invalid @enderror" value="{{ old('expires_at', $sponsorship->expires_at?->format('Y-m-d\TH:i')) }}">
                                    @error('expires_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="notes">Notes</label>
                                <div class="input-icon input-icon-wrapper">
                                    <span class="input-icon-addon"><i class="fa-solid fa-align-left"></i></span>
                                    <textarea id="notes" name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3" style="padding-left: 2.5rem;">{{ old('notes', $sponsorship->notes) }}</textarea>
                                    @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <div class="alert alert-info mb-0 small">
                                <i class="fa-solid fa-circle-info me-1"></i>Current total: <strong>{{ $sponsorship->currency }} {{ number_format($sponsorship->total_amount, 2) }}</strong>. Total amount will be recomputed as <strong>quantity &times; unit price</strong>.
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                            <a href="{{ route('admin.sponsorships.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center">
                                <i class="fa-solid fa-arrow-left me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary d-inline-flex align-items-center">
                                <i class="fa-solid fa-floppy-disk me-2"></i>Update Sponsorship
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
