<?php

namespace App\Http\Controllers;

use App\Services\TolclinWebhookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TolclinWebhookController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $secret = config('services.tolclin.webhook_secret');

        if ($secret && ! $this->signatureMatches($request, $secret)) {
            return response()->json(['error' => 'Invalid signature'], 403);
        }

        if (! $this->ipAllowed($request)) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        app(TolclinWebhookService::class)->handle($request->getContent());

        return response()->json(['received' => true]);
    }

    private function signatureMatches(Request $request, string $secret): bool
    {
        $body = $request->getContent();

        $signature = $request->header('X-Tolclin-Signature')
            ?? $request->header('X-Signature')
            ?? $request->header('X-Hub-Signature-256')
            ?? $request->input('signature');

        if (is_string($signature) && $signature !== '') {
            return hash_equals(hash_hmac('sha256', $body, $secret), $signature)
                || hash_equals(hash_hmac('sha1', $body, $secret), $signature);
        }

        $bodySecret = $request->input('secret');

        return is_string($bodySecret) && $bodySecret !== '' && hash_equals($secret, $bodySecret);
    }

    private function ipAllowed(Request $request): bool
    {
        $raw = config('services.tolclin.webhook_allowed_ips');

        if (! $raw) {
            return true;
        }

        $allowed = array_values(array_filter(array_map('trim', explode(',', (string) $raw))));

        return in_array($request->ip(), $allowed, true);
    }
}
