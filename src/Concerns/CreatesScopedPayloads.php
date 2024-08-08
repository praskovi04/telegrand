<?php

namespace Praskovi04\Telegrand\Concerns;

use Praskovi04\Telegrand\ScopedPayloads\TelegrandPollPayload;
use Praskovi04\Telegrand\ScopedPayloads\TelegrandQuizPayload;

trait CreatesScopedPayloads
{
    public function poll(string $question): TelegrandPollPayload
    {
        $poolPayload = TelegrandPollPayload::makeFrom($this);

        return $poolPayload->poll($question);
    }

    public function quiz(string $question): TelegrandQuizPayload
    {
        $quizPayload = TelegrandQuizPayload::makeFrom($this);

        return $quizPayload->quiz($question);
    }
}
