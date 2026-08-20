<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    @php
        $portalPrimaryColor = isset($portalColor) && $portalColor ? $portalColor : '#262B40';
        $portalOrgName = isset($portalOrg) && $portalOrg ? $portalOrg : config('app.name');
        $portalLogoUrl = isset($portalLogo) ? $portalLogo : null;
        $portalLocationLabel = isset($portalLocation) ? $portalLocation : null;
    @endphp

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title', 'SCRIPCOM Wi-Fi') — {{ $portalOrgName }}</title>
        <meta name="description" content="SCRIPCOM — Smart Public Wi-Fi & Civic Engagement Platform">

        <link rel="icon" href="{{ asset('favicon.ico') }}">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

        <link type="text/css" href="{{ asset('vendor/tabler/css/tabler.min.css') }}" rel="stylesheet">
        <link type="text/css" href="{{ asset('vendor/tabler/css/tabler-icons.min.css') }}" rel="stylesheet">

        <style>
            :root {
                --portal-primary: {{ $portalPrimaryColor }};
            }

            body {
                font-family: 'Plus Jakarta Sans', var(--tblr-font-sans-serif);
                background:
                    radial-gradient(1200px 600px at 100% -10%, color-mix(in srgb, var(--portal-primary) 18%, transparent), transparent),
                    radial-gradient(1000px 500px at -10% 110%, color-mix(in srgb, var(--portal-primary) 12%, transparent), transparent),
                    var(--tblr-bg-surface-secondary);
                min-height: 100vh;
            }

            .portal-shell {
                min-height: 100vh;
                display: flex;
                flex-direction: column;
            }

            .portal-main {
                flex: 1;
                display: flex;
                align-items: center;
            }

            .portal-card {
                border: 0;
                border-radius: 1.25rem;
                box-shadow: 0 1rem 3rem rgba(0, 0, 0, .08);
                overflow: hidden;
            }

            .portal-logo {
                height: 2.75rem;
                object-fit: contain;
            }

            .portal-badge {
                background: color-mix(in srgb, var(--portal-primary) 12%, white);
                color: var(--portal-primary);
                font-size: .75rem;
                font-weight: 600;
                padding: .35rem .75rem;
                border-radius: 999px;
            }

            .portal-btn-primary {
                background: var(--portal-primary);
                border-color: var(--portal-primary);
                color: #fff;
            }

            .portal-btn-primary:hover,
            .portal-btn-primary:focus {
                background: color-mix(in srgb, var(--portal-primary) 88%, black);
                border-color: color-mix(in srgb, var(--portal-primary) 88%, black);
                color: #fff;
            }

            .portal-icon {
                width: 3.5rem;
                height: 3.5rem;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border-radius: 1rem;
                font-size: 1.5rem;
                background: color-mix(in srgb, var(--portal-primary) 12%, white);
                color: var(--portal-primary);
            }

            .portal-progress {
                height: .75rem;
                background: color-mix(in srgb, var(--portal-primary) 12%, white);
                border-radius: 999px;
                overflow: hidden;
            }

            .portal-progress-bar {
                height: 100%;
                background: var(--portal-primary);
                border-radius: 999px;
                transition: width .4s ease;
            }

            .portal-timer-ring {
                width: 12rem;
                height: 12rem;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                background: conic-gradient(var(--portal-primary) 0%, color-mix(in srgb, var(--portal-primary) 12%, white) 0%);
                position: relative;
            }

            .portal-timer-ring::after {
                content: "";
                position: absolute;
                inset: .75rem;
                border-radius: 50%;
                background: var(--tblr-bg-surface);
            }

            .portal-timer-ring .timer-content {
                position: relative;
                z-index: 1;
                text-align: center;
            }

            .portal-video {
                max-height: 42vh;
                width: 100%;
                object-fit: contain;
                background: #000;
                border-radius: 1rem;
            }

            .portal-ad {
                width: 100%;
                aspect-ratio: 16 / 9;
                border-radius: 1rem;
                overflow: hidden;
                display: flex;
                align-items: center;
                justify-content: center;
                background: linear-gradient(135deg, color-mix(in srgb, var(--portal-primary) 30%, white), color-mix(in srgb, var(--portal-primary) 8%, white));
                position: relative;
            }

            .portal-ad img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .portal-play-overlay {
                position: absolute;
                inset: 0;
                display: flex;
                align-items: center;
                justify-content: center;
                background: rgba(0, 0, 0, 0.35);
                border: none;
                cursor: pointer;
                z-index: 10;
                border-radius: 1rem;
            }

            .portal-play-overlay i {
                font-size: 4rem;
                color: #fff;
                filter: drop-shadow(0 2px 8px rgba(0, 0, 0, 0.4));
                transition: transform 0.2s;
            }

            .portal-play-overlay:hover i {
                transform: scale(1.15);
            }

            .portal-timer {
                font-variant-numeric: tabular-nums;
                font-size: 2.25rem;
                font-weight: 800;
                color: var(--portal-primary);
                line-height: 1;
            }

            .portal-countdown {
                font-variant-numeric: tabular-nums;
                font-size: 2rem;
                font-weight: 800;
                color: var(--portal-primary);
            }

            .portal-field {
                border-radius: .75rem;
                padding: .75rem 1rem;
                border: 1px solid var(--tblr-border-color);
            }

            .portal-field:focus {
                border-color: var(--portal-primary);
                box-shadow: 0 0 0 .25rem color-mix(in srgb, var(--portal-primary) 20%, transparent);
            }

            .portal-footer {
                font-size: .8125rem;
                color: var(--tblr-secondary-color);
            }

            .portal-footer a {
                color: var(--tblr-body-color);
            }
        </style>
        @stack('styles')
    </head>

    <body class="layout-fluid">
        <div class="portal-shell">
            <header class="py-3">
                <div class="container">
                    <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        @if ($portalLogoUrl)
                            <img src="{{ $portalLogoUrl }}" alt="" class="portal-logo">
                        @else
                            <span class="portal-icon" style="width:2.5rem;height:2.5rem;font-size:1.1rem;"><i class="fa-solid fa-wifi"></i></span>
                        @endif
                        <span class="fw-bold fs-4 text-body">{{ $portalOrgName }}</span>
                    </div>
                    @if ($portalLocationLabel)
                        <span class="portal-badge d-none d-sm-inline-flex">
                            <i class="fa-solid fa-location-dot me-1"></i>{{ $portalLocationLabel }}
                        </span>
                    @endif
                    </div>
                </div>
            </header>

            <main class="portal-main">
                <div class="container py-4">
                    @if (session('error'))
                        <div class="alert alert-danger d-flex align-items-center" role="alert">
                            <i class="fa-solid fa-circle-exclamation me-2"></i>{{ session('error') }}
                        </div>
                    @endif
                    @yield('content')
                </div>
            </main>

            <footer class="portal-footer py-3">
                <div class="container">
                    <div class="d-flex flex-column flex-sm-row align-items-center justify-content-between gap-2">
                        <span>{{ $portalOrgName }} &copy; {{ date('Y') }}</span>
                        <div class="d-flex gap-3">
                            <a href="{{ route('portal.welcome') }}">Terms of Service</a>
                            <a href="{{ route('portal.welcome') }}">Privacy Policy</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>

        <script src="{{ asset('vendor/tabler/js/tabler.min.js') }}"></script>
        <script>
            window.portalConfig = {
                primaryColor: "{{ $portalPrimaryColor }}"
            };
        </script>
        @stack('scripts')
    </body>

</html>
