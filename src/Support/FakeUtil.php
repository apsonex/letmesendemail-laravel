<?php

namespace LetMeSendEmail\Laravel\Support;

use ReflectionClass;
use RuntimeException;
use Illuminate\Support\Facades\File;

class FakeUtil
{
    public static function resolveFakeRequest(string $chain): array
    {
        $path = static::resolveFakeDataPath($chain . '.json');

        if (!File::exists($path)) {
            throw new RuntimeException(
                'Fake response doesn\'t exist at `' . $path . '`'
            );
        }

        return File::json($path)['request'];
    }

    public static function resolveFakeResponse(string $chain): array
    {
        $path = static::resolveFakeDataPath($chain . '.json');

        if (!File::exists($path)) {
            throw new RuntimeException(
                'Fake response doesn\'t exist at `' . $path . '`'
            );
        }

        return File::json($path)['response'];
    }

    public static function resolveFakeDataPath(?string $path = null): string
    {
        $sdkRoot = dirname((new ReflectionClass(\LetMeSendEmail\Client::class))->getFileName(), 2);

        return $sdkRoot . '/tests/Fixtures' . (!!$path ? '/' . $path : '');
    }
}
