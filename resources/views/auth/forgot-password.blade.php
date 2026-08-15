@extends('layouts.auth')

@section('title', 'Forgot Password')

@section('auth-title', 'Reset your password')

@section('auth-content')
    <p class="text-center text-muted mb-4">Enter your email address and we will send you a link to reset your password.</p>

    <form method="POST" action="{{ route('password.email') }}" class="mt-4">
        @csrf

        <div class="form-group mb-4">
            <label for="email">Your Email</label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="ti ti-mail text-secondary"></i>
                </span>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="example@company.com" id="email" value="{{ old('email') }}" autofocus required>
            </div>
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-dark">Send reset link</button>
        </div>
    </form>

    <div class="d-flex justify-content-center align-items-center mt-4">
        <span class="fw-normal">Remembered your password?</span>
        <a href="{{ route('login') }}" class="fw-bold ms-1 text-primary">Sign in</a>
    </div>
@endsection
