<?php

use LetMeSendEmail\Client;
use LetMeSendEmail\Laravel\Facades\LetMeSendEmail;
use LetMeSendEmail\Resources\Emails;

it('resolves_letmesendemail_client', function () {
    config([
        'letmesendemail.key' => 'test',
    ]);

    expect(LetMeSendEmail::getFacadeRoot())->toBeInstanceOf(Client::class);
});

it('can_get_an_api_resources', function () {
    config([
        'letmesendemail.key' => 'test',
    ]);

    expect(LetMeSendEmail::emails())->toBeInstanceOf(Emails::class);
});
