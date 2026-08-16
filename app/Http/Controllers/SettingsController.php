<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\HasOrganizationScoping;
use App\Models\Setting;
use App\Services\MpesaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\View\View;

class SettingsController extends Controller
{
    use HasOrganizationScoping;

    public function index(MpesaService $mpesa): View
    {
        $organizationId = $this->organizationId();

        $settings = [
            'general.currency' => Setting::getValue('general.currency', 'KES', $organizationId),
            'general.enable_registration' => Setting::getValue('general.enable_registration', false, $organizationId),
            'portal.default_session_minutes' => Setting::getValue('portal.default_session_minutes', 120, $organizationId),
            'portal.default_bandwidth_mbps' => Setting::getValue('portal.default_bandwidth_mbps', 10, $organizationId),
            'portal.enable_otp' => Setting::getValue('portal.enable_otp', true, $organizationId),
            'portal.enable_vouchers' => Setting::getValue('portal.enable_vouchers', true, $organizationId),
            'portal.welcome_message' => Setting::getValue('portal.welcome_message', 'Welcome to free public Wi-Fi.', $organizationId),
            'sponsorship.unit_price' => Setting::getValue('sponsorship.unit_price', 2, $organizationId),
            'sponsorship.min_purchase' => Setting::getValue('sponsorship.min_purchase', 100, $organizationId),
            'finance.bandwidth_cost_per_gb' => Setting::getValue('finance.bandwidth_cost_per_gb', 50, $organizationId),
            'finance.payment_fee_rate' => Setting::getValue('finance.payment_fee_rate', 1.0, $organizationId),
            'finance.operating_expenses_monthly' => Setting::getValue('finance.operating_expenses_monthly', 0, $organizationId),
            'audit.retention_days' => Setting::getValue('audit.retention_days', 365, $organizationId),
            'notifications.enable_monthly_report' => Setting::getValue('notifications.enable_monthly_report', false, $organizationId),
            'notifications.report_recipients' => Setting::getValue('notifications.report_recipients', '', $organizationId),
            'maintenance.enable' => Setting::getValue('maintenance.enable', false, $organizationId),
            'maintenance.message' => Setting::getValue('maintenance.message', '', $organizationId),
        ];

        return view('settings.index', compact('settings', 'mpesa'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'general.currency' => ['required', 'string', 'size:3'],
            'general.enable_registration' => ['nullable', 'boolean'],
            'portal.default_session_minutes' => ['required', 'integer', 'min:15', 'max:720'],
            'portal.default_bandwidth_mbps' => ['required', 'integer', 'min:1', 'max:1000'],
            'portal.enable_otp' => ['nullable', 'boolean'],
            'portal.enable_vouchers' => ['nullable', 'boolean'],
            'portal.welcome_message' => ['nullable', 'string', 'max:500'],
            'sponsorship.unit_price' => ['required', 'numeric', 'min:0'],
            'sponsorship.min_purchase' => ['required', 'numeric', 'min:1'],
            'finance.bandwidth_cost_per_gb' => ['required', 'numeric', 'min:0'],
            'finance.payment_fee_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'finance.operating_expenses_monthly' => ['required', 'numeric', 'min:0'],
            'audit.retention_days' => ['required', 'integer', 'min:30', 'max:7300'],
            'notifications.enable_monthly_report' => ['nullable', 'boolean'],
            'notifications.report_recipients' => ['nullable', 'string', 'max:1000'],
            'maintenance.enable' => ['nullable', 'boolean'],
            'maintenance.message' => ['nullable', 'string', 'max:500'],
        ]);

        $organizationId = $this->organizationId();

        $data = Arr::dot($data);

        foreach (['general.enable_registration', 'portal.enable_otp', 'portal.enable_vouchers', 'notifications.enable_monthly_report', 'maintenance.enable'] as $key) {
            $data[$key] = $request->boolean($key);
        }

        foreach ($data as $key => $value) {
            Setting::setValue($key, $value, $organizationId);
        }

        return back()->with('success', 'Configuration saved.');
    }
}
