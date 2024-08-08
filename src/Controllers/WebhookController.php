<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Praskovi04\Telegrand\Controllers;

use Praskovi04\Telegrand\Handlers\WebhookHandler;
use Praskovi04\Telegrand\Models\TelegraphBot;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class WebhookController
{
    public function handle(Request $request, string $token): Response
    {
        /** @var class-string<TelegraphBot> $botModel */
        $botModel = config('telegraph.models.bot');

        /** @var TelegraphBot $bot */
        $bot = $botModel::fromToken($token);

        /** @var class-string $handler */
        $handler = config('telegraph.webhook_handler', config('telegraph.webhook.handler'));

        /** @var WebhookHandler $handler */
        $handler = app($handler);

        $handler->handle($request, $bot);

        return \response()->noContent();
    }
}
