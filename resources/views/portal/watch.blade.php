@extends('layouts.portal')

@php
    $portalColor = $data['branding']['primary_color'] ?? '#262B40';
    $portalLogo = $data['branding']['logo'] ?? null;
    $portalOrg = $data['branding']['sponsor_name'] ?? ($data['organization']?->name ?? config('app.name'));
    $portalLocation = $data['location'] ?? null;
    $portalWelcome = $data['welcome_message'] ?? 'Welcome to free public Wi-Fi.';
    $portalCurrency = $data['currency'] ?? 'KES';
@endphp

@section('title', $campaign->title)

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8 col-xl-7">
            <div class="card portal-card">
                <div class="card-body p-4 p-md-5 text-center">
                    <span class="portal-badge mb-3"><i class="fa-solid fa-location-dot me-1"></i>{{ $portalLocation }}</span>
                    <h1 class="h3 mb-1">Watch to Get Free Wi-Fi</h1>
                    <p class="text-secondary small mb-4">Watch the full {{ $campaign->duration_seconds }}s advertisement to connect. This powers free internet for everyone.</p>

                    <div class="portal-ad mb-4" id="ad-slot">
                        <div class="text-center p-4">
                            <span class="portal-icon mb-3"><i class="fa-solid fa-circle-play"></i></span>
                            <div class="fw-bold fs-5 text-body">{{ $campaign->title }}</div>
                            <div class="text-secondary small mt-1">Advertisement loading...</div>
                        </div>
                    </div>

                    <div class="portal-timer-ring mb-4 mx-auto" id="timer-ring">
                        <div class="timer-content">
                            <div class="portal-timer" id="timer-count">{{ (int) $campaign->duration_seconds }}</div>
                            <div class="text-secondary small">seconds</div>
                        </div>
                    </div>

                    <div class="portal-progress mb-3">
                        <div class="portal-progress-bar" id="progress-bar" style="width:0%"></div>
                    </div>
                    <div class="d-flex justify-content-between text-secondary small">
                        <span id="progress-label">0%</span>
                        <span>{{ (int) $campaign->duration_seconds }}s</span>
                    </div>

                    @if ($campaign->skip_allowed)
                        <button type="button" class="btn btn-outline-secondary mt-3 d-none" id="skip-btn">
                            <i class="fa-solid fa-forward me-1"></i>Skip Ad
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            const csrf = document.querySelector('meta[name="csrf-token"]').content;
            const hotspotId = {{ $data['hotspot']->id }};
            const campaignId = {{ $campaign->id }};
            const totalSeconds = {{ (int) $campaign->duration_seconds }};
            const skipAllowed = {{ $campaign->skip_allowed ? 'true' : 'false' }};
            const contentUrl = @json($campaign->content_url);
            const contentType = @json($campaign->content_type);
            const redirectUrl = @json($campaign->redirect_url);

            function deviceHash() {
                let h = localStorage.getItem('scripcom_device_hash');
                if (!h) {
                    h = 'dh-' + Math.random().toString(36).slice(2) + Date.now().toString(36);
                    localStorage.setItem('scripcom_device_hash', h);
                }
                return h;
            }

            const hash = deviceHash();
            let sessionId = null;
            let elapsed = 0;
            let completed = false;
            let lastMilestone = -1;
            const milestones = [0, 10, 25, 50, 75, 90];

            const ring = document.getElementById('timer-ring');
            const timerCount = document.getElementById('timer-count');
            const progressBar = document.getElementById('progress-bar');
            const progressLabel = document.getElementById('progress-label');
            const adSlot = document.getElementById('ad-slot');
            const skipBtn = document.getElementById('skip-btn');

            function render() {
                const pct = Math.min(100, Math.round((elapsed / totalSeconds) * 100));
                const remaining = Math.max(0, totalSeconds - elapsed);
                timerCount.textContent = remaining;
                progressBar.style.width = pct + '%';
                progressLabel.textContent = pct + '%';
                ring.style.background = 'conic-gradient(' + window.portalConfig.primaryColor + ' ' + pct + '%, color-mix(in srgb, ' + window.portalConfig.primaryColor + ' 12%, white) ' + pct + '%)';

                if (pct >= 30 && skipAllowed) {
                    skipBtn.classList.remove('d-none');
                }

                while (milestones.length && pct >= milestones[0]) {
                    const m = milestones.shift();
                    postProgress(m);
                }
            }

            function postProgress(progress) {
                if (!sessionId) return;
                fetch('{{ route('portal.video.progress') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                    },
                    body: JSON.stringify({
                        session_id: sessionId,
                        campaign_id: campaignId,
                        progress: progress,
                        device_hash: hash,
                    }),
                }).catch(() => {});
            }

            function complete() {
                if (completed) return;
                completed = true;
                clearInterval(ticker);

                fetch('{{ route('portal.video.complete') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                    },
                    body: JSON.stringify({
                        session_id: sessionId,
                        campaign_id: campaignId,
                        duration: Math.max(1, Math.round(elapsed)),
                        device_hash: hash,
                    }),
                }).then(function (res) { return res.json(); }).then(function (data) {
                    if (data.redirect) {
                        if (redirectUrl) {
                            window.open(redirectUrl, '_blank');
                        }
                        window.location.href = data.redirect;
                    } else {
                        adSlot.innerHTML = '<div class="text-center p-4"><div class="text-danger">' + (data.message || 'Could not verify the advertisement.') + '</div></div>';
                        completed = false;
                    }
                }).catch(function () {
                    adSlot.innerHTML = '<div class="text-center p-4"><div class="text-danger">Something went wrong. Please reload and try again.</div></div>';
                    completed = false;
                });
            }

            function startTicker() {
                ticker = setInterval(function () {
                    elapsed = Math.min(elapsed + 1, totalSeconds);
                    render();
                    if (elapsed >= totalSeconds) {
                        complete();
                    }
                }, 1000);
            }

            let ticker = null;

            function mountAd() {
                if (contentType === 'video' && contentUrl && /\.(mp4|webm|ogg|mov)(\?.*)?$/i.test(contentUrl)) {
                    const video = document.createElement('video');
                    video.className = 'portal-video';
                    video.src = contentUrl;
                    video.controls = false;
                    video.autoplay = true;
                    video.muted = true;
                    video.playsInline = true;
                    adSlot.innerHTML = '';
                    adSlot.appendChild(video);
                    video.addEventListener('timeupdate', function () {
                        elapsed = Math.min(Math.floor(video.currentTime), totalSeconds);
                        render();
                        if (elapsed >= totalSeconds) complete();
                    });
                    video.play().catch(function () {
                        startTicker();
                    });
                    return;
                }

                if (contentType === 'image' && contentUrl && /\.(jpg|jpeg|png|webp|gif)(\?.*)?$/i.test(contentUrl)) {
                    const img = document.createElement('img');
                    img.src = contentUrl;
                    img.alt = {{ json_encode($campaign->title) }};
                    adSlot.innerHTML = '';
                    adSlot.appendChild(img);
                    startTicker();
                    return;
                }

                adSlot.innerHTML =
                    '<div class="text-center p-4">' +
                    '   <span class="portal-icon mb-3"><i class="fa-solid fa-circle-play"></i></span>' +
                    '   <div class="fw-bold fs-5 text-body">{{ $campaign->title }}</div>' +
                    '   <div class="text-secondary small mt-1">' + (redirectUrl ? redirectUrl.replace(/^https?:\/\//, '') : 'Advertisement') + '</div>' +
                    '</div>';
                startTicker();
            }

            fetch('{{ route('portal.video.start') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                },
                body: JSON.stringify({
                    hotspot_id: hotspotId,
                    campaign_id: campaignId,
                    device_hash: hash,
                }),
            }).then(function (res) { return res.json(); }).then(function (data) {
                if (!data.success) {
                    adSlot.innerHTML = '<div class="text-center p-4"><div class="text-danger">' + (data.message || 'Could not start the advertisement.') + '</div></div>';
                    return;
                }
                sessionId = data.session_id;
                mountAd();
                render();
            }).catch(function () {
                adSlot.innerHTML = '<div class="text-center p-4"><div class="text-danger">Could not start the advertisement. Please reload.</div></div>';
            });

            if (skipBtn) {
                skipBtn.addEventListener('click', complete);
            }
        })();
    </script>
@endpush
