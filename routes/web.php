<?php

use Illuminate\Support\Facades\Route;
use LetMeSendEmail\Laravel\Http\Controllers\WebhookController;

Route::post('webhook', [WebhookController::class, 'handleWebhook']);
