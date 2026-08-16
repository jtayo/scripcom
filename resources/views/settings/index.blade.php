@extends('layouts.admin')

@section('title', 'System Configuration')
@section('page-title', 'System Configuration')
@section('page-subtitle', 'Portal, sponsorship, finance, audit and M-Pesa settings')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-xl-9">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <span class="avatar avatar-sm bg-primary-lt text-primary me-2">
                            <i class="fa-solid fa-gear"></i>
                        </span>
                        <div>
                            <h2 class="h5 mb-0">System Configuration</h2>
                            <div class="small text-muted">Manage platform-wide settings</div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs nav-fill mb-4" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" data-bs-toggle="tab" href="#tab-general" role="tab">
                                <i class="fa-solid fa-globe me-1"></i>General
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab-portal" role="tab">
                                <i class="fa-solid fa-wifi me-1"></i>Portal
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab-finance" role="tab">
                                <i class="fa-solid fa-coins me-1"></i>Finance
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab-sponsorship" role="tab">
                                <i class="fa-solid fa-handshake me-1"></i>Sponsorship
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab-notifications" role="tab">
                                <i class="fa-solid fa-bell me-1"></i>Notifications
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab-maintenance" role="tab">
                                <i class="fa-solid fa-triangle-exclamation me-1"></i>Maintenance
                            </a>
                        </li>
                    </ul>

                    <form method="POST" action="{{ route('admin.settings.update') }}">
                        @csrf

                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="tab-general" role="tabpanel">
                                <div class="row g-3">
                                    <div class="col-12 col-md-6">
                                        <label class="form-label" for="general.currency">Currency <span class="text-danger">*</span></label>
                                        <div class="input-icon">
                                            <span class="input-icon-addon"><i class="fa-solid fa-money-bill"></i></span>
                                            <input type="text" id="general.currency" name="general[currency]" class="form-control @error('general.currency') is-invalid @enderror" value="{{ old('general.currency', $settings['general.currency']) }}" maxlength="3" required>
                                            @error('general.currency') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="form-text">ISO 4217 code used across billing and revenue reports.</div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-check form-switch mt-4">
                                            <input class="form-check-input" type="checkbox" name="general[enable_registration]" id="general.enable_registration" value="1" @checked(old('general.enable_registration', $settings['general.enable_registration']))>
                                            <label class="form-check-label" for="general.enable_registration">Allow Admin Registration</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="tab-portal" role="tabpanel">
                                <div class="row g-3">
                                    <div class="col-12 col-md-6">
                                        <label class="form-label" for="portal.default_session_minutes">Default Session (minutes) <span class="text-danger">*</span></label>
                                        <div class="input-icon">
                                            <span class="input-icon-addon"><i class="fa-solid fa-clock"></i></span>
                                            <input type="number" id="portal.default_session_minutes" name="portal[default_session_minutes]" class="form-control @error('portal.default_session_minutes') is-invalid @enderror" value="{{ old('portal.default_session_minutes', $settings['portal.default_session_minutes']) }}" min="15" max="720" required>
                                            @error('portal.default_session_minutes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label" for="portal.default_bandwidth_mbps">Default Bandwidth (Mbps) <span class="text-danger">*</span></label>
                                        <div class="input-icon">
                                            <span class="input-icon-addon"><i class="fa-solid fa-gauge-high"></i></span>
                                            <input type="number" id="portal.default_bandwidth_mbps" name="portal[default_bandwidth_mbps]" class="form-control @error('portal.default_bandwidth_mbps') is-invalid @enderror" value="{{ old('portal.default_bandwidth_mbps', $settings['portal.default_bandwidth_mbps']) }}" min="1" max="1000" required>
                                            @error('portal.default_bandwidth_mbps') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-check form-switch mt-1">
                                            <input class="form-check-input" type="checkbox" name="portal[enable_otp]" id="portal.enable_otp" value="1" @checked(old('portal.enable_otp', $settings['portal.enable_otp']))>
                                            <label class="form-check-label" for="portal.enable_otp">Enable OTP Login</label>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-check form-switch mt-1">
                                            <input class="form-check-input" type="checkbox" name="portal[enable_vouchers]" id="portal.enable_vouchers" value="1" @checked(old('portal.enable_vouchers', $settings['portal.enable_vouchers']))>
                                            <label class="form-check-label" for="portal.enable_vouchers">Enable Vouchers</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label" for="portal.welcome_message">Welcome Message</label>
                                        <div class="input-icon">
                                            <span class="input-icon-addon"><i class="fa-solid fa-message"></i></span>
                                            <textarea id="portal.welcome_message" name="portal[welcome_message]" class="form-control @error('portal.welcome_message') is-invalid @enderror" rows="2" maxlength="500" style="padding-left: 2.5rem;">{{ old('portal.welcome_message', $settings['portal.welcome_message']) }}</textarea>
                                            @error('portal.welcome_message') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="tab-finance" role="tabpanel">
                                <p class="text-muted small">These figures power the Revenue Management dashboard (gross margin, bandwidth cost and EBITDA).</p>
                                <div class="row g-3">
                                    <div class="col-12 col-md-4">
                                        <label class="form-label" for="finance.bandwidth_cost_per_gb">Bandwidth Cost (KES/GB) <span class="text-danger">*</span></label>
                                        <div class="input-icon">
                                            <span class="input-icon-addon"><i class="fa-solid fa-tower-broadcast"></i></span>
                                            <input type="number" id="finance.bandwidth_cost_per_gb" name="finance[bandwidth_cost_per_gb]" class="form-control @error('finance.bandwidth_cost_per_gb') is-invalid @enderror" value="{{ old('finance.bandwidth_cost_per_gb', $settings['finance.bandwidth_cost_per_gb']) }}" min="0" step="0.01" required>
                                            @error('finance.bandwidth_cost_per_gb') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label class="form-label" for="finance.payment_fee_rate">Payment Fee Rate (%) <span class="text-danger">*</span></label>
                                        <div class="input-icon">
                                            <span class="input-icon-addon"><i class="fa-solid fa-percent"></i></span>
                                            <input type="number" id="finance.payment_fee_rate" name="finance[payment_fee_rate]" class="form-control @error('finance.payment_fee_rate') is-invalid @enderror" value="{{ old('finance.payment_fee_rate', $settings['finance.payment_fee_rate']) }}" min="0" max="100" step="0.01" required>
                                            @error('finance.payment_fee_rate') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="form-text">Applies to M-Pesa revenue entries (payment_fee = gross × rate).</div>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label class="form-label" for="finance.operating_expenses_monthly">Monthly Operating Expenses (KES) <span class="text-danger">*</span></label>
                                        <div class="input-icon">
                                            <span class="input-icon-addon"><i class="fa-solid fa-file-invoice-dollar"></i></span>
                                            <input type="number" id="finance.operating_expenses_monthly" name="finance[operating_expenses_monthly]" class="form-control @error('finance.operating_expenses_monthly') is-invalid @enderror" value="{{ old('finance.operating_expenses_monthly', $settings['finance.operating_expenses_monthly']) }}" min="0" step="0.01" required>
                                            @error('finance.operating_expenses_monthly') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="text-uppercase small fw-bold text-secondary mt-4">Audit Trail</div>
                                <hr class="mt-1 mb-3">
                                <div class="row g-3">
                                    <div class="col-12 col-md-6">
                                        <label class="form-label" for="audit.retention_days">Audit Log Retention (days) <span class="text-danger">*</span></label>
                                        <div class="input-icon">
                                            <span class="input-icon-addon"><i class="fa-solid fa-shield-halved"></i></span>
                                            <input type="number" id="audit.retention_days" name="audit[retention_days]" class="form-control @error('audit.retention_days') is-invalid @enderror" value="{{ old('audit.retention_days', $settings['audit.retention_days']) }}" min="30" max="7300" required>
                                            @error('audit.retention_days') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="form-text">Entries older than this are pruned nightly by the scheduler.</div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="tab-sponsorship" role="tabpanel">
                                <div class="row g-3">
                                    <div class="col-12 col-md-6">
                                        <label class="form-label" for="sponsorship.unit_price">Unit Price (KES/session) <span class="text-danger">*</span></label>
                                        <div class="input-icon">
                                            <span class="input-icon-addon"><i class="fa-solid fa-coins"></i></span>
                                            <input type="number" id="sponsorship.unit_price" name="sponsorship[unit_price]" class="form-control @error('sponsorship.unit_price') is-invalid @enderror" value="{{ old('sponsorship.unit_price', $settings['sponsorship.unit_price']) }}" min="0" step="0.01" required>
                                            @error('sponsorship.unit_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label" for="sponsorship.min_purchase">Minimum Purchase (KES) <span class="text-danger">*</span></label>
                                        <div class="input-icon">
                                            <span class="input-icon-addon"><i class="fa-solid fa-money-bill"></i></span>
                                            <input type="number" id="sponsorship.min_purchase" name="sponsorship[min_purchase]" class="form-control @error('sponsorship.min_purchase') is-invalid @enderror" value="{{ old('sponsorship.min_purchase', $settings['sponsorship.min_purchase']) }}" min="1" step="0.01" required>
                                            @error('sponsorship.min_purchase') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="tab-notifications" role="tabpanel">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="notifications[enable_monthly_report]" id="notifications.enable_monthly_report" value="1" @checked(old('notifications.enable_monthly_report', $settings['notifications.enable_monthly_report']))>
                                            <label class="form-check-label" for="notifications.enable_monthly_report">Enable Monthly Report</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label" for="notifications.report_recipients">Report Recipients</label>
                                        <div class="input-icon">
                                            <span class="input-icon-addon"><i class="fa-solid fa-envelope"></i></span>
                                            <input type="text" id="notifications.report_recipients" name="notifications[report_recipients]" class="form-control @error('notifications.report_recipients') is-invalid @enderror" value="{{ old('notifications.report_recipients', $settings['notifications.report_recipients']) }}" placeholder="admin@example.com, other@example.com" maxlength="1000">
                                            @error('notifications.report_recipients') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="form-text">Comma-separated email addresses.</div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="tab-maintenance" role="tabpanel">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="maintenance[enable]" id="maintenance.enable" value="1" @checked(old('maintenance.enable', $settings['maintenance.enable']))>
                                            <label class="form-check-label" for="maintenance.enable">Maintenance Mode</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label" for="maintenance.message">Maintenance Message</label>
                                        <div class="input-icon">
                                            <span class="input-icon-addon"><i class="fa-solid fa-triangle-exclamation"></i></span>
                                            <textarea id="maintenance.message" name="maintenance[message]" class="form-control @error('maintenance.message') is-invalid @enderror" rows="2" maxlength="500" style="padding-left: 2.5rem;">{{ old('maintenance.message', $settings['maintenance.message']) }}</textarea>
                                            @error('maintenance.message') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                </div>
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
                                <i class="fa-solid fa-floppy-disk me-2"></i>Save Configuration
                            </button>
                            @endcan
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
