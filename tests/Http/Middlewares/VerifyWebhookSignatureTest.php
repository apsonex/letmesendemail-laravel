<?php

use App\Support\Webhook\WebhookSigner;

use function Pest\Laravel\postJson;

beforeEach(function () {
    config([
        'letmesendemail.webhook.secret' => 'whsec_' . base64_encode('webhook-secret'),
    ]);
});

function webhookPayload()
{
    $secret = config('letmesendemail.webhook.secret');
    $signer = new WebhookSigner($secret);
    $payload = [
        'type' => 'email.sent',
        'data' => [
            'id' => 'email_123',
        ],
    ];
    $webhookId = 'w_123';
    $webhookLogId = 'wl_123';
    $timestamp = time();
    $signature = $signer->sign($webhookId, $webhookLogId, $timestamp, json_encode($payload));

    return [$payload, $webhookId, $webhookLogId, $timestamp, $signature];
}

it('accepts_a_valid_signed_webhook', function () {
    [$payload, $webhookId, $webhookLogId, $timestamp, $signature] = webhookPayload();

    $response = postJson(
        config('letmesendemail.route.path') . '/webhook',
        $payload,
        [
            'Webhook-Id' => $webhookId,
            'Webhook-Log-Id' => $webhookLogId,
            'Webhook-Timestamp' => $timestamp,
            'Webhook-Signature' => $signature,
            'Content-Type' => 'application/json',
        ],
    );

    $response->assertStatus(200);

    expect(str($signature)->startsWith('v1,'))->toBeTrue();
});

it('rejects_webhook_with_invalid_signature', function () {
    [$payload, $webhookId, $webhookLogId, $timestamp, $signature] = webhookPayload();

    $response = postJson(
        config('letmesendemail.route.path') . '/webhook',
        $payload,
        [
            'Webhook-Id' => $webhookId,
            'Webhook-Log-Id' => $webhookLogId,
            'Webhook-Timestamp' => $timestamp,
            'Webhook-Signature' => 'v1,invalid-signature',
            'Content-Type' => 'application/json',
        ],
    );

    $response->assertStatus(401);
});
