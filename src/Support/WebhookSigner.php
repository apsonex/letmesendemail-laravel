<?php

namespace LetMeSendEmail\Laravel\Support;

use LetMeSendEmail\Laravel\Exceptions\WebhookSigningException;
use LetMeSendEmail\Laravel\Exceptions\WebhookVerificationException;

/**
 * @source https://github.com/standard-webhooks/standard-webhooks/tree/main/libraries/php
 */
class WebhookSigner
{
    protected const SECRET_PREFIX = "whsec_";

    protected $secret;

    public static int $tolerance = 5 * 60;

    public function __construct($secret)
    {
        if (substr($secret, 0, strlen(static::SECRET_PREFIX)) === static::SECRET_PREFIX) {
            $secret = substr($secret, strlen(static::SECRET_PREFIX));
        }
        $this->secret = base64_decode($secret);
    }

    public static function fromRaw($secret)
    {
        $obj = new self('');
        $obj->secret = $secret;
        return $obj;
    }

    public function verify($payload, $headers)
    {
        if (
            isset($headers['webhook-id'])
            && isset($headers['webhook-timestamp'])
            && isset($headers['webhook-signature'])
        ) {
            $msgId = $headers['webhook-id'];
            $msgTimestamp = $headers['webhook-timestamp'];
            $msgSignature = $headers['webhook-signature'];
        } else {
            WebhookVerificationException::throw("Missing required headers");
        }

        $timestamp = $this->verifyTimestamp($msgTimestamp);

        $signature = $this->sign($msgId, $timestamp, $payload);
        $expectedSignature = explode(',', $signature, 2)[1];

        $passedSignatures = explode(' ', $msgSignature);

        foreach ($passedSignatures as $versionedSignature) {
            $sigParts = explode(',', $versionedSignature, 2);
            if (count($sigParts) !== 2) {
                continue;
            }
            $version = $sigParts[0];
            $passedSignature = $sigParts[1];

            if (strcmp($version, "v1") !== 0) {
                continue;
            }
            if (hash_equals($expectedSignature, $passedSignature)) {
                return json_decode($payload, true);
            }
        }
        WebhookVerificationException::throw("No matching signature found");
    }

    public function sign($msgId, $timestamp, $payload)
    {
        $timestamp = (string) $timestamp;
        $is_positive_integer = $this->isPositiveInteger($timestamp);
        if (!$is_positive_integer) {
            WebhookSigningException::throw("Invalid timestamp");
        }
        $toSign = "{$msgId}.{$timestamp}.{$payload}";
        $hex_hash = hash_hmac('sha256', $toSign, $this->secret);
        $signature = base64_encode(pack('H*', $hex_hash));
        return "v1,{$signature}";
    }

    private function verifyTimestamp($timestampHeader)
    {
        $now = time();

        try {
            $timestamp = intval($timestampHeader, 10);
        } catch (\Exception $e) {
            WebhookVerificationException::throw("Invalid Signature Headers");
        }

        if ($timestamp < ($now - static::$tolerance)) {
            WebhookVerificationException::throw("Message timestamp too old");
        }
        if ($timestamp > ($now + static::$tolerance)) {
            WebhookVerificationException::throw("Message timestamp too new");
        }
        return $timestamp;
    }

    private function isPositiveInteger($v)
    {
        return is_numeric($v) && !is_float($v + 0) && (int) $v == $v && (int) $v > 0;
    }
}
