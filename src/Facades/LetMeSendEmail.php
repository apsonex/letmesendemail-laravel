<?php

namespace LetMeSendEmail\Laravel\Facades;

use Illuminate\Support\Facades\Facade;
use LetMeSendEmail\Laravel\Support\FakeUtil;
use LetMeSendEmail\Resources\Emails;
use LetMeSendEmail\Resources\Domains;
use LetMeSendEmail\Resources\Contacts;
use LetMeSendEmail\Resources\ContactTags;
use LetMeSendEmail\Resources\ContactCategories;
use LetMeSendEmail\Client;

/**
 * Provides LetMeSendEmail integration for Laravel and Symfony Mailer.
 *
 * @see \LetMeSendEmail\Client
 *
 * @method Emails emails();
 * @method Domains domains();
 * @method Contacts contacts();
 * @method ContactCategories contactCategories();
 * @method ContactTags contactTags();
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
