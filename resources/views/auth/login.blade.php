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
                    <i class="ti ti-mail text-secondary"></i>
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
                    <i class="ti ti-lock text-secondary"></i>
                </span>
                <input type="password" name="password" id="password"
                    class="form-control @error('password') is-invalid @enderror"
                    placeholder="Your password" autocomplete="current-password" required>
                <button type="button" class="btn btn-outline-secondary border-0" id="toggle-password" aria-label="Show password">
                    <i class="ti ti-eye text-secondary" id="icon-eye"></i>
                    <i class="ti ti-eye-off text-secondary d-none" id="icon-eye-off"></i>
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
