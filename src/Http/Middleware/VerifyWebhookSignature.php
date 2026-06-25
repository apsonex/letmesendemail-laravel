<?php

namespace LetMeSendEmail\Laravel\Http\Middleware;

use Closure;
use Throwable;
use Illuminate\Http\Request;
use LetMeSendEmail\Support\WebhookSignature;
use Symfony\Component\HttpFoundation\Response;
use LetMeSendEmail\Exceptions\WebhookSigningException;
use LetMeSendEmail\Exceptions\WebhookVerificationException;

class VerifyWebhookSignature
{
    public function handle(Request $request, Closure $next): Response
    {
        $secret = config('letmesendemail.webhook.secret');

        if (! $secret) {
            return $next($request);
        }

        try {
            WebhookSignature::verify(
                payload: $request->getContent(),
                headers: [
                    'webhook-id' => $request->header('Webhook-Id'),
                    'webhook-log-id' => $request->header('Webhook-Log-Id'),
                    'webhook-timestamp' => $request->header('Webhook-Timestamp'),
                    'webhook-signature' => $request->header('Webhook-Signature'),
                ],
                secret: $secret,
                tolerance: (int) config('letmesendemail.webhook.tolerance', 300),
            );
        } catch (WebhookVerificationException | WebhookSigningException $e) {
            return $this->makeResponse(message: $e->getMessage());
        } catch (Throwable $e) {
            return $this->makeResponse(
                message: $e->getMessage(),
                code: $e->getCode() > 0 ? $e->getCode() : Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }

        return $next($request);
    }

    protected function makeResponse(string $message = 'Unauthorized', int $code = 401): Response
    {
        return new Response($message, $code);
    }
}
