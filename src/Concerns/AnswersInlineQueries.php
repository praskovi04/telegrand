<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Praskovi04\Telegrand\Concerns;

use Praskovi04\Telegrand\DTO\InlineQueryResult;
use Praskovi04\Telegrand\Exceptions\InlineQueryException;
use Praskovi04\Telegrand\Telegrand;

/**
 * @mixin Telegrand
 */
trait AnswersInlineQueries
{
    /**
     * @param InlineQueryResult[] $results
     */
    public function answerInlineQuery(string $inlineQueryID, array $results): Telegrand
    {
        $telegraph = clone $this;

        $telegraph->endpoint = self::ENDPOINT_ANSWER_INLINE_QUERY;
        $telegraph->data = [
            'inline_query_id' => $inlineQueryID,
            'results' => collect($results)->map(fn (InlineQueryResult $result) => $result->toArray())->toArray(),
        ];

        return $telegraph;
    }

    public function cache(int $seconds): Telegrand
    {
        $telegraph = clone $this;
        $telegraph->data['cache_time'] = $seconds;

        return $telegraph;
    }

    public function personal(): Telegrand
    {
        $telegraph = clone $this;
        $telegraph->data['is_personal'] = true;

        return $telegraph;
    }

    public function nextOffset(string $offset): Telegrand
    {
        $telegraph = clone $this;
        $telegraph->data['next_offset'] = $offset;

        return $telegraph;
    }

    public function offertToSwitchToPrivateMessage(string $text, string $parameter): Telegrand
    {
        if (!preg_match("#^[a-zA-Z\d_-]+$#", $parameter)) {
            throw InlineQueryException::invalidSwitchToPmParameter($parameter);
        }

        $telegraph = clone $this;
        $telegraph->data['switch_pm_text'] = $text;
        $telegraph->data['switch_pm_parameter'] = $parameter;

        return $telegraph;
    }
}
