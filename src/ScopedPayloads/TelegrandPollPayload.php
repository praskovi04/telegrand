<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Praskovi04\Telegrand\ScopedPayloads;

use Praskovi04\Telegrand\Concerns\BuildsFromTelegraphClass;
use Praskovi04\Telegrand\Concerns\SendsPolls;
use Praskovi04\Telegrand\Telegrand;

class TelegrandPollPayload extends Telegrand
{
    use BuildsFromTelegraphClass;
    use SendsPolls;

    public function poll(string $question): static
    {
        $telegraph = clone $this;

        $telegraph->endpoint = self::ENDPOINT_SEND_POLL;
        $telegraph->data['chat_id'] = $telegraph->getChatId();
        $telegraph->data['options'] = [];
        $telegraph->data['question'] = $question;

        return $telegraph;
    }

    public function allowMultipleAnswers(): static
    {
        $telegraph = clone $this;
        $telegraph->data['allows_multiple_answers'] = true;

        return $telegraph;
    }
}
