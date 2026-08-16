@extends('layouts.portal')

@php
    $portalColor = config('brand.primary_color');
    $portalLogo = asset(config('brand.logo'));
    $portalOrg = config('brand.name');
    $portalLocation = $data['location'] ?? null;
    $portalWelcome = $data['welcome_message'] ?? 'Welcome to free public Wi-Fi.';
    $portalCurrency = $data['currency'] ?? 'KES';
    $hasHotspot = $data !== null;
    $hotspotParam = $data['hotspot'] ? ($data['hotspot']->slug ?? $data['hotspot']->id) : null;
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
                <div class="card portal-card">
                    <div class="card-body p-4 p-md-5">
                        <div class="text-center mb-4">
                            <h1 class="h2 mb-1">Welcome to SCRIPCOM Wi-Fi</h1>
                            <p class="text-secondary mb-2">{{ $portalWelcome }}</p>
                            <span class="portal-badge"><i class="fa-solid fa-location-dot me-1"></i>{{ $portalLocation }}</span>
                        </div>

                        <p class="fw-semibold text-body mb-3">Choose how you want to connect:</p>

                        <div class="row g-3">
                            @if ($data['sponsored_package'] && $data['has_sponsored'])
                                <div class="col-12">
                                    <a href="{{ route('portal.watch', $data['hotspot']) }}" class="card card-hover text-decoration-none h-100 border-0" style="background: color-mix(in srgb, {{ $portalColor }} 6%, white);">
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
                                    <a href="#buy" class="card card-hover text-decoration-none h-100 border-0" onclick="event.preventDefault(); document.getElementById('buy').scrollIntoView({behavior:'smooth'});">
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
                                    <a href="#voucher" class="card card-hover text-decoration-none h-100 border-0" onclick="event.preventDefault(); document.getElementById('voucher').scrollIntoView({behavior:'smooth'});">
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
                    <div class="card portal-card mt-4" id="buy">
                        <div class="card-body p-4 p-md-5">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <span class="portal-icon" style="width:2.5rem;height:2.5rem;font-size:1.1rem;"><i class="fa-solid fa-cart-shopping"></i></span>
                                <h2 class="h4 mb-0">Buy Wi-Fi</h2>
                            </div>

                            <div class="row g-2 mb-3" id="buy-packages">
                                @foreach ($data['paid_packages'] as $index => $package)
                                    <div class="col-12 col-sm-6 col-lg-4">
                                        <label class="card mb-0 package-option {{ $index === 0 ? 'selected' : '' }}">
                                            <input type="radio" name="buy_package_id" value="{{ $package->id }}" class="d-none" {{ $index === 0 ? 'checked' : '' }}>
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
                                    <input type="tel" id="buy-phone" class="form-control portal-field" placeholder="0712345678" inputmode="numeric" maxlength="13">
                                    <div class="form-text">Enter the Safaricom number that will pay for the session. You will receive an STK push prompt.</div>
                                </div>
                                <div class="col-12 col-md-4 d-flex align-items-end">
                                    <button type="button" class="btn portal-btn-primary w-100" id="buy-submit" disabled>
                                        <i class="fa-solid fa-money-bill-wave me-1"></i>Pay Now
                                    </button>
                                </div>
                            </div>

                            <div class="alert alert-danger d-none mt-3" id="buy-error" role="alert"></div>
                            <div class="text-center mt-4 d-none" id="buy-waiting">
                                <div class="spinner-border text-primary mb-2" role="status"></div>
                                <div class="fw-semibold">Check your phone and enter your M-Pesa PIN...</div>
                                <div class="text-secondary small">This may take up to a minute.</div>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($data['vouchers_enabled'])
                    <div class="card portal-card mt-4" id="voucher">
                        <div class="card-body p-4 p-md-5">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <span class="portal-icon" style="width:2.5rem;height:2.5rem;font-size:1.1rem;"><i class="fa-solid fa-ticket"></i></span>
                                <h2 class="h4 mb-0">Redeem a Voucher</h2>
                            </div>

                            <form id="voucher-form" class="row g-3">
                                <div class="col-12 col-md-7">
                                    <label class="form-label" for="voucher-code">Voucher code</label>
                                    <input type="text" id="voucher-code" class="form-control portal-field text-uppercase" placeholder="Enter voucher code" maxlength="50" autocomplete="off">
                                </div>
                                <div class="col-12 col-md-5 d-flex align-items-end">
                                    <button type="submit" class="btn portal-btn-primary w-100">
                                        <i class="fa-solid fa-check me-1"></i>Redeem
                                    </button>
                                </div>
                                <div class="col-12">
                                    <div class="alert d-none mb-0" id="voucher-feedback" role="alert"></div>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    @if ($hasHotspot && ($data['featured_paid_package'] || $data['vouchers_enabled']))
        <script>
            (function () {
                const csrf = document.querySelector('meta[name="csrf-token"]').content;
                const hotspotId = {{ $data['hotspot']->id }};

                document.querySelectorAll('#buy-packages .package-option').forEach(function (el) {
                    el.addEventListener('click', function () {
                        document.querySelectorAll('#buy-packages .package-option').forEach(function (o) { o.classList.remove('selected'); });
                        el.classList.add('selected');
                        const radio = el.querySelector('input');
                        radio.checked = true;
                        updateBuy();
                    });
                });

                const phoneInput = document.getElementById('buy-phone');
                const buyButton = document.getElementById('buy-submit');

                function updateBuy() {
                    const valid = /^(\+?254|0)?[17]\d{8}$/.test(phoneInput.value.replace(/\s+/g, ''));
                    buyButton.disabled = !valid;
                }

                phoneInput.addEventListener('input', updateBuy);
                updateBuy();

                buyButton.addEventListener('click', async function () {
                    const packageId = document.querySelector('input[name="buy_package_id"]:checked').value;
                    buyButton.disabled = true;
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
                            buyButton.disabled = false;
                            return;
                        }

                        if (data.redirect) {
                            window.location.href = data.redirect;
                            return;
                        }

                        document.getElementById('buy-waiting').classList.remove('d-none');

                        const timer = setInterval(async function () {
                            const status = await fetch('{{ url('/portal/payment') }}/' + data.payment_id, {
                                headers: { 'Accept': 'application/json' },
                            }).then(r => r.json());

                            if (status.redirect) {
                                clearInterval(timer);
                                window.location.href = status.redirect;
                            } else if (status.status === 'failed') {
                                clearInterval(timer);
                                document.getElementById('buy-waiting').classList.add('d-none');
                                showError('Payment failed. Please try again.');
                                buyButton.disabled = false;
                            }
                        }, 3000);
                    } catch (e) {
                        showError('Something went wrong. Please try again.');
                        buyButton.disabled = false;
                    }
                });

                const voucherForm = document.getElementById('voucher-form');
                if (voucherForm) {
                    voucherForm.addEventListener('submit', async function (e) {
                        e.preventDefault();
                        const feedback = document.getElementById('voucher-feedback');
                        feedback.classList.add('d-none');

                        try {
                            const res = await fetch('{{ route('portal.voucher') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': csrf,
                                },
                                body: JSON.stringify({
                                    code: document.getElementById('voucher-code').value.trim(),
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
                        }
                    });
                }

                function showError(message) {
                    const el = document.getElementById('buy-error');
                    el.textContent = message;
                    el.classList.remove('d-none');
                }

                function hideError() {
                    document.getElementById('buy-error').classList.add('d-none');
                }
            })();
        </script>

        <style>
            .package-option {
                cursor: pointer;
                border: 1px solid var(--tblr-border-color);
                transition: all .2s ease;
            }

            .package-option.selected {
                border-color: {{ $portalColor }};
                box-shadow: 0 0 0 .25rem color-mix(in srgb, {{ $portalColor }} 18%, transparent);
            }

            .package-option.selected .fw-bold {
                color: {{ $portalColor }};
            }

            .card-hover {
                transition: transform .15s ease, box-shadow .15s ease;
            }

            .card-hover:hover {
                transform: translateY(-2px);
                box-shadow: 0 .5rem 1.5rem rgba(0, 0, 0, .08);
            }
        </style>
    @endif
@endpush
