@extends('layouts.auth')

@section('title', 'Sign in')

@section('auth-title', 'Welcome back')

@section('auth-subtitle', 'Sign in to your account to continue.')

@section('auth-content')
    <form method="POST" action="{{ route('login') }}" id="login-form" novalidate>
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">Email address</label>
            <div class="input-group">
                <span class="input-group-text">
                    <svg class="icon text-secondary" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path></svg>
                </span>
                <input type="email" name="email" id="email" value="{{ old('email') }}"
                    class="form-control @error('email') is-invalid @enderror"
                    placeholder="name@company.com" autocomplete="email" autofocus required>
            </div>
            @error('email')
                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <label for="password" class="form-label mb-0">Password</label>
                <a href="{{ route('password.request') }}" class="small fw-bold text-primary">Forgot password?</a>
            </div>
            <div class="input-group">
                <span class="input-group-text">
                    <svg class="icon text-secondary" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path></svg>
                </span>
                <input type="password" name="password" id="password"
                    class="form-control @error('password') is-invalid @enderror"
                    placeholder="Your password" autocomplete="current-password" required>
                <button type="button" class="btn btn-outline-secondary border-0" id="toggle-password" aria-label="Show password">
                    <svg class="icon text-secondary" id="icon-eye" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    <svg class="icon text-secondary d-none" id="icon-eye-off" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.05 10.05 0 012.312-3.762M6.09 6.09A10.05 10.05 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.05 10.05 0 01-2.312 3.762m-2.33 1.332a10.05 10.05 0 01-4.9 1.106"></path><path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18"></path></svg>
                </button>
            </div>
            @error('password')
                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="remember" id="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
            <label class="form-check-label" for="remember">Remember me</label>
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-primary" id="login-submit">
                <span class="spinner-border spinner-border-sm d-none" id="login-spinner" role="status" aria-hidden="true"></span>
                <span id="login-text">Sign in</span>
            </button>
        </div>
    </form>

    <p class="text-center text-muted mt-4 mb-0 auth-footer">
        Don't have an account?
        <a href="{{ route('register') }}" class="fw-bold text-primary">Create an account</a>
    </p>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggle = document.getElementById('toggle-password');
            const password = document.getElementById('password');
            const eye = document.getElementById('icon-eye');
            const eyeOff = document.getElementById('icon-eye-off');

            toggle.addEventListener('click', function () {
                const show = password.type === 'password';
                password.type = show ? 'text' : 'password';
                eye.classList.toggle('d-none', show);
                eyeOff.classList.toggle('d-none', !show);
                toggle.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
            });

            const form = document.getElementById('login-form');
            form.addEventListener('submit', function () {
                if (form.checkValidity()) {
                    const spinner = document.getElementById('login-spinner');
                    const text = document.getElementById('login-text');
                    spinner.classList.remove('d-none');
                    text.textContent = 'Signing in...';
                    form.querySelector('button[type="submit"]').disabled = true;
                }
            });
        });
    </script>
@endpush
