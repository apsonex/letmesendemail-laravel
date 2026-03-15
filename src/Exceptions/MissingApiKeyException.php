<?php

namespace LetMeSendEmail\Laravel\Exceptions;

use InvalidArgumentException;

class MissingApiKeyException extends InvalidArgumentException
{
    public static function throw(): self
    {
        throw new self(
            'LetMeSendEmail API key not found. Please define LMSE_API_KEY in your .env file. If you don\'t have one, get your API key at https://letmesend.email.'
        );
    }
}
