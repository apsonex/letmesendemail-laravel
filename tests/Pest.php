<?php

use Illuminate\Http\Request;
use LetMeSendEmail\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

function webhookRequest(string $event): Request
{
    return Request::create('/', 'POST', [], [], [], [], json_encode([
        'id' => 'evt_123456789',
        'type' => $event,
    ]));
}

function basePath(string $path)
{
    return realpath(__DIR__ . '/../') . ($path ? '/' . $path : '');
}
