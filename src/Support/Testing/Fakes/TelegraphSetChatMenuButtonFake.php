<?php

/** @noinspection PhpUnhandledExceptionInspection */

/** @noinspection PhpUnused */

/** @noinspection PhpPropertyOnlyWrittenInspection */

namespace Praskovi04\Telegrand\Support\Testing\Fakes;

use Praskovi04\Telegrand\Concerns\FakesRequests;

use Praskovi04\Telegrand\ScopedPayloads\SetChatMenuButtonPayload;
use Praskovi04\Telegrand\Telegrand;

class TelegraphSetChatMenuButtonFake extends SetChatMenuButtonPayload
{
    use FakesRequests;

    /**
     * @param array<string, array<mixed>> $replies
     */
    public function __construct(array $replies = [])
    {
        parent::__construct();
        $this->replies = $replies;
    }

    /**
     * @param array<string, mixed> $options
     */
    public static function assertChangedMenuButton(string $type, array $options = []): void
    {
        self::assertSentData(Telegrand::ENDPOINT_SET_CHAT_MENU_BUTTON, [
            'menu_button' => [
                'type' => $type,
            ] + $options,
        ]);
    }
}
