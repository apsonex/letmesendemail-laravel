<?php

namespace App\Support\Webhook;

use LetMeSendEmail\Exceptions\WebhookSigningException;

class WebhookSigner
{
    private string $secret;
    private const SECRET_PREFIX = "whsec_";

    public function __construct(string $secret)
    {
        if (substr($secret, 0, strlen(static::SECRET_PREFIX)) === static::SECRET_PREFIX) {
            $secret = substr($secret, strlen(static::SECRET_PREFIX));
        }
        $this->secret = base64_decode($secret);
    }

    public function sign(string $webhookId, string $webhookLogId, int|string $timestamp, string|array $payload)
    {
        $payload = is_array($payload) ? json_encode($payload, true) : $payload;
        $timestamp = (string) $timestamp;
        $is_positive_integer = $this->isPositiveInteger($timestamp);
        if (!$is_positive_integer) {
            WebhookSigningException::throw('Invalid timestamp');
        }
        $toSign = "{$webhookId}.{$webhookLogId}.{$timestamp}.{$payload}";
        $hex_hash = hash_hmac('sha256', $toSign, $this->secret);
        $signature = base64_encode(pack('H*', $hex_hash));
        return "v1,{$signature}";
    }

    private function isPositiveInteger(mixed $v)
    {
        return is_numeric($v) && !is_float($v + 0) && (int) $v == $v && (int) $v > 0;
    }
}
