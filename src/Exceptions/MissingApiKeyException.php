<?php

namespace LetMeSendEmail\Laravel\Exceptions;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class MissingApiKeyException extends AccessDeniedHttpException
{
    public static function throw(): self
    {
        throw new self(
            'LetMeSendEmail invalid API key. Please define active LMSE_API_KEY in your .env file. If you don\'t have one, get your API key at https://letmesend.email.'
        );
    }
}
