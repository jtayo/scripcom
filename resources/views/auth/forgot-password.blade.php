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
                    <svg class="icon text-secondary" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path></svg>
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
