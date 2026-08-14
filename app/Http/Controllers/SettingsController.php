<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\HasOrganizationScoping;
use App\Models\Setting;
use App\Services\MpesaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    use HasOrganizationScoping;

    public function index(MpesaService $mpesa): View
    {
        $organizationId = $this->organizationId();

        $settings = [
            'portal.default_session_minutes' => Setting::getValue('portal.default_session_minutes', 120, $organizationId),
            'portal.default_bandwidth_mbps' => Setting::getValue('portal.default_bandwidth_mbps', 10, $organizationId),
            'portal.enable_otp' => Setting::getValue('portal.enable_otp', true, $organizationId),
            'portal.enable_vouchers' => Setting::getValue('portal.enable_vouchers', true, $organizationId),
            'portal.welcome_message' => Setting::getValue('portal.welcome_message', 'Welcome to free public Wi-Fi.', $organizationId),
            'sponsorship.unit_price' => Setting::getValue('sponsorship.unit_price', 2, $organizationId),
            'sponsorship.min_purchase' => Setting::getValue('sponsorship.min_purchase', 100, $organizationId),
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
            'portal.default_session_minutes' => ['required', 'integer', 'min:15', 'max:720'],
            'portal.default_bandwidth_mbps' => ['required', 'integer', 'min:1', 'max:1000'],
            'portal.enable_otp' => ['nullable', 'boolean'],
            'portal.enable_vouchers' => ['nullable', 'boolean'],
            'portal.welcome_message' => ['nullable', 'string', 'max:500'],
            'sponsorship.unit_price' => ['required', 'numeric', 'min:0'],
            'sponsorship.min_purchase' => ['required', 'numeric', 'min:1'],
            'notifications.enable_monthly_report' => ['nullable', 'boolean'],
            'notifications.report_recipients' => ['nullable', 'string', 'max:1000'],
            'maintenance.enable' => ['nullable', 'boolean'],
            'maintenance.message' => ['nullable', 'string', 'max:500'],
        ]);

        $organizationId = $this->organizationId();

        foreach ($data as $key => $value) {
            Setting::setValue($key, $value, $organizationId);
        }

        return back()->with('success', 'Settings saved.');
    }
}
