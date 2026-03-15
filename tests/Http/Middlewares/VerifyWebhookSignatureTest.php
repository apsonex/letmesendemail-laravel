<?php

use function Pest\Laravel\postJson;
use Illuminate\Support\Facades\Route;
use LetMeSendEmail\Laravel\Support\WebhookSigner;
use LetMeSendEmail\Laravel\Http\Controllers\WebhookController;

beforeEach(function () {
    config([
        'letmesendemail.webhook.secret' => 'whsec_' . base64_encode('webhook-secret')
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
    $msgId = 'msg_test_123';
    $timestamp = time();
    $signature = $signer->sign($msgId, $timestamp, json_encode($payload));

    return [$payload, $msgId, $timestamp, $signature];
}

it('accepts_a_valid_signed_webhook', function () {
    [$payload, $msgId, $timestamp, $signature] = webhookPayload();

    $response = postJson(
        config('letmesendemail.route.path') . '/webhook',
        $payload,
        [
            'Webhook-Id' => $msgId,
            'Webhook-Timestamp' => $timestamp,
            'Webhook-Signature' => $signature,
            'Content-Type' => 'application/json',
        ],
    );

    $response->assertStatus(200);

    expect(str($signature)->startsWith('v1,'))->toBeTrue();
});

it('rejects_webhook_with_invalid_signature', function () {
    [$payload, $msgId, $timestamp, $signature] = webhookPayload();

    $response = postJson(
        config('letmesendemail.route.path') . '/webhook',
        $payload,
        [
            'Webhook-Id' => $msgId,
            'Webhook-Timestamp' => $timestamp,
            'Webhook-Signature' => 'v1,invalid-signature',
            'Content-Type' => 'application/json',
        ],
    );

    $response->assertStatus(401);
});
