@extends('layouts.admin')

@section('title', 'Settings')
@section('page-title', 'Settings')
@section('page-subtitle', 'Configure portal, sponsorship, notifications and M-Pesa')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-xl-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <span class="avatar avatar-sm bg-primary-lt text-primary me-2">
                            <i class="fa-solid fa-gear"></i>
                        </span>
                        <div>
                            <h2 class="h5 mb-0">Settings</h2>
                            <div class="small text-muted">System-wide configuration</div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.settings.update') }}">
                        @csrf

                        <div class="text-uppercase small fw-bold text-secondary">Portal</div>
                        <hr class="mt-1 mb-3">

                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="portal.default_session_minutes">Default Session (minutes) <span class="text-danger">*</span></label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-clock"></i></span>
                                    <input type="number" id="portal.default_session_minutes" name="portal.default_session_minutes" class="form-control @error('portal.default_session_minutes') is-invalid @enderror" value="{{ old('portal.default_session_minutes', $settings['portal.default_session_minutes']) }}" min="15" max="720" required>
                                    @error('portal.default_session_minutes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="portal.default_bandwidth_mbps">Default Bandwidth (Mbps) <span class="text-danger">*</span></label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-gauge-high"></i></span>
                                    <input type="number" id="portal.default_bandwidth_mbps" name="portal.default_bandwidth_mbps" class="form-control @error('portal.default_bandwidth_mbps') is-invalid @enderror" value="{{ old('portal.default_bandwidth_mbps', $settings['portal.default_bandwidth_mbps']) }}" min="1" max="1000" required>
                                    @error('portal.default_bandwidth_mbps') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-check form-switch mt-1">
                                    <input class="form-check-input" type="checkbox" name="portal.enable_otp" id="portal.enable_otp" value="1" @checked(old('portal.enable_otp', $settings['portal.enable_otp']))>
                                    <label class="form-check-label" for="portal.enable_otp">Enable OTP Login</label>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-check form-switch mt-1">
                                    <input class="form-check-input" type="checkbox" name="portal.enable_vouchers" id="portal.enable_vouchers" value="1" @checked(old('portal.enable_vouchers', $settings['portal.enable_vouchers']))>
                                    <label class="form-check-label" for="portal.enable_vouchers">Enable Vouchers</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="portal.welcome_message">Welcome Message</label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-message"></i></span>
                                    <textarea id="portal.welcome_message" name="portal.welcome_message" class="form-control @error('portal.welcome_message') is-invalid @enderror" rows="2" maxlength="500" style="padding-left: 2.5rem;">{{ old('portal.welcome_message', $settings['portal.welcome_message']) }}</textarea>
                                    @error('portal.welcome_message') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="text-uppercase small fw-bold text-secondary mt-4">Maintenance</div>
                        <hr class="mt-1 mb-3">

                        <div class="row g-3">
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="maintenance.enable" id="maintenance.enable" value="1" @checked(old('maintenance.enable', $settings['maintenance.enable']))>
                                    <label class="form-check-label" for="maintenance.enable">Maintenance Mode</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="maintenance.message">Maintenance Message</label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-triangle-exclamation"></i></span>
                                    <textarea id="maintenance.message" name="maintenance.message" class="form-control @error('maintenance.message') is-invalid @enderror" rows="2" maxlength="500" style="padding-left: 2.5rem;">{{ old('maintenance.message', $settings['maintenance.message']) }}</textarea>
                                    @error('maintenance.message') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="text-uppercase small fw-bold text-secondary mt-4">Sponsorship</div>
                        <hr class="mt-1 mb-3">

                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="sponsorship.unit_price">Unit Price (KES/session) <span class="text-danger">*</span></label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-coins"></i></span>
                                    <input type="number" id="sponsorship.unit_price" name="sponsorship.unit_price" class="form-control @error('sponsorship.unit_price') is-invalid @enderror" value="{{ old('sponsorship.unit_price', $settings['sponsorship.unit_price']) }}" min="0" step="0.01" required>
                                    @error('sponsorship.unit_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="sponsorship.min_purchase">Minimum Purchase (KES) <span class="text-danger">*</span></label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-money-bill"></i></span>
                                    <input type="number" id="sponsorship.min_purchase" name="sponsorship.min_purchase" class="form-control @error('sponsorship.min_purchase') is-invalid @enderror" value="{{ old('sponsorship.min_purchase', $settings['sponsorship.min_purchase']) }}" min="1" step="0.01" required>
                                    @error('sponsorship.min_purchase') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="text-uppercase small fw-bold text-secondary mt-4">Notifications</div>
                        <hr class="mt-1 mb-3">

                        <div class="row g-3">
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="notifications.enable_monthly_report" id="notifications.enable_monthly_report" value="1" @checked(old('notifications.enable_monthly_report', $settings['notifications.enable_monthly_report']))>
                                    <label class="form-check-label" for="notifications.enable_monthly_report">Enable Monthly Report</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="notifications.report_recipients">Report Recipients</label>
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="fa-solid fa-envelope"></i></span>
                                    <input type="text" id="notifications.report_recipients" name="notifications.report_recipients" class="form-control @error('notifications.report_recipients') is-invalid @enderror" value="{{ old('notifications.report_recipients', $settings['notifications.report_recipients']) }}" placeholder="admin@example.com, other@example.com" maxlength="1000">
                                    @error('notifications.report_recipients') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="form-text">Comma-separated email addresses.</div>
                            </div>
                        </div>

                        <div class="text-uppercase small fw-bold text-secondary mt-4">M-Pesa</div>
                        <hr class="mt-1 mb-3">

                        <div class="d-flex align-items-center">
                            <span class="badge bg-{{ $mpesa->isConfigured() ? 'success' : 'warning' }} me-2">{{ $mpesa->isConfigured() ? 'Configured' : 'Not Configured' }}</span>
                            <span class="small text-muted">{{ $mpesa->isConfigured() ? 'STK push is live.' : 'STK push will be simulated until credentials are added to services.mpesa in config.' }}</span>
                        </div>

                        <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary d-inline-flex align-items-center">
                                <i class="fa-solid fa-arrow-left me-2"></i>Cancel
                            </a>
                            @can('update-settings')
                            <button type="submit" class="btn btn-primary d-inline-flex align-items-center">
                                <i class="fa-solid fa-floppy-disk me-2"></i>Save Settings
                            </button>
                            @endcan
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
