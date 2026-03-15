# LetMeSendEmail Laravel SDK

[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](https://opensource.org/licenses/MIT)

Official Laravel integration for [LetMeSendEmail](https://letmesend.email), a modern email API service. This package provides:

- Full Laravel SDK for LetMeSendEmail
- Symfony Mailer transport
- Webhook handling with signature verification
- Facade for convenient API access
- Event dispatching for all email lifecycle events
- Testing utilities with fake responses

For more details about the core API, visit [letmesendemail-php GitHub](https://github.com/apsonex/letmesendemail-php).

---

## Table of Contents

- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
  - [Facade](#facade)
  - [Sending Emails via Symfony Mailer](#sending-emails-via-symfony-mailer)
  - [Testing & Fake Responses](#testing--fake-responses)
- [Webhooks](#webhooks)
  - [Controller Setup](#controller-setup)
  - [Middleware Verification](#middleware-verification)
- [Events](#events)
- [Exceptions](#exceptions)
- [Contributing](#contributing)
- [License](#license)

---

## Installation

Install via Composer:

```bash
composer require letmesendemail/letmesendemail-laravel
```

Laravel will automatically register the service provider via package discovery.

---

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --provider="LetMeSendEmail\Laravel\LetMeSendEmailServiceProvider" --tag="config"
```

Add your API key and webhook secret to `.env`:

```env
LMSE_API_KEY=your_api_key_here
LMSE_WEBHOOK_SECRET=whsec_your_webhook_secret
```

---

## Usage

### Facade

Access LetMeSendEmail API via the facade:

```php
use LetMeSendEmail\Laravel\Facades\LetMeSendEmail;

// Send an email
$response = LetMeSendEmail::emails()->send([
    'from' => 'hello@yourdomain.com',
    'to' => 'recipient@example.com',
    'subject' => 'Hello!',
    'text' => 'This is a test email.',
]);

// Access other resources
$domains = LetMeSendEmail::domains();
$contacts = LetMeSendEmail::contacts();
$categories = LetMeSendEmail::contactCategories();
$tags = LetMeSendEmail::contactTags();
```

Fake responses for testing:

```php
LetMeSendEmail::fake('emails/send');
```

Visit composer vendor directory `vendor/letmesendemail/letmesendemail-php/tests/Fixtures` for available fake responses.

---

### Sending Emails via Symfony Mailer

Configure the transport in `config/mail.php`:

```php
'mailers' => [
    'letmesendemail' => [
        'transport' => 'letmesendemail',
    ],
],
```

Send an email:

```php
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mime\Email;

$email = (new Email())
    ->from('hello@yourdomain.com')
    ->to('recipient@example.com')
    ->subject('Hello')
    ->text('This is a test email')
    ->html('<p>This is a test email</p>');

Mail::mailer('letmesendemail')->send($email);
```

Supports attachments and headers:

```php
$email->attach('File contents', 'filename.txt', 'text/plain');
$email->getHeaders()->addTextHeader('X-Custom-Header', 'value');
```

---

## Webhooks

### Controller Setup

The package provides a webhook controller:

```php
use LetMeSendEmail\Laravel\Http\Controllers\WebhookController;

Route::post('/letmesendemail/webhook', [WebhookController::class, 'handleWebhook']);
```

This controller dispatches events based on the webhook `type`.

---

### Middleware Verification

If you set `webhook.secret` in the config, the `VerifyWebhookSignature` middleware validates the signature automatically:

```php
use LetMeSendEmail\Laravel\Http\Middleware\VerifyWebhookSignature;

Route::post('/letmesendemail/webhook', [WebhookController::class, 'handleWebhook'])
    ->middleware(VerifyWebhookSignature::class);
```

The middleware will reject requests with invalid or missing signatures with a `401 Unauthorized`.

---

## Events

The following events are dispatched for webhooks:

- `EmailSent`
- `EmailDelivered`
- `EmailDeliveryDelayed`
- `EmailComplained`
- `EmailBounced`
- `EmailOpened`
- `EmailClicked`
- `EmailReceived`
- `EmailRejected`
- `EmailFailed`
- `EmailScanFailed`
- `EmailRenderingFailure`

Example usage:

```php
use LetMeSendEmail\Laravel\Events\EmailSent;
use Illuminate\Support\Facades\Event;

Event::listen(EmailSent::class, function ($event) {
    logger()->info('Email sent:', $event->payload);
});
```

---

## Exceptions

- `MissingApiKeyException` — thrown if the API key is not set.
- `WebhookSigningException` — thrown for invalid webhook signature generation.
- `WebhookVerificationException` — thrown for failed webhook verification.

---

## Testing & Fake Responses

You can fake API responses in tests:

```php
LetMeSendEmail::fake('emails/send');
```

Example Pest test:

```php
it('resolves_letmesendemail_client', function () {
    config(['letmesendemail.key' => 'test']);
    expect(LetMeSendEmail::getFacadeRoot())->toBeInstanceOf(\LetMeSendEmail\Client::class);
});
```

---

## Contributing

Contributions are welcome! Please fork the repository, make your changes, and submit a pull request.

---

## License

This package is licensed under the MIT License.
