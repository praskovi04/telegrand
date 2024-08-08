<?php

namespace Praskovi04\Telegrand\Concerns;

use Praskovi04\Telegrand\ScopedPayloads\TelegraphPollPayload;
use Praskovi04\Telegrand\ScopedPayloads\TelegraphQuizPayload;

trait CreatesScopedPayloads
{
    public function poll(string $question): TelegraphPollPayload
    {
        $poolPayload = TelegraphPollPayload::makeFrom($this);

        return $poolPayload->poll($question);
    }

    public function quiz(string $question): TelegraphQuizPayload
    {
        $quizPayload = TelegraphQuizPayload::makeFrom($this);

        return $quizPayload->quiz($question);
    }
}
