@extends('layouts.auth')

@section('title', 'Register')

@section('auth-title', 'Create an account')

@section('auth-content')
    <form method="POST" action="{{ route('register') }}" class="mt-4">
        @csrf

        <div class="form-group mb-4">
            <label for="name">Your Name</label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="ti ti-user text-secondary"></i>
                </span>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Full name" id="name" value="{{ old('name') }}" autofocus required>
            </div>
        </div>

        <div class="form-group mb-4">
            <label for="email">Your Email</label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="ti ti-mail text-secondary"></i>
                </span>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="example@company.com" id="email" value="{{ old('email') }}" required>
            </div>
        </div>

        <div class="form-group">
            <div class="form-group mb-4">
                <label for="phone">Phone Number</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="ti ti-phone text-secondary"></i>
                    </span>
                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" placeholder="0712 345 678" id="phone" value="{{ old('phone') }}" required>
                </div>
            </div>
            <div class="form-group mb-4">
                <label for="password">Password</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="ti ti-lock text-secondary"></i>
                    </span>
                    <input type="password" name="password" placeholder="Password" class="form-control @error('password') is-invalid @enderror" id="password" required>
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
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-dark">Sign up</button>
        </div>
    </form>

    <div class="d-flex justify-content-center align-items-center mt-4">
        <span class="fw-normal">Already have an account?</span>
        <a href="{{ route('login') }}" class="fw-bold ms-1 text-primary">Sign in</a>
    </div>
@endsection
