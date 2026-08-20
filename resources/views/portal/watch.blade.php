@extends('layouts.portal')

@php
    $portalColor = $data['branding']['primary_color'] ?? '#262B40';
    $portalLogo = $data['branding']['logo'] ?? null;
    $portalOrg = $data['branding']['sponsor_name'] ?? ($data['organization']?->name ?? config('app.name'));
    $portalLocation = $data['location'] ?? null;
    $portalWelcome = $data['welcome_message'] ?? 'Welcome to free public Wi-Fi.';
    $portalCurrency = $data['currency'] ?? 'KES';
    $alreadyWatched = $alreadyWatched ?? false;
@endphp

@section('title', $campaign->title)

@section('content')
    <style>
        .portal-video-container {
            position: relative;
            user-select: none;
            -webkit-user-select: none;
            -webkit-touch-callout: none;
        }
        .portal-video-container video {
            pointer-events: none;
        }
        .portal-ad-overlay {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 5;
        }
        .portal-phone-input {
            border-radius: .75rem;
            padding: .75rem 1rem;
            border: 1px solid var(--tblr-border-color);
            font-size: 1rem;
            width: 100%;
            max-width: 280px;
            text-align: center;
            letter-spacing: .5rem;
        }
        .portal-phone-input:focus {
            border-color: var(--portal-primary);
            box-shadow: 0 0 0 .25rem color-mix(in srgb, var(--portal-primary) 20%, transparent);
            outline: none;
        }
        .portal-otp-input {
            border-radius: .75rem;
            padding: .75rem 1rem;
            border: 1px solid var(--tblr-border-color);
            font-size: 1.25rem;
            font-weight: 700;
            width: 100%;
            max-width: 200px;
            text-align: center;
            letter-spacing: .75rem;
        }
        .portal-otp-input:focus {
            border-color: var(--portal-primary);
            box-shadow: 0 0 0 .25rem color-mix(in srgb, var(--portal-primary) 20%, transparent);
            outline: none;
        }
        .portal-watched-badge {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            background: color-mix(in srgb, #f59e0b 15%, white);
            color: #b45309;
            font-weight: 600;
            font-size: .875rem;
            padding: .75rem 1.25rem;
            border-radius: .75rem;
            border: 1px solid color-mix(in srgb, #f59e0b 25%, white);
        }
        .portal-restrict-notice {
            font-size: .8125rem;
            color: var(--tblr-secondary-color);
            background: color-mix(in srgb, var(--portal-primary) 6%, white);
            padding: .5rem .75rem;
            border-radius: .5rem;
            border-left: 3px solid var(--portal-primary);
        }
        .portal-otp-sent {
            font-size: .8125rem;
            color: var(--tblr-secondary-color);
        }
        .portal-otp-sent strong {
            color: var(--portal-primary);
        }
        .portal-resend-btn {
            font-size: .8125rem;
            padding: .25rem .5rem;
        }
    </style>

    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8 col-xl-7">
            <div class="card portal-card">
                <div class="card-body p-4 p-md-5 text-center">
                    <span class="portal-badge mb-3"><i class="fa-solid fa-location-dot me-1"></i>{{ $portalLocation }}</span>
                    <h1 class="h3 mb-1">Watch to Get Free Wi-Fi</h1>
                    <p class="text-secondary small mb-4">Watch the full {{ $campaign->duration_seconds }}s advertisement to connect. This powers free internet for everyone.</p>

                    @if ($alreadyWatched)
                        <div class="portal-ad mb-4">
                            <div class="text-center p-4">
                                <div class="portal-watched-badge mb-3">
                                    <i class="fa-solid fa-circle-check"></i>
                                    Already Watched Today
                                </div>
                                <div class="fw-bold text-body mt-2">You've already watched this advertisement today.</div>
                                <div class="text-secondary small mt-1">Please come back tomorrow for another free session.</div>
                            </div>
                        </div>
                    @else
                        {{-- Step 1: Phone entry --}}
                        <div id="phone-gate">
                            <div class="portal-ad mb-4">
                                <div class="text-center p-4">
                                    <span class="portal-icon mb-3"><i class="fa-solid fa-mobile-screen-button"></i></span>
                                    <div class="fw-bold fs-5 text-body">Enter your phone number</div>
                                    <div class="text-secondary small mt-1 mb-3">We'll send you a verification code to confirm your number.</div>
                                    <form id="phone-form" class="d-flex flex-column align-items-center gap-3">
                                        <input type="tel" class="portal-phone-input" id="phone-input"
                                               placeholder="0712345678" maxlength="10" required
                                               autocomplete="tel" inputmode="numeric" pattern="[0-9]{10}">
                                        <button type="submit" class="btn portal-btn-primary px-4" id="phone-submit">
                                            <i class="fa-solid fa-paper-plane me-1"></i>Send Code
                                        </button>
                                    </form>
                                    <div id="phone-error" class="text-danger small mt-2 d-none"></div>
                                </div>
                            </div>
                        </div>

                        {{-- Step 2: OTP verification --}}
                        <div id="otp-gate" class="d-none">
                            <div class="portal-ad mb-4">
                                <div class="text-center p-4">
                                    <span class="portal-icon mb-3"><i class="fa-solid fa-shield-halved"></i></span>
                                    <div class="fw-bold fs-5 text-body">Verify your number</div>
                                    <div class="portal-otp-sent mt-1 mb-3">
                                        Code sent to <strong id="otp-phone-display"></strong>
                                    </div>
                                    <form id="otp-form" class="d-flex flex-column align-items-center gap-3">
                                        <input type="text" class="portal-otp-input" id="otp-input"
                                               placeholder="------" maxlength="6" required
                                               inputmode="numeric" pattern="[0-9]{6}" autocomplete="one-time-code">
                                        <button type="submit" class="btn portal-btn-primary px-4" id="otp-submit">
                                            <i class="fa-solid fa-check me-1"></i>Verify &amp; Start
                                        </button>
                                    </form>
                                    <div id="otp-error" class="text-danger small mt-2 d-none"></div>
                                    <div class="mt-3">
                                        <button type="button" class="btn btn-link btn-sm portal-resend-btn" id="resend-btn">
                                            Resend code
                                        </button>
                                        <span id="resend-cooldown" class="text-secondary small d-none"></span>
                                    </div>
                                    <div class="mt-2">
                                        <button type="button" class="btn btn-link btn-sm text-secondary" id="change-phone-btn">
                                            <i class="fa-solid fa-arrow-left me-1"></i>Change number
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Step 3: Video player --}}
                        <div id="video-stage" class="d-none">
                            <div class="portal-ad mb-4 portal-video-container" id="ad-slot">
                                <div class="text-center p-4">
                                    <span class="portal-icon mb-3"><i class="fa-solid fa-circle-play"></i></span>
                                    <div class="fw-bold fs-5 text-body">{{ $campaign->title }}</div>
                                    <div class="text-secondary small mt-1">Advertisement loading...</div>
                                </div>
                                <button type="button" class="portal-play-overlay" id="play-overlay" style="display:none">
                                    <i class="fa-solid fa-circle-play"></i>
                                </button>
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

                            <div class="portal-restrict-notice mt-3">
                                <i class="fa-solid fa-lock me-1"></i>
                                Forwarding and skipping are disabled to ensure fair sponsorship access.
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            @if ($alreadyWatched)
                return;
            @endif

            var csrf = document.querySelector('meta[name="csrf-token"]').content;
            var hotspotId = {{ $data['hotspot']->id }};
            var campaignId = {{ $campaign->id }};
            var totalSeconds = {{ (int) $campaign->duration_seconds }};
            var contentUrl = @json($campaign->content_url);
            var contentType = @json($campaign->content_type);
            var redirectUrl = @json($campaign->redirect_url);

            var sessionId = null;
            var elapsed = 0;
            var completed = false;
            var milestones = [0, 10, 25, 50, 75, 90];
            var videoElement = null;
            var verifiedPhone = null;

            var ring = document.getElementById('timer-ring');
            var timerCount = document.getElementById('timer-count');
            var progressBar = document.getElementById('progress-bar');
            var progressLabel = document.getElementById('progress-label');
            var adSlot = document.getElementById('ad-slot');

            function render() {
                var pct = Math.min(100, Math.round((elapsed / totalSeconds) * 100));
                var remaining = Math.max(0, totalSeconds - elapsed);
                timerCount.textContent = remaining;
                progressBar.style.width = pct + '%';
                progressLabel.textContent = pct + '%';
                ring.style.background = 'conic-gradient(' + window.portalConfig.primaryColor + ' ' + pct + '%, color-mix(in srgb, ' + window.portalConfig.primaryColor + ' 12%, white) ' + pct + '%)';

                while (milestones.length && pct >= milestones[0]) {
                    var m = milestones.shift();
                    postProgress(m);
                }
            }

            function postProgress(progress) {
                if (!sessionId) return;
                fetch('{{ route("portal.video.progress") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                    body: JSON.stringify({ session_id: sessionId, campaign_id: campaignId, progress: progress }),
                }).catch(function () {});
            }

            function complete() {
                if (completed) return;
                completed = true;
                if (videoElement) videoElement.pause();

                fetch('{{ route("portal.video.complete") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                    body: JSON.stringify({ session_id: sessionId, campaign_id: campaignId, duration: Math.max(1, Math.round(elapsed)) }),
                }).then(function (res) { return res.json(); }).then(function (data) {
                    if (data.redirect) {
                        if (redirectUrl) window.open(redirectUrl, '_blank');
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

            function mountAd() {
                var demoVideoUrl = 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4';
                var videoSrc = '';
                if (contentType === 'video' && contentUrl && /\.(mp4|webm|ogg|mov)(\?.*)?$/i.test(contentUrl)) {
                    videoSrc = contentUrl;
                }

                var video = document.createElement('video');
                video.className = 'portal-video';
                video.src = videoSrc || demoVideoUrl;
                video.controls = false;
                video.autoplay = true;
                video.muted = true;
                video.playsInline = true;
                video.loop = false;
                video.disablePictureInPicture = true;
                video.disableRemotePlayback = true;
                video.preload = 'auto';
                videoElement = video;

                adSlot.innerHTML = '';
                adSlot.appendChild(video);

                var playOverlay = document.getElementById('play-overlay');
                if (playOverlay) adSlot.appendChild(playOverlay);

                enforceAntiSkip(video);

                video.addEventListener('timeupdate', function () {
                    elapsed = Math.min(Math.floor(video.currentTime), totalSeconds);
                    render();
                    if (elapsed >= totalSeconds) complete();
                });
                video.addEventListener('loadeddata', function () {
                    if (playOverlay) playOverlay.style.display = 'none';
                });
                video.addEventListener('ended', function () {
                    if (elapsed >= totalSeconds) complete();
                });

                var playPromise = video.play();
                if (playPromise !== undefined) {
                    playPromise.then(function () {
                        if (playOverlay) playOverlay.style.display = 'none';
                    }).catch(function () {
                        if (playOverlay) {
                            playOverlay.style.display = 'flex';
                            playOverlay.onclick = function () {
                                video.play().then(function () { playOverlay.style.display = 'none'; }).catch(function () {});
                            };
                        }
                    });
                }
            }

            function enforceAntiSkip(video) {
                video.addEventListener('seeking', function () {
                    if (video.currentTime > elapsed + 1) video.currentTime = elapsed;
                });
                video.addEventListener('ratechange', function () {
                    if (video.playbackRate !== 1) video.playbackRate = 1;
                });
                video.addEventListener('contextmenu', function (e) { e.preventDefault(); });
                video.addEventListener('dragstart', function (e) { e.preventDefault(); });
                video.addEventListener('mousedown', function (e) {
                    if (e.target === video) e.preventDefault();
                });
            }

            function startVideo() {
                document.getElementById('phone-gate').classList.add('d-none');
                document.getElementById('otp-gate').classList.add('d-none');
                document.getElementById('video-stage').classList.remove('d-none');
                mountAd();
                render();
            }

            function postVideoStart() {
                fetch('{{ route("portal.video.start") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                    body: JSON.stringify({ hotspot_id: hotspotId, campaign_id: campaignId, phone: verifiedPhone }),
                }).then(function (res) { return res.json(); }).then(function (data) {
                    if (!data.success) {
                        if (data.already_watched) {
                            document.getElementById('video-stage').innerHTML =
                                '<div class="text-center p-4"><div class="portal-watched-badge mb-3"><i class="fa-solid fa-circle-check"></i> Already Watched Today</div>' +
                                '<div class="fw-bold text-body mt-2">' + (data.message || 'You have already watched this advertisement today.') + '</div>' +
                                '<div class="text-secondary small mt-1">Please come back tomorrow for another free session.</div></div>';
                            document.getElementById('video-stage').classList.remove('d-none');
                        }
                        return;
                    }
                    sessionId = data.session_id;
                    startVideo();
                }).catch(function () {});
            }

            function setupPhoneForm() {
                var phoneForm = document.getElementById('phone-form');
                var phoneInput = document.getElementById('phone-input');
                var phoneError = document.getElementById('phone-error');
                var phoneSubmit = document.getElementById('phone-submit');

                phoneForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                    var phone = phoneInput.value.trim();

                    if (!/^[0-9]{10}$/.test(phone)) {
                        phoneError.textContent = 'Enter a valid 10-digit phone number (e.g. 0712345678).';
                        phoneError.classList.remove('d-none');
                        return;
                    }
                    phoneError.classList.add('d-none');
                    phoneSubmit.disabled = true;
                    phoneSubmit.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i>Sending...';

                    fetch('{{ route("portal.otp.send") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                        body: JSON.stringify({ phone: phone }),
                    }).then(function (res) { return res.json(); }).then(function (data) {
                        if (!data.success) {
                            phoneError.textContent = data.message || 'Could not send code. Please try again.';
                            phoneError.classList.remove('d-none');
                            phoneSubmit.disabled = false;
                            phoneSubmit.innerHTML = '<i class="fa-solid fa-paper-plane me-1"></i>Send Code';
                            return;
                        }

                        verifiedPhone = phone;
                        document.getElementById('otp-phone-display').textContent = phone;
                        document.getElementById('phone-gate').classList.add('d-none');
                        document.getElementById('otp-gate').classList.remove('d-none');
                        document.getElementById('otp-input').focus();
                        startResendCooldown(60);

                        if (data.debug_otp) {
                            document.getElementById('otp-input').value = data.debug_otp;
                        }
                    }).catch(function () {
                        phoneError.textContent = 'Network error. Please try again.';
                        phoneError.classList.remove('d-none');
                        phoneSubmit.disabled = false;
                        phoneSubmit.innerHTML = '<i class="fa-solid fa-paper-plane me-1"></i>Send Code';
                    });
                });
            }

            function setupOtpForm() {
                var otpForm = document.getElementById('otp-form');
                var otpInput = document.getElementById('otp-input');
                var otpError = document.getElementById('otp-error');
                var otpSubmit = document.getElementById('otp-submit');

                otpForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                    var otp = otpInput.value.trim();

                    if (!/^[0-9]{6}$/.test(otp)) {
                        otpError.textContent = 'Enter the 6-digit code.';
                        otpError.classList.remove('d-none');
                        return;
                    }
                    otpError.classList.add('d-none');
                    otpSubmit.disabled = true;
                    otpSubmit.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i>Verifying...';

                    fetch('{{ route("portal.otp.verify") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                        body: JSON.stringify({ phone: verifiedPhone, otp: otp }),
                    }).then(function (res) { return res.json(); }).then(function (data) {
                        if (!data.success) {
                            otpError.textContent = data.message || 'Invalid code. Please try again.';
                            otpError.classList.remove('d-none');
                            otpSubmit.disabled = false;
                            otpSubmit.innerHTML = '<i class="fa-solid fa-check me-1"></i>Verify &amp; Start';
                            return;
                        }

                        document.getElementById('otp-gate').classList.add('d-none');
                        document.getElementById('video-stage').classList.remove('d-none');
                        postVideoStart();
                    }).catch(function () {
                        otpError.textContent = 'Network error. Please try again.';
                        otpError.classList.remove('d-none');
                        otpSubmit.disabled = false;
                        otpSubmit.innerHTML = '<i class="fa-solid fa-check me-1"></i>Verify &amp; Start';
                    });
                });

                document.getElementById('resend-btn').addEventListener('click', function () {
                    var btn = this;
                    btn.classList.add('d-none');

                    fetch('{{ route("portal.otp.send") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                        body: JSON.stringify({ phone: verifiedPhone }),
                    }).then(function (res) { return res.json(); }).then(function (data) {
                        if (data.success) {
                            startResendCooldown(60);
                        } else {
                            document.getElementById('otp-error').textContent = data.message || 'Could not resend code.';
                            document.getElementById('otp-error').classList.remove('d-none');
                            btn.classList.remove('d-none');
                        }
                    }).catch(function () {
                        btn.classList.remove('d-none');
                    });
                });

                document.getElementById('change-phone-btn').addEventListener('click', function () {
                    verifiedPhone = null;
                    document.getElementById('otp-gate').classList.add('d-none');
                    document.getElementById('phone-gate').classList.remove('d-none');
                    document.getElementById('phone-input').value = '';
                    document.getElementById('otp-input').value = '';
                    document.getElementById('phone-submit').disabled = false;
                    document.getElementById('phone-submit').innerHTML = '<i class="fa-solid fa-paper-plane me-1"></i>Send Code';
                });
            }

            function startResendCooldown(seconds) {
                var cooldownEl = document.getElementById('resend-cooldown');
                var resendBtn = document.getElementById('resend-btn');
                resendBtn.classList.add('d-none');
                cooldownEl.classList.remove('d-none');

                var remaining = seconds;
                cooldownEl.textContent = 'Resend in ' + remaining + 's';

                var timer = setInterval(function () {
                    remaining--;
                    if (remaining <= 0) {
                        clearInterval(timer);
                        cooldownEl.classList.add('d-none');
                        resendBtn.classList.remove('d-none');
                    } else {
                        cooldownEl.textContent = 'Resend in ' + remaining + 's';
                    }
                }, 1000);
            }

            setupPhoneForm();
            setupOtpForm();
        })();
    </script>
@endpush
