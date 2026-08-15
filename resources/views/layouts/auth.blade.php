<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Authentication') — {{ config('app.name') }}</title>

    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <link type="text/css" href="{{ asset('vendor/tabler/css/tabler.min.css') }}" rel="stylesheet">
    <link type="text/css" href="{{ asset('vendor/tabler/css/tabler-icons.min.css') }}" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', var(--tblr-font-sans-serif);
            background-color: var(--tblr-bg-secondary);
        }
        .auth-form .form-label {
            font-size: .8125rem;
            color: var(--tblr-secondary);
        }
        .auth-form .form-control,
        .auth-form .input-group-text {
            font-size: .875rem;
            padding-top: .5rem;
            padding-bottom: .5rem;
        }
        .auth-form .form-check-label {
            font-size: .8125rem;
        }
        .auth-form .btn {
            font-size: .875rem;
            padding-top: .55rem;
            padding-bottom: .55rem;
        }
        .auth-form .auth-heading {
            font-size: 1.25rem;
        }
        .auth-form .auth-subtitle {
            font-size: .8125rem;
        }
        .auth-form .auth-logo {
            max-height: 48px;
        }
        .auth-form .auth-footer {
            font-size: .8125rem;
        }
    </style>
    @stack('styles')
</head>

<body>

    <main>
        <div class="row g-0 min-vh-100">

            <div class="col-lg-6 d-none d-lg-flex align-items-center justify-content-center text-center text-white p-5 position-relative overflow-hidden" style="background: linear-gradient(160deg, #111827 0%, #1F2937 55%, #31316A 130%);">
                <div class="position-relative w-100" style="z-index: 2;">
                    <span class="text-uppercase small fw-light text-white-50">Digital loyalty made simple</span>
                    <h1 class="display-5 fw-light mt-2 mb-4">{{ config('app.name') }}</h1>
                    <img src="{{ asset('img/illustrations/signin.svg') }}" alt="Sign in illustration" class="img-fluid mx-auto" style="max-width: 420px;">
                    <p class="lead text-white-50 mt-4 mb-0 mx-auto" style="max-width: 380px;">Manage sponsorships, campaigns and redemptions from a single, secure dashboard.</p>
                </div>
            </div>

            <div class="col-lg-6 d-flex align-items-center justify-content-center p-4 p-lg-5 bg-light">
                <div class="w-100 container-tight">
                    <div class="auth-form">
                        <div class="text-center mb-4">
                            <a href="{{ route('login') }}" class="d-inline-block mb-3">
                                <img src="{{ asset('scripcom_logo.png') }}" alt="{{ config('app.name') }} logo" class="img-fluid auth-logo">
                            </a>
                            <h1 class="mb-1 fw-bold auth-heading">@yield('auth-title', 'Sign in to the platform')</h1>
                            @if($__env->yieldContent('auth-subtitle'))
                                <p class="text-muted mb-0 auth-subtitle">@yield('auth-subtitle')</p>
                            @endif
                        </div>

                        @if(session('status'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('status') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Please fix the following errors:</strong>
                                <ul class="mb-0 mt-1">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @yield('auth-content')
                    </div>
                </div>
            </div>

        </div>
    </main>

    <script src="{{ asset('vendor/tabler/js/tabler.min.js') }}"></script>
    @stack('scripts')
</body>

</html>
