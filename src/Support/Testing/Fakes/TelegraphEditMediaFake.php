<?php

/** @noinspection PhpUnhandledExceptionInspection */

/** @noinspection PhpUnused */

/** @noinspection PhpPropertyOnlyWrittenInspection */

namespace Praskovi04\Telegrand\Support\Testing\Fakes;

use Praskovi04\Telegrand\Concerns\FakesRequests;

use Praskovi04\Telegrand\ScopedPayloads\TelegrandEditMediaPayload;
use Praskovi04\Telegrand\Telegrand;

class TelegraphEditMediaFake extends TelegrandEditMediaPayload
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

    public static function assertSentEditMedia(string $type, string $media): void
    {
        self::assertSentData(Telegrand::ENDPOINT_EDIT_MEDIA, [
            "media" => json_encode([
                'type' => $type,
                'media' => $media,
            ]),
        ], false);
    }
}
