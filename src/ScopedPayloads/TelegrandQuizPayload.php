<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Praskovi04\Telegrand\ScopedPayloads;

use Praskovi04\Telegrand\Concerns\BuildsFromTelegraphClass;
use Praskovi04\Telegrand\Concerns\SendsPolls;
use Praskovi04\Telegrand\Exceptions\TelegraphPollException;
use Praskovi04\Telegrand\Telegrand;

class TelegrandQuizPayload extends Telegrand
{
    use BuildsFromTelegraphClass;
    use SendsPolls {
        option as protected _createOption;
    }

    public function quiz(string $question): static
    {
        $telegraph = clone $this;

        $telegraph->endpoint = self::ENDPOINT_SEND_POLL;
        $telegraph->data['chat_id'] = $telegraph->getChatId();
        $telegraph->data['type'] = 'quiz';
        $telegraph->data['options'] = [];
        $telegraph->data['question'] = $question;

        return $telegraph;
    }

    public function option(string $option, bool $correct = false): static
    {
        $telegraph = self::_createOption($option);

        if ($correct) {
            if (isset($telegraph->data['correct_option_id'])) {
                /** @phpstan-ignore-next-line */
                throw TelegraphPollException::onlyOneCorrectAnswerAllowed($telegraph->data['options'][$telegraph->data['correct_option_id']]);
            }

            /** @phpstan-ignore-next-line */
            $telegraph->data['correct_option_id'] = count($telegraph->data['options']) - 1;
        }

        return $telegraph;
    }

    public function explanation(string $text): static
    {
        if (strlen($text) > 200) {
            throw TelegraphPollException::explanationMaxLengthExceeded();
        }

        $telegraph = clone $this;
        $telegraph->data['explanation'] = $text;

        return $telegraph;
    }
}
