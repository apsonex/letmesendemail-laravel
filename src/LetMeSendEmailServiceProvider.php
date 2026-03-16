<?php

namespace LetMeSendEmail\Laravel;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use LetMeSendEmail\Contracts\ClientContract;
use LetMeSendEmail\Laravel\Exceptions\MissingApiKeyException;
use LetMeSendEmail\Laravel\Transport\LetMeSendEmailTransportFactory;
use LetMeSendEmail\LetMeSendEmail;

class LetMeSendEmailServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->configure();
        $this->bindLetMeSendEmailClient();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerRoutes();
        $this->registerPublishing();

        Mail::extend('letmesendemail', function (array $config = []) {
            return new LetMeSendEmailTransportFactory(
                client: $this->app['letmesendemail'],
                config: $config['options'] ?? [],
            );
        });
    }

    protected function bindLetMeSendEmailClient(): void
    {
        $this->app->singleton(ClientContract::class, static function (): ClientContract {
            $apiKey        = config('letmesendemail.key') ?? config('services.letmesendemail.key');
            $clientOptions = config('letmesendemail.client.options') ?? config('services.letmesendemail.client.options') ?? [];

            if (! is_string($apiKey)) {
                MissingApiKeyException::throw();
            }

            return LetMeSendEmail::client(
                apiKey: $apiKey,
                clientOptions: $clientOptions,
            );
        });

        $this->app->alias(ClientContract::class, 'letmesendemail');
        $this->app->alias(ClientContract::class, \LetMeSendEmail\Client::class);
    }

    protected function registerRoutes(): void
    {
        Route::group([
            'domain'    => config('letmesendemail.domain', null),
            'namespace' => 'LetMeSendEmail\Laravel\Http\Controllers',
            'prefix'    => config('letmesendemail.route.path'),
            'as'        => 'letmesend.',
        ], function () {
            $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        });
    }

    protected function registerPublishing(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/letmesendemail.php' => $this->app->configPath('letmesendemail.php'),
            ], 'letmesendemail-config');
        }
    }

    protected function configure(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/letmesendemail.php',
            'letmesendemail'
        );
    }

    public function provides(): array
    {
        return [
            \LetMeSendEmail\Client::class,
        ];
    }
}
