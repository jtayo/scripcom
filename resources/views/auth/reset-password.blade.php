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
                    <svg class="icon text-secondary" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path></svg>
                </span>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="example@company.com" id="email" value="{{ old('email', $request->email) }}" autofocus required>
            </div>
        </div>

        <div class="form-group mb-4">
            <label for="password">New Password</label>
            <div class="input-group">
                <span class="input-group-text">
                    <svg class="icon text-secondary" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path></svg>
                </span>
                <input type="password" name="password" placeholder="New password" class="form-control @error('password') is-invalid @enderror" id="password" required>
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

        <div class="d-grid">
            <button type="submit" class="btn btn-dark">Reset password</button>
        </div>
    </form>
@endsection
