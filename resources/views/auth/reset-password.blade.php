@extends('layouts.auth')

@section('title', 'Reset Password')

@section('auth-title', 'Set a new password')

@section('auth-content')
    <form method="POST" action="{{ route('password.store') }}" class="mt-4">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="form-group mb-4">
            <label for="email">Your Email</label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="ti ti-mail text-secondary"></i>
                </span>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="example@company.com" id="email" value="{{ old('email', $request->email) }}" autofocus required>
            </div>
        </div>

        <div class="form-group mb-4">
            <label for="password">New Password</label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="ti ti-lock text-secondary"></i>
                </span>
                <input type="password" name="password" placeholder="New password" class="form-control @error('password') is-invalid @enderror" id="password" required>
            </div>
        </div>

        <div class="form-group mb-4">
            <label for="password_confirmation">Confirm Password</label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="ti ti-lock text-secondary"></i>
                </span>
                <input type="password" name="password_confirmation" placeholder="Confirm password" class="form-control" id="password_confirmation" required>
            </div>
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-dark">Reset password</button>
        </div>
    </form>
@endsection
