<?php

use Praskovi04\Telegrand\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

if ($webhookUrl = config('telegraph.webhook_url', config('telegraph.webhook.url', '/telegraph/{token}/webhook'))) {

    Route::post($webhookUrl, [WebhookController::class, 'handle'])
        ->middleware(config('telegraph.webhook.middleware', []))
        ->name('telegraph.webhook');

}

