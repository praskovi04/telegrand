<?php

/** @noinspection PhpUnhandledExceptionInspection */

/** @noinspection PhpUnused */

/** @noinspection PhpPropertyOnlyWrittenInspection */

namespace Praskovi04\Telegrand\Support\Testing\Fakes;

use Praskovi04\Telegrand\Concerns\FakesRequests;

use Praskovi04\Telegrand\ScopedPayloads\TelegrandQuizPayload;
use Praskovi04\Telegrand\Telegrand;

class TelegraphQuizFake extends TelegrandQuizPayload
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
    public static function assertSentQuiz(string $question, array $options = [], int $correct_index = null): void
    {
        $data = ['question' => $question, 'type' => 'quiz'];

        if (!empty($options)) {
            $data['options'] = $options;
        }

        if ($correct_index !== null) {
            $data['correct_option_id'] = $correct_index;
        }

        self::assertSentData(Telegrand::ENDPOINT_SEND_POLL, $data);
    }
}
