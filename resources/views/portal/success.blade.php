@extends('layouts.portal')

@php
    $hotspot = $session->hotspot;
    $portalColor = config('brand.primary_color');
    $portalLogo = asset(config('brand.logo'));
    $portalOrg = config('brand.name');
    $portalLocation = collect([$hotspot?->ward, $hotspot?->sub_county, $hotspot?->name])->filter()->implode(', ') ?: $hotspot?->name;
    $sessionSpeedMbps = $session->package && $session->package->bandwidth_down_kbps
        ? number_format($session->package->bandwidth_down_kbps / 1024, 1)
        : number_format(config('services.tolclin.bandwidth_mbps', 10));
@endphp

@section('title', 'You are Connected')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6 col-xl-5">
            <div class="card portal-card">
                <div class="card-body p-4 p-md-5 text-center">
                    @if ($session->isExpired())
                        <span class="portal-icon mb-3" style="color:#d63939;background:#fbd5d5;"><i class="fa-solid fa-triangle-exclamation"></i></span>
                        <h1 class="h3 mb-2">Your session has expired</h1>
                        <p class="text-secondary">Please reconnect to the Wi-Fi network and start a new session.</p>
                        <a href="{{ route('portal.welcome') }}" class="btn portal-btn-primary w-100 mt-2">Connect Again</a>
                    @else
                        <span class="portal-icon mb-3"><i class="fa-solid fa-circle-check"></i></span>
                        <h1 class="h3 mb-1">You are Connected!</h1>
                        <p class="text-secondary mb-4">Enjoy fast, free internet. Please stay connected to the <strong>{{ $hotspot?->name }}</strong> network.</p>

                        <div class="card mb-4" style="background: color-mix(in srgb, {{ $portalColor }} 6%, white);">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center py-1">
                                    <span class="text-secondary">Session ID</span>
                                    <span class="fw-semibold text-body">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($session->session_id, 0, 8)) }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center py-1">
                                    <span class="text-secondary">Package</span>
                                    <span class="fw-semibold text-body">{{ $session->package?->name ?? 'Wi-Fi Session' }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center py-1">
                                    <span class="text-secondary">Speed</span>
                                    <span class="fw-semibold text-body">{{ $sessionSpeedMbps }} Mbps</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center py-1">
                                    <span class="text-secondary">Expires in</span>
                                    <span class="fw-semibold text-body">
                                        <span class="portal-countdown" id="countdown" data-expires="{{ $session->expires_at?->toIso8601String() }}">{{ $session->remainingSeconds() > 0 ? gmdate('H:i:s', $session->remainingSeconds()) : '00:00:00' }}</span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-light border text-start small mb-4">
                            <i class="fa-solid fa-circle-info me-1"></i> Keep this page open or reconnect anytime during the session. Your connection works across the zone.
                        </div>

                        <a href="{{ route('portal.welcome') }}" class="btn btn-outline-secondary w-100">Back to Home</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @if (! $session->isExpired() && $session->expires_at)
        <script>
            (function () {
                const el = document.getElementById('countdown');
                if (!el) return;
                const expires = new Date(el.dataset.expires);

                function tick() {
                    const diff = Math.max(0, Math.floor((expires.getTime() - Date.now()) / 1000));
                    const h = String(Math.floor(diff / 3600)).padStart(2, '0');
                    const m = String(Math.floor((diff % 3600) / 60)).padStart(2, '0');
                    const s = String(diff % 60).padStart(2, '0');
                    el.textContent = h + ':' + m + ':' + s;
                }

                tick();
                setInterval(tick, 1000);
            })();
        </script>
    @endif
@endpush
