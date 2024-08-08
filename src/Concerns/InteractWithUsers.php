<?php


/** @noinspection PhpUnhandledExceptionInspection */

namespace Praskovi04\Telegrand\Concerns;

use Praskovi04\Telegrand\Telegrand;

/**
 * @mixin Telegrand
 */

trait InteractWithUsers
{
    public function userProfilePhotos(string $userId): Telegrand
    {
        $telegraph = clone $this;

        $telegraph->endpoint = self::ENDPOINT_GET_USER_PROFILE_PHOTOS;
        $telegraph->data['user_id'] = $userId;

        return $telegraph;
    }
}
