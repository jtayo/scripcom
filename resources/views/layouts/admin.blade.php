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

            .navbar-vertical .nav-submenu {
                display: block;
                list-style: none;
                margin: 0 0 .5rem;
                padding: 0;
            }

            .navbar-vertical .nav-submenu .nav-link {
                padding-top: .25rem;
                padding-bottom: .25rem;
                padding-left: calc(var(--tblr-page-padding) + 2rem);
                color: var(--tblr-muted-color);
                font-size: .8125rem;
            }

            .navbar-vertical .nav-submenu .nav-link:hover,
            .navbar-vertical .nav-submenu .nav-item.active .nav-link {
                color: var(--tblr-body-color);
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

                            @can('view-analytics')
                                <li class="nav-item {{ request()->routeIs('admin.analytics') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('admin.analytics') }}">
                                        <span class="nav-link-icon"><i class="ti ti-chart-pie"></i></span>
                                        <span class="nav-link-title">Analytics</span>
                                    </a>
                                </li>
                            @endcan

                            @can('view-any-router')
                                <li class="nav-item {{ request()->routeIs('admin.device-monitoring') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('admin.device-monitoring') }}">
                                        <span class="nav-link-icon"><i class="ti ti-device-desktop-analytics"></i></span>
                                        <span class="nav-link-title">Device Monitoring</span>
                                    </a>
                                </li>
                            @endcan

                            @php
                                $showManagement = auth()->user()->can('view-any-organization') || auth()->user()->can('view-any-user') || auth()->user()->can('view-any-hotspot') || auth()->user()->can('view-any-router') || auth()->user()->can('view-any-campaign') || auth()->user()->can('view-any-package');
                                $managementActive = request()->routeIs('admin.organizations.*', 'admin.users.*', 'admin.hotspots.*', 'admin.routers.*', 'admin.campaigns.*', 'admin.packages.*');
                            @endphp

                            @if ($showManagement)
                                <li class="nav-item">
                                    <a class="nav-link" href="#sidebar-management" data-bs-toggle="collapse" role="button" aria-expanded="{{ $managementActive ? 'true' : 'false' }}" aria-controls="sidebar-management">
                                        <span class="nav-link-icon"><i class="ti ti-tools"></i></span>
                                        <span class="nav-link-title">Management</span>
                                        <span class="nav-link-toggle"></span>
                                    </a>
                                    <div class="collapse {{ $managementActive ? 'show' : '' }}" id="sidebar-management">
                                        <ul class="nav nav-submenu mb-1">
                                            @can('view-any-organization')
                                                <li class="nav-item {{ request()->routeIs('admin.organizations.*') ? 'active' : '' }}">
                                                    <a class="nav-link" href="{{ route('admin.organizations.index') }}">
                                                        <span class="nav-link-title">Organizations</span>
                                                    </a>
                                                </li>
                                            @endcan

                                            @can('view-any-user')
                                                <li class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                                                    <a class="nav-link" href="{{ route('admin.users.index') }}">
                                                        <span class="nav-link-title">Users</span>
                                                    </a>
                                                </li>
                                            @endcan

                                            @can('view-any-hotspot')
                                                <li class="nav-item {{ request()->routeIs('admin.hotspots.*') ? 'active' : '' }}">
                                                    <a class="nav-link" href="{{ route('admin.hotspots.index') }}">
                                                        <span class="nav-link-title">Hotspots</span>
                                                    </a>
                                                </li>
                                            @endcan

                                            @can('view-any-router')
                                                <li class="nav-item {{ request()->routeIs('admin.routers.*') ? 'active' : '' }}">
                                                    <a class="nav-link" href="{{ route('admin.routers.index') }}">
                                                        <span class="nav-link-title">Routers</span>
                                                    </a>
                                                </li>
                                            @endcan

                                            @can('view-any-campaign')
                                                <li class="nav-item {{ request()->routeIs('admin.campaigns.*') ? 'active' : '' }}">
                                                    <a class="nav-link" href="{{ route('admin.campaigns.index') }}">
                                                        <span class="nav-link-title">Campaigns</span>
                                                    </a>
                                                </li>
                                            @endcan

                                            @can('view-any-package')
                                                <li class="nav-item {{ request()->routeIs('admin.packages.*') ? 'active' : '' }}">
                                                    <a class="nav-link" href="{{ route('admin.packages.index') }}">
                                                        <span class="nav-link-title">Wi-Fi Packages</span>
                                                    </a>
                                                </li>
                                            @endcan
                                        </ul>
                                    </div>
                                </li>
                            @endif

                            @php
                                $showSponsorship = auth()->user()->can('view-any-sponsor') || auth()->user()->can('view-any-sponsorship') || auth()->user()->can('buy-credits');
                                $sponsorshipActive = request()->routeIs('admin.sponsors.*', 'admin.sponsorships.*', 'admin.buy-credits');
                            @endphp

                            @if ($showSponsorship)
                                <li class="nav-item">
                                    <a class="nav-link" href="#sidebar-sponsorship" data-bs-toggle="collapse" role="button" aria-expanded="{{ $sponsorshipActive ? 'true' : 'false' }}" aria-controls="sidebar-sponsorship">
                                        <span class="nav-link-icon"><i class="ti ti-heart-handshake"></i></span>
                                        <span class="nav-link-title">Sponsorship</span>
                                        <span class="nav-link-toggle"></span>
                                    </a>
                                    <div class="collapse {{ $sponsorshipActive ? 'show' : '' }}" id="sidebar-sponsorship">
                                        <ul class="nav nav-submenu mb-1">
                                            @can('view-any-sponsor')
                                                <li class="nav-item {{ request()->routeIs('admin.sponsors.*') ? 'active' : '' }}">
                                                    <a class="nav-link" href="{{ route('admin.sponsors.index') }}">
                                                        <span class="nav-link-title">Sponsors</span>
                                                    </a>
                                                </li>
                                            @endcan

                                            @can('view-any-sponsorship')
                                                <li class="nav-item {{ request()->routeIs('admin.sponsorships.*') ? 'active' : '' }}">
                                                    <a class="nav-link" href="{{ route('admin.sponsorships.index') }}">
                                                        <span class="nav-link-title">Sponsorships</span>
                                                    </a>
                                                </li>
                                            @endcan

                                            @can('buy-credits')
                                                <li class="nav-item {{ request()->routeIs('admin.buy-credits') ? 'active' : '' }}">
                                                    <a class="nav-link" href="{{ route('admin.buy-credits') }}">
                                                        <span class="nav-link-title">Buy Credits</span>
                                                    </a>
                                                </li>
                                            @endcan
                                        </ul>
                                    </div>
                                </li>
                            @endif

                            @php
                                $showMonitoring = auth()->user()->can('view-any-session') || auth()->user()->can('view-any-event') || auth()->user()->can('view-any-payment');
                                $monitoringActive = request()->routeIs('admin.sessions.*', 'admin.events.*', 'admin.payments.*');
                            @endphp

                            @if ($showMonitoring)
                                <li class="nav-item">
                                    <a class="nav-link" href="#sidebar-monitoring" data-bs-toggle="collapse" role="button" aria-expanded="{{ $monitoringActive ? 'true' : 'false' }}" aria-controls="sidebar-monitoring">
                                        <span class="nav-link-icon"><i class="ti ti-activity"></i></span>
                                        <span class="nav-link-title">Monitoring</span>
                                        <span class="nav-link-toggle"></span>
                                    </a>
                                    <div class="collapse {{ $monitoringActive ? 'show' : '' }}" id="sidebar-monitoring">
                                        <ul class="nav nav-submenu mb-1">
                                            @can('view-any-session')
                                                <li class="nav-item {{ request()->routeIs('admin.sessions.*') ? 'active' : '' }}">
                                                    <a class="nav-link" href="{{ route('admin.sessions.index') }}">
                                                        <span class="nav-link-title">Sessions</span>
                                                    </a>
                                                </li>
                                            @endcan

                                            @can('view-any-event')
                                                <li class="nav-item {{ request()->routeIs('admin.events.*') ? 'active' : '' }}">
                                                    <a class="nav-link" href="{{ route('admin.events.index') }}">
                                                        <span class="nav-link-title">Events</span>
                                                    </a>
                                                </li>
                                            @endcan

                                            @can('view-any-payment')
                                                <li class="nav-item {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                                                    <a class="nav-link" href="{{ route('admin.payments.index') }}">
                                                        <span class="nav-link-title">Payments</span>
                                                    </a>
                                                </li>
                                            @endcan
                                        </ul>
                                    </div>
                                </li>
                            @endif

                            @can('view-reports')
                                <li class="nav-item {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('admin.reports.index') }}">
                                        <span class="nav-link-icon"><i class="ti ti-report"></i></span>
                                        <span class="nav-link-title">Reports</span>
                                    </a>
                                </li>
                            @endcan

                            @php
                                $showBilling = auth()->user()->can('view-any-contract') || auth()->user()->can('view-any-invoice') || auth()->user()->can('view-any-revenue');
                                $billingActive = request()->routeIs('admin.billing.index', 'admin.contracts.*', 'admin.invoices.*', 'admin.revenue');
                            @endphp

                            @if ($showBilling)
                                <li class="nav-item">
                                    <a class="nav-link" href="#sidebar-billing" data-bs-toggle="collapse" role="button" aria-expanded="{{ $billingActive ? 'true' : 'false' }}" aria-controls="sidebar-billing">
                                        <span class="nav-link-icon"><i class="ti ti-receipt-2"></i></span>
                                        <span class="nav-link-title">Billing</span>
                                        <span class="nav-link-toggle"></span>
                                    </a>
                                    <div class="collapse {{ $billingActive ? 'show' : '' }}" id="sidebar-billing">
                                        <ul class="nav nav-submenu mb-1">
                                            @can('view-any-invoice')
                                                <li class="nav-item {{ request()->routeIs('admin.billing.index') ? 'active' : '' }}">
                                                    <a class="nav-link" href="{{ route('admin.billing.index') }}">
                                                        <span class="nav-link-title">Billing Dashboard</span>
                                                    </a>
                                                </li>
                                            @endcan

                                            @can('view-any-contract')
                                                <li class="nav-item {{ request()->routeIs('admin.contracts.*') ? 'active' : '' }}">
                                                    <a class="nav-link" href="{{ route('admin.contracts.index') }}">
                                                        <span class="nav-link-title">Contracts</span>
                                                    </a>
                                                </li>
                                            @endcan

                                            @can('view-any-invoice')
                                                <li class="nav-item {{ request()->routeIs('admin.invoices.*') ? 'active' : '' }}">
                                                    <a class="nav-link" href="{{ route('admin.invoices.index') }}">
                                                        <span class="nav-link-title">Invoices</span>
                                                    </a>
                                                </li>
                                            @endcan

                                            @can('view-any-revenue')
                                                <li class="nav-item {{ request()->routeIs('admin.revenue') ? 'active' : '' }}">
                                                    <a class="nav-link" href="{{ route('admin.revenue') }}">
                                                        <span class="nav-link-title">Revenue</span>
                                                    </a>
                                                </li>
                                            @endcan
                                        </ul>
                                    </div>
                                </li>
                            @endif

                            @php
                                $showSystem = auth()->user()->can('view-settings') || auth()->user()->can('update-settings') || auth()->user()->can('view-any-role') || auth()->user()->can('view-any-permission') || auth()->user()->can('view-any-audit-log');
                                $systemActive = request()->routeIs('admin.settings', 'admin.roles.*', 'admin.permissions.*', 'admin.audit-logs.*');
                            @endphp

                            @if ($showSystem)
                                <li class="nav-item">
                                    <a class="nav-link" href="#sidebar-system" data-bs-toggle="collapse" role="button" aria-expanded="{{ $systemActive ? 'true' : 'false' }}" aria-controls="sidebar-system">
                                        <span class="nav-link-icon"><i class="ti ti-settings"></i></span>
                                        <span class="nav-link-title">System</span>
                                        <span class="nav-link-toggle"></span>
                                    </a>
                                    <div class="collapse {{ $systemActive ? 'show' : '' }}" id="sidebar-system">
                                        <ul class="nav nav-submenu mb-1">
                                            @canany(['view-settings', 'update-settings'])
                                                <li class="nav-item {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                                                    <a class="nav-link" href="{{ route('admin.settings') }}">
                                                        <span class="nav-link-title">Settings</span>
                                                    </a>
                                                </li>
                                            @endcanany

                                            @can('view-any-role')
                                                <li class="nav-item {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                                                    <a class="nav-link" href="{{ route('admin.roles.index') }}">
                                                        <span class="nav-link-title">Roles</span>
                                                    </a>
                                                </li>
                                            @endcan

                                            @can('view-any-permission')
                                                <li class="nav-item {{ request()->routeIs('admin.permissions.*') ? 'active' : '' }}">
                                                    <a class="nav-link" href="{{ route('admin.permissions.index') }}">
                                                        <span class="nav-link-title">Permissions</span>
                                                    </a>
                                                </li>
                                            @endcan

                                            @can('view-any-audit-log')
                                                <li class="nav-item {{ request()->routeIs('admin.audit-logs.*') ? 'active' : '' }}">
                                                    <a class="nav-link" href="{{ route('admin.audit-logs.index') }}">
                                                        <span class="nav-link-title">Audit Logs</span>
                                                    </a>
                                                </li>
                                            @endcan
                                        </ul>
                                    </div>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </aside>

            <div class="page-wrapper">

                <div class="page-header d-print-none">
                    <div class="container-xl">
                        <div class="row g-2 align-items-center">
                            <div class="col">
                                <div class="page-pretitle">
                                    @php
                                        auth()->user()->loadMissing('organization');
                                        $layoutOrg = auth()->user()->organization;
                                    @endphp
                                    @if ($layoutOrg)
                                        <span class="badge bg-primary-lt me-1">{{ $layoutOrg->typeLabel() }}</span>
                                        {{ $layoutOrg->name }}
                                    @else
                                        {{ config('app.name') }} Admin
                                    @endif
                                </div>
                                <h2 class="page-title">@yield('page-title', 'Dashboard')</h2>
                                @if ($__env->yieldContent('page-subtitle'))
                                    <div class="text-secondary mt-1">@yield('page-subtitle')</div>
                                @endif
                            </div>
                            <div class="col-auto ms-auto d-print-none">
                                @can('view-notifications')
                                    @php
                                        $unreadCount = auth()->user()->unreadNotifications()->count();
                                        $recentNotifications = auth()->user()->notifications()->latest()->limit(5)->get();
                                    @endphp
                                    <div class="dropdown me-3">
                                        <a href="#" class="nav-link d-flex lh-1 text-reset p-0"
                                            data-bs-toggle="dropdown" aria-label="Open notifications"
                                            data-bs-auto-close="outside">
                                            <span class="position-relative">
                                                <i class="ti ti-bell text-secondary"></i>
                                                @if ($unreadCount > 0)
                                                    <span
                                                        class="position-absolute top-0 start-100 translate-middle badge bg-red rounded-pill">
                                                        {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                                                    </span>
                                                @endif
                                            </span>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow"
                                            style="width: 22rem;">
                                            <div class="d-flex align-items-center px-3 py-2">
                                                <div class="fw-semibold">Notifications</div>
                                                @if ($unreadCount > 0)
                                                    <form method="POST"
                                                        action="{{ route('admin.notifications.read-all') }}"
                                                        class="ms-auto">
                                                        @csrf
                                                        <button type="submit"
                                                            class="btn btn-sm btn-link text-secondary p-0">
                                                            Mark all as read
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                            <div class="dropdown-divider"></div>
                                            @forelse ($recentNotifications as $notification)
                                                @php
                                                    $data = $notification->data;
                                                    $levelColors = [
                                                        'danger' => 'text-danger',
                                                        'warning' => 'text-warning',
                                                        'success' => 'text-success',
                                                        'info' => 'text-primary',
                                                    ];
                                                @endphp
                                                <a href="{{ route('admin.notifications.show', $notification) }}"
                                                    class="dropdown-item d-flex text-wrap {{ $notification->read_at ? '' : 'bg-azure-lt' }}">
                                                    <span class="me-2 {{ $levelColors[$data['level'] ?? 'info'] ?? 'text-primary' }}">
                                                        <i class="ti ti-{{ $data['icon'] ?? 'bell' }}"></i>
                                                    </span>
                                                    <span class="d-flex flex-column">
                                                        <span class="fw-semibold">{{ $data['title'] ?? 'Notification' }}</span>
                                                        <span class="small text-secondary">{{ $data['message'] ?? '' }}</span>
                                                        <span class="small text-secondary mt-1">{{ $notification->created_at->diffForHumans() }}</span>
                                                    </span>
                                                </a>
                                            @empty
                                                <div class="dropdown-item text-secondary">You have no notifications.</div>
                                            @endforelse
                                            <div class="dropdown-divider"></div>
                                            <a href="{{ route('admin.notifications.index') }}"
                                                class="dropdown-item text-center fw-semibold">
                                                View all notifications
                                            </a>
                                        </div>
                                    </div>
                                @endcan
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
                                    <li class="list-inline-item">
                                        @canany(['view-settings', 'update-settings'])
                                        <a href="{{ route('admin.settings') }}">Settings</a>
                                        @endcanany
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
