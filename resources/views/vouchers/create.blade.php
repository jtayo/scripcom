@extends('layouts.admin')

@section('title', 'Generate Vouchers')
@section('page-title', 'Generate Vouchers')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-xl-8">
            <div class="card">
                <div class="card-header">
                    <h2 class="h5 mb-0">Batch Settings</h2>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.vouchers.store') }}">
                        @csrf

                        <div class="row">
                            <div class="col-12 col-md-4 mb-3">
                                <label class="form-label" for="type">Type <span class="text-danger">*</span></label>
                                <select id="type" name="type" class="form-select @error('type') is-invalid @enderror" required>
                                    @foreach(['sessions', 'hours', 'bandwidth'] as $type)
                                        <option value="{{ $type }}" @selected(old('type', 'sessions') === $type)>{{ ucfirst($type) }}</option>
                                    @endforeach
                                </select>
                                @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-4 mb-3">
                                <label class="form-label" for="value">Value <span class="text-danger">*</span></label>
                                <input type="number" id="value" name="value" class="form-control @error('value') is-invalid @enderror" value="{{ old('value', 1) }}" min="1" required>
                                @error('value') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-4 mb-3">
                                <label class="form-label" for="count">Count <span class="text-danger">*</span></label>
                                <input type="number" id="count" name="count" class="form-control @error('count') is-invalid @enderror" value="{{ old('count', 50) }}" min="1" max="1000" required>
                                @error('count') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label class="form-label" for="sponsor_id">Sponsor</label>
                                <select id="sponsor_id" name="sponsor_id" class="form-select @error('sponsor_id') is-invalid @enderror">
                                    <option value="">None</option>
                                    @foreach($sponsors as $sponsor)
                                        <option value="{{ $sponsor->id }}" @selected(old('sponsor_id') == $sponsor->id)>{{ $sponsor->name }}</option>
                                    @endforeach
                                </select>
                                @error('sponsor_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label" for="expires_at">Expires At</label>
                                <input type="datetime-local" id="expires_at" name="expires_at" class="form-control @error('expires_at') is-invalid @enderror" value="{{ old('expires_at') }}">
                                @error('expires_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="alert alert-info mb-0 small">
                                This will generate <strong>{{ old('count', 50) }}</strong> unique voucher code(s).
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.vouchers.index') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary d-inline-flex align-items-center">Generate Vouchers</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
