<?php

namespace LetMeSendEmail\Laravel\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmailReceived
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public array $payload
    ) {
        //
    }
}
