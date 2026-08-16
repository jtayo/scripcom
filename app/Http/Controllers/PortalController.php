<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Hotspot;
use App\Models\Payment;
use App\Models\WifiPackage;
use App\Models\WifiSession;
use App\Services\CaptivePortalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PortalController extends Controller
{
    public function __construct(private readonly CaptivePortalService $portal) {}

    public function welcome(Request $request): View
    {
        $hotspot = $this->portal->detectHotspot($request);

        if ($hotspot) {
            $this->portal->logEvent('portal.opened', null, null, $request, [
                'hotspot_id' => $hotspot->id,
                'mac' => $request->input('mac'),
                'ip' => $request->ip(),
                'link_orig' => $request->input('link-orig'),
            ]);
        }

        $data = $hotspot ? $this->portal->getPortalData($hotspot) : null;

        return view('portal.welcome', compact('data'));
    }

    public function packages(Request $request): JsonResponse
    {
        $hotspot = $this->portal->detectHotspot($request);

        if (! $hotspot) {
            return response()->json(['success' => false, 'message' => 'No active Wi-Fi zone found.']);
        }

        return response()->json([
            'success' => true,
            'data' => $this->portal->packagesFor($hotspot),
        ]);
    }

    public function watch(Hotspot $hotspot, Request $request): View|RedirectResponse
    {
        $campaign = $this->portal->getActiveCampaign($hotspot);

        if (! $campaign) {
            return redirect()->route('portal.welcome', ['hotspot' => $hotspot->slug ?? $hotspot->id])
                ->with('error', 'No sponsored campaign is available at this location right now.');
        }

        $data = $this->portal->getPortalData($hotspot, $campaign);

        return view('portal.watch', compact('data', 'campaign'));
    }

    public function videoStart(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'hotspot_id' => ['required', 'integer', 'exists:hotspots,id'],
            'campaign_id' => ['required', 'integer', 'exists:campaigns,id'],
            'device_hash' => ['nullable', 'string', 'max:128'],
        ]);

        $hotspot = Hotspot::findOrFail($validated['hotspot_id']);
        $campaign = Campaign::findOrFail($validated['campaign_id']);

        if (! $campaign->hotspots()->where('hotspots.id', $hotspot->id)->exists()) {
            return response()->json(['success' => false, 'message' => 'Campaign is not available at this location.'], 422);
        }

        $result = $this->portal->startWatch($hotspot, $campaign, $request);

        return response()->json([
            'success' => true,
            'session_id' => $result['session']->id,
            'campaign' => [
                'id' => $campaign->id,
                'title' => $campaign->title,
                'content_type' => $campaign->content_type,
                'content_url' => $campaign->content_url,
                'thumbnail' => $campaign->thumbnail,
                'redirect_url' => $campaign->redirect_url,
                'duration_seconds' => (int) $campaign->duration_seconds,
                'skip_allowed' => (bool) $campaign->skip_allowed,
            ],
        ]);
    }

    public function videoProgress(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'session_id' => ['required', 'integer', 'exists:wifi_sessions,id'],
            'campaign_id' => ['required', 'integer', 'exists:campaigns,id'],
            'progress' => ['required', 'numeric', 'min:0', 'max:100'],
            'device_hash' => ['nullable', 'string', 'max:128'],
        ]);

        $session = WifiSession::findOrFail($validated['session_id']);
        $campaign = Campaign::findOrFail($validated['campaign_id']);

        $this->portal->logProgress($session, $campaign, $validated['progress'], $request);

        return response()->json(['success' => true]);
    }

    public function videoComplete(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'session_id' => ['required', 'integer', 'exists:wifi_sessions,id'],
            'campaign_id' => ['required', 'integer', 'exists:campaigns,id'],
            'duration' => ['required', 'integer', 'min:0'],
            'device_hash' => ['nullable', 'string', 'max:128'],
        ]);

        $session = WifiSession::findOrFail($validated['session_id']);
        $campaign = Campaign::findOrFail($validated['campaign_id']);

        $result = $this->portal->completeWatch($session, $campaign, (int) $validated['duration'], $request);

        if (! ($result['success'] ?? false)) {
            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Unable to verify advertisement completion.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'session_id' => $result['session']->id,
            'redirect' => route('portal.success', $result['session']),
        ]);
    }

    public function initiatePayment(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'package_id' => ['required', 'integer', 'exists:wifi_packages,id'],
            'phone' => ['required', 'string', 'max:20'],
            'hotspot_id' => ['required', 'integer', 'exists:hotspots,id'],
        ]);

        $package = WifiPackage::findOrFail($validated['package_id']);
        $hotspot = Hotspot::findOrFail($validated['hotspot_id']);

        $result = $this->portal->initiatePaid($package, $validated['phone'], $hotspot, $request);

        if (! ($result['success'] ?? false)) {
            return response()->json(['success' => false, 'message' => $result['message'] ?? 'Payment could not be initiated.'], 422);
        }

        $response = [
            'success' => true,
            'payment_id' => $result['payment']->id,
            'status' => $result['payment']->status,
            'message' => $result['message'] ?? 'Payment initiated.',
        ];

        if (isset($result['session'])) {
            $response['session_id'] = $result['session']->id;
            $response['redirect'] = route('portal.success', $result['session']);
        }

        return response()->json($response);
    }

    public function paymentStatus(Payment $payment, Request $request): JsonResponse
    {
        $session = $payment->session;

        return response()->json([
            'success' => true,
            'payment_id' => $payment->id,
            'status' => $payment->status,
            'session_id' => $session?->id,
            'redirect' => $session ? route('portal.success', $session) : null,
        ]);
    }

    public function redeemVoucher(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50'],
            'hotspot_id' => ['required', 'integer', 'exists:hotspots,id'],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $hotspot = Hotspot::findOrFail($validated['hotspot_id']);

        $result = $this->portal->redeemVoucher(strtoupper($validated['code']), $hotspot, $request);

        if (! ($result['success'] ?? false)) {
            return response()->json(['success' => false, 'message' => $result['message'] ?? 'Invalid voucher code.'], 422);
        }

        return response()->json([
            'success' => true,
            'session_id' => $result['session']->id,
            'redirect' => route('portal.success', $result['session']),
        ]);
    }

    public function success(WifiSession $session): View
    {
        $session->loadMissing(['hotspot.organization', 'package', 'campaign']);

        return view('portal.success', compact('session'));
    }
}
