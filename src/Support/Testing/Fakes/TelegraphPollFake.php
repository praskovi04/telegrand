<?php

/** @noinspection PhpUnhandledExceptionInspection */

/** @noinspection PhpUnused */

/** @noinspection PhpPropertyOnlyWrittenInspection */

namespace Praskovi04\Telegrand\Support\Testing\Fakes;

use Praskovi04\Telegrand\Concerns\FakesRequests;

use Praskovi04\Telegrand\ScopedPayloads\TelegrandPollPayload;
use Praskovi04\Telegrand\Telegrand;

class TelegraphPollFake extends TelegrandPollPayload
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
     * @param array<int, string> $options
     */
    public static function assertSentPoll(string $question, array $options = []): void
    {
        if (empty($options)) {
            self::assertSentData(Telegrand::ENDPOINT_SEND_POLL, [
                'question' => $question,
            ], false);

            return;
        }

        self::assertSentData(Telegrand::ENDPOINT_SEND_POLL, [
            'question' => $question,
            'options' => ['bar!', 'baz!'],
        ]);
    }
}
