<?php

namespace LetMeSendEmail\Laravel\Facades;

use Illuminate\Support\Facades\Facade;
use LetMeSendEmail\Laravel\Support\FakeUtil;
use \LetMeSendEmail\Resources\Email;
use \LetMeSendEmail\Resources\Domain;
use \LetMeSendEmail\Resources\Contact;
use \LetMeSendEmail\Resources\ContactCategory;
use \LetMeSendEmail\Client;

/**
 * Provides LetMeSendEmail integration for Laravel and Symfony Mailer.
 *
 * @see \LetMeSendEmail\Client
 *
 * @method Email emails();
 * @method Domain domains();
 * @method Contact contacts();
 * @method ContactCategory contactCategories();
 * @method ContactTag contactTags();
 * @method Client fake();
 */
class LetMeSendEmail extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'letmesendemail';
    }

    public static function fake(?string $path = null)
    {
        return tap(static::getFacadeRoot(), function ($client) use ($path) {
            return $client->fake(
                $path ? fn() => FakeUtil::resolveFakeResponse($path) : null
            );
        });
    }
}
