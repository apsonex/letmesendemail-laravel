<?php

namespace LetMeSendEmail\Laravel\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use LetMeSendEmail\Laravel\Support\WebhookSigner;
use LetMeSendEmail\Laravel\Exceptions\WebhookVerificationException;

class VerifyWebhookSignature
{
    public function handle(Request $request, Closure $next): Response
    {
        $secret = config('letmesendemail.webhook.secret');

        if (! $secret) {
            return $next($request);
        }

        $payload = $request->getContent();

        $headers = [
            'webhook-id' => $request->header('Webhook-Id'),
            'webhook-timestamp' => $request->header('Webhook-Timestamp'),
            'webhook-signature' => $request->header('Webhook-Signature'),
        ];

        try {
            WebhookSigner::$tolerance =  (int) config('letmesendemail.webhook.tolerance', 300);
            (new WebhookSigner($secret))->verify($payload, $headers);
        } catch (WebhookVerificationException $e) {
            return new Response($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
