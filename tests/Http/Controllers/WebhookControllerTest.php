<?php

use Illuminate\Support\Facades\Event;
use LetMeSendEmail\Laravel\Events as WebhookEvents;
use LetMeSendEmail\Laravel\Http\Controllers\WebhookController;

it('dispatches_correct_event_for_webhook_type', function (string $eventName, string $event) {
    $request = webhookRequest($eventName);

    Event::fake([$event]);

    $response = app(WebhookController::class)->handleWebhook($request);

    Event::assertDispatched($event, function ($e) use ($request) {
        return $request->getContent() == json_encode($e->payload);
    });

    expect($response->getStatusCode())->toBe(200)
        ->and($response->getContent())->toBe('Webhook handled');
})->with([
    ['email.bounced', WebhookEvents\EmailBounced::class],
    ['email.clicked', WebhookEvents\EmailClicked::class],
    ['email.complained', WebhookEvents\EmailComplained::class],
    ['email.delivered', WebhookEvents\EmailDelivered::class],
    ['email.delivery_delayed', WebhookEvents\EmailDeliveryDelayed::class],
    ['email.failed', WebhookEvents\EmailFailed::class],
    ['email.opened', WebhookEvents\EmailOpened::class],
    ['email.received', WebhookEvents\EmailReceived::class],
    ['email.rejected', WebhookEvents\EmailRejected::class],
    ['email.rendering_failure', WebhookEvents\EmailRenderingFailure::class],
    ['email.scan_failed', WebhookEvents\EmailScanFailed::class],
    ['email.sent', WebhookEvents\EmailSent::class],
]);
