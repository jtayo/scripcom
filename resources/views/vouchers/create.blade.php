@extends('layouts.admin')

@section('title', 'Generate Vouchers')
@section('page-title', 'Generate Vouchers')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-xl-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <span class="avatar avatar-sm bg-primary-lt text-primary me-2">
                            <i class="fa-solid fa-ticket"></i>
                        </span>
                        <div>
                            <h2 class="h5 mb-0">Create Voucher</h2>
                            <div class="small text-muted">Generate a batch of unique redemption vouchers</div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.vouchers.store') }}">
                        @csrf

                        <div class="row g-3">
                            <div class="col-12 col-md-4">
                                <label class="form-label" for="type">Type <span class="text-danger">*</span></label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-tags"></i></span>
                                    <select id="type" name="type" class="form-select @error('type') is-invalid @enderror" style="padding-left: 2.5rem;" required>
                                        @foreach(['sessions', 'hours', 'bandwidth'] as $type)
                                            <option value="{{ $type }}" @selected(old('type', 'sessions') === $type)>{{ ucfirst($type) }}</option>
                                        @endforeach
                                    </select>
                                    @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label" for="value">Value <span class="text-danger">*</span></label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-money-bill"></i></span>
                                    <input type="number" id="value" name="value" class="form-control @error('value') is-invalid @enderror" value="{{ old('value', 1) }}" min="1" required>
                                    @error('value') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label" for="count">Count <span class="text-danger">*</span></label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-layer-group"></i></span>
                                    <input type="number" id="count" name="count" class="form-control @error('count') is-invalid @enderror" value="{{ old('count', 50) }}" min="1" max="1000" required>
                                    @error('count') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label" for="sponsor_id">Sponsor</label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-handshake"></i></span>
                                    <select id="sponsor_id" name="sponsor_id" class="form-select @error('sponsor_id') is-invalid @enderror" style="padding-left: 2.5rem;">
                                        <option value="">None</option>
                                        @foreach($sponsors as $sponsor)
                                            <option value="{{ $sponsor->id }}" @selected(old('sponsor_id') == $sponsor->id)>{{ $sponsor->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('sponsor_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="expires_at">Expires At</label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-calendar"></i></span>
                                    <input type="datetime-local" id="expires_at" name="expires_at" class="form-control @error('expires_at') is-invalid @enderror" value="{{ old('expires_at') }}">
                                    @error('expires_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <div class="alert alert-info mb-0 small">
                                <i class="fa-solid fa-circle-info me-1"></i>This will generate <strong>{{ old('count', 50) }}</strong> unique voucher code(s).
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                            <a href="{{ route('admin.vouchers.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center">
                                <i class="fa-solid fa-arrow-left me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary d-inline-flex align-items-center">
                                <i class="fa-solid fa-ticket me-2"></i>Create Voucher
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
