<?php

namespace LetMeSendEmail\Laravel\Exceptions;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class WebhookSigningException extends AccessDeniedHttpException
{
    public static function throw(?string $message = null): self
    {
        throw new self($message ?: "Access denied.");
    }
}
