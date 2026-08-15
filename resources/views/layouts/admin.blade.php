<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title', 'Dashboard') — {{ config('app.name') }}</title>
        <meta name="description" content="{{ config('app.name') }} — Smart Public Wi-Fi & Civic Engagement Platform">

        <link rel="icon" href="{{ asset('favicon.ico') }}">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link
            href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
            rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

        <link type="text/css" href="{{ asset('vendor/tabler/css/tabler.min.css') }}" rel="stylesheet">
        <link type="text/css" href="{{ asset('vendor/tabler/css/tabler-icons.min.css') }}" rel="stylesheet">

        <style>
            body {
                font-family: 'Plus Jakarta Sans', var(--tblr-font-sans-serif);
            }

            .nav-section-title {
                padding: .5rem var(--tblr-page-padding) .25rem;
                margin-top: 1rem;
                font-size: .65rem;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: .05em;
                color: var(--tblr-secondary);
            }

            .navbar-vertical .nav-link {
                font-size: .875rem;
            }

            .navbar-vertical .nav-link-title {
                font-size: .875rem;
            }

            .navbar-vertical .nav-link-icon {
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }

            .navbar-vertical .nav-link-icon .ti {
                font-size: 1.1rem;
            }
        </style>
        @yield('head')
        @stack('styles')
    </head>

    <body @yield('body-attrs')>

        <div class="page">

            <aside class="navbar navbar-vertical navbar-expand-lg" data-bs-theme="dark">
                <div class="container-fluid">
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                        data-bs-target="#sidebar-menu" aria-controls="sidebar-menu" aria-expanded="false"
                        aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <h1 class="navbar-brand navbar-brand-autodark">
                        <a href="{{ route('admin.dashboard') }}">
                            <img src="{{ asset('scripcom_logo.png') }}" class="navbar-brand-image"
                                alt="{{ config('app.name') }}">
                        </a>
                    </h1>
                    <div class="collapse navbar-collapse" id="sidebar-menu">
                        <ul class="navbar-nav pt-lg-3">

                            <li class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('admin.dashboard') }}">
                                    <span class="nav-link-icon"><i class="ti ti-layout-dashboard"></i></span>
                                    <span class="nav-link-title">Dashboard</span>
                                </a>
                            </li>

                            @if (auth()->user()->can('view-any-organization') || auth()->user()->can('view-any-user'))
                                <li class="nav-section-title">Management</li>
                            @endif

                            @can('view-any-organization')
                                <li class="nav-item {{ request()->routeIs('admin.organizations.*') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('admin.organizations.index') }}">
                                        <span class="nav-link-icon"><i class="ti ti-building-community"></i></span>
                                        <span class="nav-link-title">Organizations</span>
                                    </a>
                                </li>
                            @endcan

                            @can('view-any-user')
                                <li class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('admin.users.index') }}">
                                        <span class="nav-link-icon"><i class="ti ti-users"></i></span>
                                        <span class="nav-link-title">Users</span>
                                    </a>
                                </li>
                            @endcan

                            @can('view-any-hotspot')
                                <li class="nav-item {{ request()->routeIs('admin.hotspots.*') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('admin.hotspots.index') }}">
                                        <span class="nav-link-icon"><i class="ti ti-wifi"></i></span>
                                        <span class="nav-link-title">Hotspots</span>
                                    </a>
                                </li>
                            @endcan

                            @can('view-any-campaign')
                                <li class="nav-item {{ request()->routeIs('admin.campaigns.*') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('admin.campaigns.index') }}">
                                        <span class="nav-link-icon"><i class="ti ti-speakerphone"></i></span>
                                        <span class="nav-link-title">Campaigns</span>
                                    </a>
                                </li>
                            @endcan

                            @if (auth()->user()->can('view-any-sponsor') ||
                                    auth()->user()->can('view-any-sponsorship') ||
                                    auth()->user()->can('buy-credits'))
                                <li class="nav-section-title">Sponsorship</li>
                            @endif

                            @can('view-any-sponsor')
                                <li class="nav-item {{ request()->routeIs('admin.sponsors.*') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('admin.sponsors.index') }}">
                                        <span class="nav-link-icon"><i class="ti ti-heart-handshake"></i></span>
                                        <span class="nav-link-title">Sponsors</span>
                                    </a>
                                </li>
                            @endcan

                            @can('view-any-sponsorship')
                                <li class="nav-item {{ request()->routeIs('admin.sponsorships.*') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('admin.sponsorships.index') }}">
                                        <span class="nav-link-icon"><i class="ti ti-credit-card"></i></span>
                                        <span class="nav-link-title">Sponsorships</span>
                                    </a>
                                </li>
                            @endcan

                            @can('buy-credits')
                                <li class="nav-item {{ request()->routeIs('admin.buy-credits') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('admin.buy-credits') }}">
                                        <span class="nav-link-icon"><i class="ti ti-wallet"></i></span>
                                        <span class="nav-link-title">Buy Credits</span>
                                    </a>
                                </li>
                            @endcan

                            @if (auth()->user()->can('view-any-session') ||
                                    auth()->user()->can('view-any-event') ||
                                    auth()->user()->can('view-any-payment'))
                                <li class="nav-section-title">Monitoring</li>
                            @endif

                            @can('view-any-session')
                                <li class="nav-item {{ request()->routeIs('admin.sessions.*') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('admin.sessions.index') }}">
                                        <span class="nav-link-icon"><i class="ti ti-clock"></i></span>
                                        <span class="nav-link-title">Sessions</span>
                                    </a>
                                </li>
                            @endcan

                            @can('view-any-event')
                                <li class="nav-item {{ request()->routeIs('admin.events.*') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('admin.events.index') }}">
                                        <span class="nav-link-icon"><i class="ti ti-calendar-event"></i></span>
                                        <span class="nav-link-title">Events</span>
                                    </a>
                                </li>
                            @endcan

                            @can('view-any-payment')
                                <li class="nav-item {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('admin.payments.index') }}">
                                        <span class="nav-link-icon"><i class="ti ti-cash"></i></span>
                                        <span class="nav-link-title">Payments</span>
                                    </a>
                                </li>
                            @endcan

                            @can('view-any-voucher')
                                <li class="nav-item {{ request()->routeIs('admin.vouchers.*') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('admin.vouchers.index') }}">
                                        <span class="nav-link-icon"><i class="ti ti-ticket"></i></span>
                                        <span class="nav-link-title">Vouchers</span>
                                    </a>
                                </li>
                            @endcan

                            @canany(['view-settings', 'update-settings', 'view-any-role', 'view-any-permission'])
                                <li class="nav-section-title">System</li>
                                @canany(['view-settings', 'update-settings'])
                                <li class="nav-item {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('admin.settings') }}">
                                        <span class="nav-link-icon"><i class="ti ti-settings"></i></span>
                                        <span class="nav-link-title">Settings</span>
                                    </a>
                                </li>
                                @endcanany
                                @can('view-any-role')
                                <li class="nav-item {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('admin.roles.index') }}">
                                        <span class="nav-link-icon"><i class="ti ti-shield"></i></span>
                                        <span class="nav-link-title">Roles</span>
                                    </a>
                                </li>
                                @endcan
                                @can('view-any-permission')
                                <li class="nav-item {{ request()->routeIs('admin.permissions.*') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('admin.permissions.index') }}">
                                        <span class="nav-link-icon"><i class="ti ti-lock"></i></span>
                                        <span class="nav-link-title">Permissions</span>
                                    </a>
                                </li>
                                @endcan
                            @endcanany
                        </ul>
                    </div>
                </div>
            </aside>

            <div class="page-wrapper">

                <div class="page-header d-print-none">
                    <div class="container-xl">
                        <div class="row g-2 align-items-center">
                            <div class="col">
                                <div class="page-pretitle">{{ config('app.name') }} Admin</div>
                                <h2 class="page-title">@yield('page-title', 'Dashboard')</h2>
                                @if ($__env->yieldContent('page-subtitle'))
                                    <div class="text-secondary mt-1">@yield('page-subtitle')</div>
                                @endif
                            </div>
                            <div class="col-auto ms-auto d-print-none">
                                <div class="dropdown">
                                    <a href="#" class="nav-link d-flex lh-1 text-reset p-0"
                                        data-bs-toggle="dropdown" aria-label="Open user menu">
                                        <span class="avatar avatar-sm me-2"
                                            style="background-image: url('{{ auth()->user()->avatar ?? asset('img/team/profile-picture-3.jpg') }}')"></span>
                                        <div class="d-none d-xl-block text-start">
                                            <div class="fw-semibold text-body">{{ auth()->user()->name }}</div>
                                            <div class="mt-1 small text-secondary">
                                                {{ auth()->user()->getRoleNames()->first() ?? '' }}</div>
                                        </div>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                        <a class="dropdown-item" href="{{ route('admin.profile') }}">
                                            <i class="ti ti-user me-2"></i>My Profile
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item">
                                                <i class="ti ti-logout text-danger me-2"></i>Logout
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page-body">
                    <div class="container-xl">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Please fix the following errors:</strong>
                                <ul class="mb-0 mt-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        @yield('content')
                    </div>
                </div>

                <footer class="footer">
                    <div class="container">
                        <div class="row text-center align-items-center flex-row-reverse">
                            <div class="col-lg-auto ms-lg-auto">
                                <ul class="list-inline list-inline-dots mb-0">
                                    <li class="list-inline-item"><a href="{{ route('admin.settings') }}">Settings</a>
                                    </li>
                                    <li class="list-inline-item"><a href="{{ config('app.url') }}">Public Portal</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-12 col-lg-auto mt-3 mt-lg-0">
                                &copy; {{ date('Y') }} <a href="#"
                                    class="text-reset text-primary">{{ config('app.name') }}</a> — Powered by
                                Scripcom.
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>

        <script src="{{ asset('vendor/tabler/js/tabler.min.js') }}"></script>
        <script src="{{ asset('vendor/tabler/vendor/chartjs/chart.umd.js') }}"></script>
        @stack('scripts')
    </body>

</html>
