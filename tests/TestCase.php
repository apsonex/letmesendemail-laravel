<?php

namespace LetMeSendEmail\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use LetMeSendEmail\Laravel\LetMeSendEmailServiceProvider;

class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            LetMeSendEmailServiceProvider::class,
        ];
    }
}
