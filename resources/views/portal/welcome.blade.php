@extends('layouts.portal')

@php
    $portalColor = config('brand.primary_color', '#262B40');
    $portalLogo = asset(config('brand.logo', 'scripcom_logo.png'));
    $portalOrg = config('brand.name', 'SCRIPCOM');
    $portalLocation = $data['location'] ?? null;
    $portalWelcome = $data['welcome_message'] ?? 'Welcome to free public Wi-Fi.';
    $portalCurrency = $data['currency'] ?? 'KES';
    $hasHotspot = $data !== null;
@endphp

@section('title', 'Connect to Wi-Fi')

@section('content')
    @if (! $hasHotspot)
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card portal-card">
                    <div class="card-body text-center p-5">
                        <span class="portal-icon mb-3"><i class="fa-solid fa-satellite-dish"></i></span>
                        <h1 class="h3 mb-2">No Active Wi-Fi Zones</h1>
                        <p class="text-secondary mb-0">We could not find an active SCRIPCOM Wi-Fi zone. Please make sure you are connected to the hotspot network and try again.</p>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8 col-xl-7">

                <div class="portal-hero text-center mb-4">
                    @if ($portalLogo)
                        <img src="{{ $portalLogo }}" alt="{{ $portalOrg }} logo" class="portal-hero-logo mb-3">
                    @endif
                    <h1 class="portal-hero-title mb-1">Welcome to {{ $portalOrg }} Wi-Fi</h1>
                    <p class="portal-hero-subtitle mb-3">{{ $portalWelcome }}</p>
                    @if ($portalLocation)
                        <span class="portal-badge"><i class="fa-solid fa-location-dot me-1"></i>{{ $portalLocation }}</span>
                    @endif
                </div>

                <div class="card portal-card portal-options-card">
                    <div class="card-body p-4 p-md-5">
                        <p class="fw-semibold text-body mb-3">Choose how you want to connect:</p>

                        <div class="row g-3">
                            @if ($data['sponsored_package'] && $data['has_sponsored'])
                                <div class="col-12">
                                    <a href="{{ route('portal.watch', $data['hotspot']) }}" class="card card-hover text-decoration-none h-100 border-0">
                                        <div class="card-body d-flex align-items-center gap-3 p-3 p-md-4">
                                            <span class="portal-icon flex-shrink-0"><i class="fa-solid fa-circle-play"></i></span>
                                            <div class="flex-grow-1">
                                                <div class="fw-bold fs-5 text-body">Watch &amp; Get Free Wi-Fi</div>
                                                <div class="text-secondary small">Watch a short sponsor video and connect for free</div>
                                            </div>
                                            <span class="btn portal-btn-primary d-none d-sm-inline-flex">Connect</span>
                                            <i class="fa-solid fa-chevron-right text-secondary d-sm-none"></i>
                                        </div>
                                    </a>
                                </div>
                            @endif

                            @if ($data['featured_paid_package'])
                                <div class="col-12">
                                    <a href="#buy" class="card card-hover text-decoration-none h-100 border-0">
                                        <div class="card-body d-flex align-items-center gap-3 p-3 p-md-4">
                                            <span class="portal-icon flex-shrink-0"><i class="fa-solid fa-cart-shopping"></i></span>
                                            <div class="flex-grow-1">
                                                <div class="fw-bold fs-5 text-body">Buy Wi-Fi</div>
                                                <div class="text-secondary small">{{ $data['featured_paid_package']->durationLabel() }} — {{ $portalCurrency }} {{ number_format((float) $data['featured_paid_package']->price, 2) }}</div>
                                            </div>
                                            <span class="btn portal-btn-primary d-none d-sm-inline-flex">Buy Now</span>
                                            <i class="fa-solid fa-chevron-right text-secondary d-sm-none"></i>
                                        </div>
                                    </a>
                                </div>
                            @endif

                            @if ($data['vouchers_enabled'])
                                <div class="col-12">
                                    <a href="#voucher" class="card card-hover text-decoration-none h-100 border-0">
                                        <div class="card-body d-flex align-items-center gap-3 p-3 p-md-4">
                                            <span class="portal-icon flex-shrink-0"><i class="fa-solid fa-ticket"></i></span>
                                            <div class="flex-grow-1">
                                                <div class="fw-bold fs-5 text-body">I Have a Voucher</div>
                                                <div class="text-secondary small">Redeem a voucher code to connect</div>
                                            </div>
                                            <span class="btn btn-outline-secondary d-none d-sm-inline-flex">Redeem</span>
                                            <i class="fa-solid fa-chevron-right text-secondary d-sm-none"></i>
                                        </div>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                @if ($data['featured_paid_package'])
                    <section class="card portal-card portal-section mt-4" id="buy" aria-labelledby="buy-title">
                        <div class="card-body p-4 p-md-5">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <span class="portal-icon" style="width:2.5rem;height:2.5rem;font-size:1.1rem;"><i class="fa-solid fa-cart-shopping"></i></span>
                                <h2 class="h4 mb-0" id="buy-title">Buy Wi-Fi</h2>
                            </div>

                            <div class="row g-2 mb-3" id="buy-packages">
                                @foreach ($data['paid_packages'] as $index => $package)
                                    <div class="col-12 col-sm-6 col-lg-4">
                                        <label class="card mb-0 package-option">
                                            <input type="radio" name="buy_package_id" value="{{ $package->id }}" class="portal-radio" {{ $index === 0 ? 'checked' : '' }}>
                                            <div class="card-body text-center p-3">
                                                <div class="fw-bold fs-4 text-body">{{ $portalCurrency }} {{ number_format((float) $package->price, 2) }}</div>
                                                <div class="text-secondary small">{{ $package->durationLabel() }}</div>
                                            </div>
                                        </label>
                                    </div>
                                @endforeach
                            </div>

                            <div class="row g-3">
                                <div class="col-12 col-md-8">
                                    <label class="form-label" for="buy-phone">M-Pesa number</label>
                                    <input type="tel" id="buy-phone" class="form-control portal-field" placeholder="0712345678" inputmode="numeric" maxlength="13" autocomplete="tel" pattern="^(\+?254|0)?[17]\d{8}$" aria-describedby="buy-phone-help">
                                    <div class="form-text" id="buy-phone-help">Enter the Safaricom number that will pay for the session. You will receive an STK push prompt.</div>
                                </div>
                                <div class="col-12 col-md-4 d-flex align-items-end">
                                    <button type="button" class="btn portal-btn-primary w-100" id="buy-submit" disabled>
                                        <i class="fa-solid fa-money-bill-wave me-1"></i>Pay Now
                                    </button>
                                </div>
                            </div>

                            <div class="alert alert-danger d-none mt-3" id="buy-error" role="alert"></div>

                            <div class="text-center mt-4 d-none" id="buy-waiting" role="status" aria-live="polite">
                                <div class="spinner-border text-primary mb-2" role="status" aria-hidden="true"></div>
                                <div class="fw-semibold">Check your phone and enter your M-Pesa PIN...</div>
                                <div class="text-secondary small">This may take up to a minute.</div>
                            </div>
                        </div>
                    </section>
                @endif

                @if ($data['vouchers_enabled'])
                    <section class="card portal-card portal-section mt-4" id="voucher" aria-labelledby="voucher-title">
                        <div class="card-body p-4 p-md-5">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <span class="portal-icon" style="width:2.5rem;height:2.5rem;font-size:1.1rem;"><i class="fa-solid fa-ticket"></i></span>
                                <h2 class="h4 mb-0" id="voucher-title">Redeem a Voucher</h2>
                            </div>

                            <form id="voucher-form" class="row g-3">
                                <div class="col-12 col-md-7">
                                    <label class="form-label" for="voucher-code">Voucher code</label>
                                    <input type="text" id="voucher-code" class="form-control portal-field text-uppercase" placeholder="Enter voucher code" maxlength="50" autocomplete="off" spellcheck="false" autocapitalize="characters">
                                </div>
                                <div class="col-12 col-md-5 d-flex align-items-end">
                                    <button type="submit" class="btn portal-btn-primary w-100" id="voucher-submit">
                                        <span class="voucher-spinner spinner-border spinner-border-sm d-none me-1" aria-hidden="true"></span>
                                        <span class="voucher-label"><i class="fa-solid fa-check me-1"></i>Redeem</span>
                                    </button>
                                </div>
                                <div class="col-12">
                                    <div class="alert d-none mb-0" id="voucher-feedback" role="alert" aria-live="polite"></div>
                                </div>
                            </form>
                        </div>
                    </section>
                @endif

            </div>
        </div>
    @endif
@endsection

@push('styles')
    <style>
        :root {
            color-scheme: light;
        }

        html {
            scroll-behavior: smooth;
        }

        .portal-hero-logo {
            height: 3.25rem;
            width: auto;
            object-fit: contain;
        }

        .portal-hero-title {
            font-weight: 800;
            letter-spacing: -0.02em;
        }

        .portal-hero-subtitle {
            color: var(--tblr-secondary-color);
            font-size: 1.05rem;
            max-width: 32rem;
            margin-inline: auto;
        }

        .portal-section {
            scroll-margin-top: 1rem;
        }

        .package-option {
            position: relative;
            cursor: pointer;
            border: 1px solid var(--tblr-border-color);
            border-radius: 1rem;
            height: 100%;
            transition: border-color .2s ease, box-shadow .2s ease;
        }

        .portal-radio {
            position: absolute;
            width: 1px;
            height: 1px;
            opacity: 0;
            overflow: hidden;
        }

        .package-option:hover {
            border-color: color-mix(in srgb, var(--portal-primary) 45%, transparent);
        }

        .package-option:has(input:checked) {
            border-color: var(--portal-primary);
            box-shadow: 0 0 0 .25rem color-mix(in srgb, var(--portal-primary) 18%, transparent);
        }

        .package-option:has(input:checked) .fw-bold {
            color: var(--portal-primary);
        }

        .package-option:has(input:focus-visible) {
            outline: 2px solid var(--portal-primary);
            outline-offset: 2px;
        }

        .card-hover {
            transition: transform .15s ease, box-shadow .15s ease;
        }

        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 .5rem 1.5rem rgba(0, 0, 0, .08);
        }

        .portal-hero,
        .portal-options-card,
        .portal-section {
            animation: portal-rise .45s ease both;
        }

        .portal-options-card {
            animation-delay: .06s;
        }

        .portal-section {
            animation-delay: .12s;
        }

        @keyframes portal-rise {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: none;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            html {
                scroll-behavior: auto;
            }

            .card-hover,
            .package-option {
                transition: none;
            }

            .card-hover:hover {
                transform: none;
            }

            .portal-hero,
            .portal-options-card,
            .portal-section {
                animation: none;
            }
        }
    </style>
@endpush

@push('scripts')
    @if ($hasHotspot && ($data['featured_paid_package'] || $data['vouchers_enabled']))
        <script>
            (function () {
                'use strict';

                const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
                const hotspotId = {{ $data['hotspot']->id }};

                const phoneInput = document.getElementById('buy-phone');
                const buyButton = document.getElementById('buy-submit');
                const buyError = document.getElementById('buy-error');
                const buyWaiting = document.getElementById('buy-waiting');

                function isValidPhone(value) {
                    return /^(\+?254|0)?[17]\d{8}$/.test(value.replace(/\s+/g, ''));
                }

                function updateBuy() {
                    if (phoneInput && buyButton) {
                        buyButton.disabled = !isValidPhone(phoneInput.value);
                    }
                }

                if (phoneInput && buyButton) {
                    phoneInput.addEventListener('input', updateBuy);
                    phoneInput.addEventListener('change', updateBuy);
                    updateBuy();
                }

                function showError(message) {
                    if (!buyError) return;
                    buyError.textContent = message;
                    buyError.classList.remove('d-none');
                }

                function hideError() {
                    if (!buyError) return;
                    buyError.classList.add('d-none');
                }

                function reEnableBuy() {
                    if (!buyButton) return;
                    buyButton.disabled = false;
                    buyButton.removeAttribute('aria-busy');
                }

                buyButton?.addEventListener('click', async function () {
                    const packageId = document.querySelector('input[name="buy_package_id"]:checked')?.value;
                    if (!packageId) return;

                    buyButton.disabled = true;
                    buyButton.setAttribute('aria-busy', 'true');
                    hideError();

                    try {
                        const res = await fetch('{{ route('portal.payment') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrf,
                            },
                            body: JSON.stringify({
                                package_id: packageId,
                                phone: phoneInput.value.replace(/\s+/g, ''),
                                hotspot_id: hotspotId,
                            }),
                        });

                        const data = await res.json();

                        if (!res.ok || !data.success) {
                            showError(data.message || 'Payment could not be initiated.');
                            reEnableBuy();
                            return;
                        }

                        if (data.redirect) {
                            window.location.href = data.redirect;
                            return;
                        }

                        buyWaiting.classList.remove('d-none');

                        const startedAt = Date.now();
                        const timeoutMs = 120000;
                        const timer = setInterval(async function () {
                            try {
                                const status = await fetch('{{ url('/portal/payment') }}/' + data.payment_id, {
                                    headers: { 'Accept': 'application/json' },
                                }).then(r => r.json());

                                if (status.redirect) {
                                    clearInterval(timer);
                                    window.location.href = status.redirect;
                                } else if (status.status === 'failed') {
                                    clearInterval(timer);
                                    buyWaiting.classList.add('d-none');
                                    showError('Payment failed. Please try again.');
                                    reEnableBuy();
                                } else if (Date.now() - startedAt > timeoutMs) {
                                    clearInterval(timer);
                                    buyWaiting.classList.add('d-none');
                                    showError('Payment is taking longer than expected. Please try again.');
                                    reEnableBuy();
                                }
                            } catch (e) {
                                if (Date.now() - startedAt > timeoutMs) {
                                    clearInterval(timer);
                                    buyWaiting.classList.add('d-none');
                                    showError('Payment is taking longer than expected. Please try again.');
                                    reEnableBuy();
                                }
                            }
                        }, 3000);
                    } catch (e) {
                        showError('Something went wrong. Please try again.');
                        reEnableBuy();
                    }
                });

                const voucherForm = document.getElementById('voucher-form');
                const voucherButton = document.getElementById('voucher-submit');
                const voucherLabel = voucherButton?.querySelector('.voucher-label');
                const voucherSpinner = voucherButton?.querySelector('.voucher-spinner');

                voucherForm?.addEventListener('submit', async function (e) {
                    e.preventDefault();
                    const feedback = document.getElementById('voucher-feedback');
                    feedback.classList.add('d-none');

                    const code = document.getElementById('voucher-code').value.trim();
                    if (!code) {
                        feedback.classList.remove('d-none');
                        feedback.classList.add('alert-danger');
                        feedback.textContent = 'Please enter a voucher code.';
                        return;
                    }

                    voucherButton.disabled = true;
                    voucherButton.setAttribute('aria-busy', 'true');
                    voucherSpinner.classList.remove('d-none');
                    voucherLabel.classList.add('d-none');

                    try {
                        const res = await fetch('{{ route('portal.voucher') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrf,
                            },
                            body: JSON.stringify({
                                code,
                                hotspot_id: hotspotId,
                            }),
                        });

                        const data = await res.json();

                        if (data.redirect) {
                            window.location.href = data.redirect;
                            return;
                        }

                        feedback.classList.remove('d-none');
                        feedback.classList.add('alert-danger');
                        feedback.textContent = data.message || 'Invalid voucher code.';
                    } catch (e2) {
                        feedback.classList.remove('d-none');
                        feedback.classList.add('alert-danger');
                        feedback.textContent = 'Something went wrong. Please try again.';
                    } finally {
                        voucherButton.disabled = false;
                        voucherButton.removeAttribute('aria-busy');
                        voucherSpinner.classList.add('d-none');
                        voucherLabel.classList.remove('d-none');
                    }
                });
            })();
        </script>
    @endif
@endpush
