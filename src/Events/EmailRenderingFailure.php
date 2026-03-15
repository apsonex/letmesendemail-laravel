<?php

namespace LetMeSendEmail\Laravel\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmailRenderingFailure
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public array $payload
    ) {
        //
    }
}
