<?php

namespace LetMeSendEmail\Laravel\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;
use LetMeSendEmail\Laravel\Http\Middleware\VerifyWebhookSignature;
use LetMeSendEmail\Laravel\Events\EmailBounced;
use LetMeSendEmail\Laravel\Events\EmailClicked;
use LetMeSendEmail\Laravel\Events\EmailComplained;
use LetMeSendEmail\Laravel\Events\EmailDelivered;
use LetMeSendEmail\Laravel\Events\EmailDeliveryDelayed;
use LetMeSendEmail\Laravel\Events\EmailFailed;
use LetMeSendEmail\Laravel\Events\EmailOpened;
use LetMeSendEmail\Laravel\Events\EmailReceived;
use LetMeSendEmail\Laravel\Events\EmailRejected;
use LetMeSendEmail\Laravel\Events\EmailRenderingFailure;
use LetMeSendEmail\Laravel\Events\EmailScanFailed;
use LetMeSendEmail\Laravel\Events\EmailSent;

class WebhookController extends Controller
{
    public function __construct()
    {
        if (config('letmesendemail.webhook.secret')) {
            $this->middleware(VerifyWebhookSignature::class);
        }
    }

    public function handleWebhook(Request $request): Response
    {
        $payload = json_decode($request->getContent(), true);

        if (!is_array($payload) || !isset($payload['type'])) {
            return new Response('Invalid payload', 400);
        }

        $event = match ($payload['type']) {
            'email.bounced' => EmailBounced::class,
            'email.clicked' => EmailClicked::class,
            'email.complained' => EmailComplained::class,
            'email.delivered' => EmailDelivered::class,
            'email.delivery_delayed' => EmailDeliveryDelayed::class,
            'email.failed' => EmailFailed::class,
            'email.opened' => EmailOpened::class,
            'email.received' => EmailReceived::class,
            'email.rejected' => EmailRejected::class,
            'email.rendering_failure' => EmailRenderingFailure::class,
            'email.scan_failed' => EmailScanFailed::class,
            'email.sent' => EmailSent::class,
            default => null,
        };

        if (!$event) {
            return new Response();
        }

        $event::dispatch($payload);

        return new Response('Webhook handled', 200);
    }
}
