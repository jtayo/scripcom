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
                    <svg class="icon text-secondary" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                </span>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Full name" id="name" value="{{ old('name') }}" autofocus required>
            </div>
        </div>

        <div class="form-group mb-4">
            <label for="email">Your Email</label>
            <div class="input-group">
                <span class="input-group-text">
                    <svg class="icon text-secondary" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path></svg>
                </span>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="example@company.com" id="email" value="{{ old('email') }}" required>
            </div>
        </div>

        <div class="form-group">
            <div class="form-group mb-4">
                <label for="phone">Phone Number</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <svg class="icon text-secondary" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path></svg>
                    </span>
                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" placeholder="0712 345 678" id="phone" value="{{ old('phone') }}" required>
                </div>
            </div>
            <div class="form-group mb-4">
                <label for="password">Password</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <svg class="icon text-secondary" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path></svg>
                    </span>
                    <input type="password" name="password" placeholder="Password" class="form-control @error('password') is-invalid @enderror" id="password" required>
                </div>
            </div>
            <div class="form-group mb-4">
                <label for="password_confirmation">Confirm Password</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <svg class="icon text-secondary" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path></svg>
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
