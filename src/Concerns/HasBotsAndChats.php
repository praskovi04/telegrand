<?php

/** @noinspection DuplicatedCode */

/** @noinspection PhpDocMissingThrowsInspection */

/** @noinspection PhpUnhandledExceptionInspection */

namespace Praskovi04\Telegrand\Concerns;

use Praskovi04\Telegrand\DTO\Attachment;
use Praskovi04\Telegrand\Enums\ChatActions;
use Praskovi04\Telegrand\Enums\ChatAdminPermissions;
use Praskovi04\Telegrand\Exceptions\ChatSettingsException;
use Praskovi04\Telegrand\Exceptions\FileException;
use Praskovi04\Telegrand\Exceptions\TelegraphException;
use Praskovi04\Telegrand\Models\TelegraphBot;
use Praskovi04\Telegrand\Models\TelegraphChat;
use Praskovi04\Telegrand\ScopedPayloads\SetChatMenuButtonPayload;
use Praskovi04\Telegrand\Telegrand;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;

/**
 * @mixin Telegrand
 */
trait HasBotsAndChats
{
    protected TelegraphBot|string|null $bot;

    protected TelegraphChat|string|null $chat;

    public function bot(TelegraphBot|string $bot): Telegrand
    {
        $telegraph = clone $this;

        $telegraph->bot = $bot;

        return $telegraph;
    }

    public function chat(TelegraphChat|string $chat): Telegrand
    {
        $telegraph = clone $this;

        $telegraph->chat = $chat;

        if (empty($telegraph->bot) && $chat instanceof TelegraphChat) {
            $telegraph->bot = $chat->bot;
        }

        return $telegraph;
    }

    protected function getBotIfAvailable(): TelegraphBot|string|null
    {
        $telegraph = clone $this;

        if (empty($telegraph->bot)) {
            /** @var TelegraphBot|string $bot */
            $bot = rescue(fn () => TelegraphBot::query()->with('chats')->sole(), config('telegraph.bot_token'), false);

            $telegraph->bot = $bot;
        }

        return $telegraph->bot;
    }

    protected function getBot(): TelegraphBot|string
    {
        return $this->getBotIfAvailable() ?? throw TelegraphException::missingBot();
    }

    protected function getBotToken(): string
    {
        $bot = $this->getBot();

        if ($bot instanceof TelegraphBot) {
            return $bot->token;
        }

        return $bot;
    }

    protected function getChatIfAvailable(): TelegraphChat|string|null
    {
        $telegraph = clone $this;

        if (empty($telegraph->chat)) {
            $bot = $telegraph->getBotIfAvailable();

            if ($bot instanceof TelegraphBot) {
                $telegraph->chat = rescue(fn () => $bot->chats()->sole(), report: false);
            }
        }

        if (empty($telegraph->chat)) {
            $telegraph->chat = rescue(fn () => TelegraphChat::query()->sole(), report: false);
        }

        return $telegraph->chat ?? null;
    }

    protected function getChat(): TelegraphChat|string
    {
        return $this->getChatIfAvailable() ?? throw TelegraphException::missingChat();
    }

    protected function getChatId(): string
    {
        $chat = $this->getChat();

        if ($chat instanceof TelegraphChat) {
            return $chat->chat_id;
        }

        return $chat;
    }

    public function leaveChat(): Telegrand
    {
        $telegraph = clone $this;

        $telegraph->endpoint = self::ENDPOINT_LEAVE_CHAT;
        $telegraph->data['chat_id'] = $telegraph->getChatId();

        return $telegraph;
    }

    public function botInfo(): Telegrand
    {
        $telegraph = clone $this;

        $telegraph->endpoint = self::ENDPOINT_GET_BOT_INFO;

        return $telegraph;
    }

    /**
     * @param string[]|null $allowedUpdates
     */
    public function botUpdates(int $timeout = null, int $offset = null, int $limit = null, array $allowedUpdates = null): Telegrand
    {
        $telegraph = clone $this;

        $telegraph->endpoint = self::ENDPOINT_GET_BOT_UPDATES;

        if ($offset !== null) {
            $telegraph->data['offset'] = $offset;
        }

        if ($limit !== null) {
            $telegraph->data['limit'] = $limit;
        }

        if ($timeout !== null) {
            $telegraph->data['timeout'] = $timeout;
        }

        if ($allowedUpdates !== null) {
            $telegraph->data['allowed_updates'] = $allowedUpdates;
        }

        return $telegraph;
    }

    public function chatAction(string $action): Telegrand
    {
        $telegraph = clone $this;

        in_array($action, ChatActions::available_actions()) || throw TelegraphException::invalidChatAction($action);

        $telegraph->endpoint = self::ENDPOINT_SEND_CHAT_ACTION;
        $telegraph->data['chat_id'] = $telegraph->getChatId();
        $telegraph->data['action'] = $action;

        return $telegraph;
    }

    public function setTitle(string $title): Telegrand
    {
        $telegraph = clone $this;

        !empty($title) || throw ChatSettingsException::emptyTitle();
        strlen($title) < 256 || throw ChatSettingsException::titleMaxLengthExceeded();

        $telegraph->endpoint = self::ENDPOINT_SET_CHAT_TITLE;
        $telegraph->data['chat_id'] = $telegraph->getChatId();
        $telegraph->data['title'] = $title;

        return $telegraph;
    }

    public function setDescription(string $description): Telegrand
    {
        $telegraph = clone $this;

        strlen($description) < 256 || throw ChatSettingsException::descriptionMaxLengthExceeded();

        $telegraph->endpoint = self::ENDPOINT_SET_CHAT_DESCRIPTION;
        $telegraph->data['chat_id'] = $telegraph->getChatId();
        $telegraph->data['description'] = $description;

        return $telegraph;
    }

    public function setChatPhoto(string $path): Telegrand
    {
        $telegraph = clone $this;

        $telegraph->endpoint = self::ENDPOINT_SET_CHAT_PHOTO;
        $telegraph->data['chat_id'] = $telegraph->getChatId();

        File::exists($path) || throw FileException::fileNotFound('photo', $path);

        $maxSizeInMb = config('telegraph.attachments.photo.max_size_mb', 10);

        assert(is_float($maxSizeInMb));

        if (($size = $telegraph->fileSizeInMb($path)) > $maxSizeInMb) {
            throw FileException::photoSizeExceeded($size, $maxSizeInMb);
        }

        $height = $telegraph->imageHeight($path);
        $width = $telegraph->imageWidth($path);

        $height_width_sum_px = config('telegraph.attachments.photo.height_width_sum_px', 10000);

        assert(is_integer($height_width_sum_px));

        if (($totalLength = $height + $width) > $height_width_sum_px) {
            throw FileException::invalidPhotoSize($totalLength, $height_width_sum_px);
        }

        $maxRatio = config('telegraph.attachments.photo.max_ratio', 20);

        assert(is_float($maxRatio));

        if (($ratio = $height / $width) > $maxRatio || $ratio < (1 / $maxRatio)) {
            throw FileException::invalidPhotoRatio($ratio, $maxRatio);
        }

        $telegraph->files->put('photo', new Attachment($path));

        return $telegraph;
    }

    public function deleteChatPhoto(): Telegrand
    {
        $telegraph = clone $this;

        $telegraph->endpoint = self::ENDPOINT_DELETE_CHAT_PHOTO;
        $telegraph->data['chat_id'] = $telegraph->getChatId();

        return $telegraph;
    }

    public function chatInfo(): Telegrand
    {
        $telegraph = clone $this;

        $telegraph->endpoint = self::ENDPOINT_GET_CHAT_INFO;
        $telegraph->data['chat_id'] = $telegraph->getChatId();

        return $telegraph;
    }

    public function setChatMenuButton(): SetChatMenuButtonPayload
    {
        $telegraph = clone $this;
        $telegraph->endpoint = self::ENDPOINT_SET_CHAT_MENU_BUTTON;

        if ($this->getChatIfAvailable() !== null) {
            $telegraph->data['chat_id'] = $this->getChatId();
        }


        return SetChatMenuButtonPayload::makeFrom($telegraph);
    }

    public function chatMenuButton(): Telegrand
    {
        $telegraph = clone $this;

        $telegraph->endpoint = self::ENDPOINT_GET_CHAT_MENU_BUTTON;

        return $telegraph;
    }

    public function generateChatPrimaryInviteLink(): Telegrand
    {
        $telegraph = clone $this;

        $telegraph->endpoint = self::ENDPOINT_EXPORT_CHAT_INVITE_LINK;
        $telegraph->data['chat_id'] = $telegraph->getChatId();

        return $telegraph;
    }

    public function createChatInviteLink(): Telegrand
    {
        $telegraph = clone $this;

        $telegraph->endpoint = self::ENDPOINT_CREATE_CHAT_INVITE_LINK;
        $telegraph->data['chat_id'] = $telegraph->getChatId();

        return $telegraph;
    }

    public function expire(Carbon $expiration): Telegrand
    {
        $telegraph = clone $this;

        $telegraph->data['expire_date'] = $expiration->timestamp;

        return $telegraph;
    }

    public function name(string $name): Telegrand
    {
        $telegraph = clone $this;

        $telegraph->data['name'] = $name;

        return $telegraph;
    }

    public function memberLimit(int $limit): Telegrand
    {
        $telegraph = clone $this;

        $telegraph->data['member_limit'] = $limit;

        return $telegraph;
    }

    public function withJoinRequest(bool $enable = true): Telegrand
    {
        $telegraph = clone $this;

        $telegraph->data['creates_join_request'] = $enable;

        return $telegraph;
    }

    public function editChatInviteLink(string $link): Telegrand
    {
        $telegraph = clone $this;

        $telegraph->endpoint = self::ENDPOINT_EDIT_CHAT_INVITE_LINK;
        $telegraph->data['chat_id'] = $telegraph->getChatId();
        $telegraph->data['invite_link'] = $link;

        return $telegraph;
    }

    public function revokeChatInviteLink(string $link): Telegrand
    {
        $telegraph = clone $this;

        $telegraph->endpoint = self::ENDPOINT_REVOKE_CHAT_INVITE_LINK;
        $telegraph->data['chat_id'] = $telegraph->getChatId();
        $telegraph->data['invite_link'] = $link;

        return $telegraph;
    }

    public function chatMemberCount(): Telegrand
    {
        $telegraph = clone $this;

        $telegraph->endpoint = self::ENDPOINT_GET_CHAT_MEMBER_COUNT;
        $telegraph->data['chat_id'] = $telegraph->getChatId();

        return $telegraph;
    }

    public function chatMember(string $userId): Telegrand
    {
        $telegraph = clone $this;

        $telegraph->endpoint = self::ENDPOINT_GET_CHAT_MEMBER;
        $telegraph->data['chat_id'] = $telegraph->getChatId();
        $telegraph->data['user_id'] = $userId;

        return $telegraph;
    }

    /**
     * @param array<int|string, string|bool> $permissions
     */
    public function setChatPermissions(array $permissions): Telegrand
    {
        $telegraph = clone $this;

        $telegraph->endpoint = self::ENDPOINT_SET_CHAT_PERMISSIONS;
        $telegraph->data['chat_id'] = $telegraph->getChatId();

        $permissions = collect($permissions)
            ->mapWithKeys(
                fn ($value, $key) => is_bool($value)
                    ? [$key => $value]
                    : [$value => true]
            );

        $telegraph->data['permissions'] = $permissions;

        return $telegraph;
    }

    public function banChatMember(string $userId): Telegrand
    {
        $telegraph = clone $this;

        $telegraph->endpoint = self::ENDPOINT_BAN_CHAT_MEMBER;
        $telegraph->data['chat_id'] = $telegraph->getChatId();
        $telegraph->data['user_id'] = $userId;

        return $telegraph;
    }

    public function until(Carbon $date): Telegrand
    {
        $telegraph = clone $this;

        $telegraph->data['until_date'] = $date->timestamp;

        return $telegraph;
    }

    public function andRevokeMessages(): Telegrand
    {
        $telegraph = clone $this;

        $telegraph->data['revoke_messages'] = true;

        return $telegraph;
    }

    public function unbanChatMember(string $userId): Telegrand
    {
        $telegraph = clone $this;

        $telegraph->endpoint = self::ENDPOINT_UNBAN_CHAT_MEMBER;
        $telegraph->data['chat_id'] = $telegraph->getChatId();
        $telegraph->data['user_id'] = $userId;
        $telegraph->data['only_if_banned'] = true;

        return $telegraph;
    }

    /**
     * @param array<int|string, string|bool> $permissions
     */
    public function restrictChatMember(string $userId, array $permissions): Telegrand
    {
        $telegraph = clone $this;

        $telegraph->endpoint = self::ENDPOINT_RESTRICT_CHAT_MEMBER;
        $telegraph->data['chat_id'] = $telegraph->getChatId();
        $telegraph->data['user_id'] = $userId;

        /** @var array<string, bool> $permissions */
        $permissions = collect($permissions)
            ->mapWithKeys(
                fn ($value, $key) => is_bool($value)
                    ? [$key => $value]
                    : [$value => true]
            );


        $telegraph->data['permissions'] = $permissions;

        return $telegraph;
    }

    /**
     * @param array<int|string, string|bool> $permissions
     */
    public function promoteChatMember(string $userId, array $permissions): Telegrand
    {
        $telegraph = clone $this;

        $telegraph->endpoint = self::ENDPOINT_PROMOTE_CHAT_MEMBER;
        $telegraph->data['chat_id'] = $telegraph->getChatId();
        $telegraph->data['user_id'] = $userId;

        /** @var array<string, bool> $permissions */
        $permissions = collect($permissions)
            ->mapWithKeys(
                fn ($value, $key) => is_bool($value)
                    ? [$key => $value]
                    : [$value => true]
            );

        foreach ($permissions as $permission => $enabled) {
            $telegraph->data[$permission] = $enabled;
        }

        return $telegraph;
    }

    public function demoteChatMember(string $userId): Telegrand
    {
        $telegraph = clone $this;

        $telegraph->endpoint = self::ENDPOINT_PROMOTE_CHAT_MEMBER;
        $telegraph->data['chat_id'] = $telegraph->getChatId();
        $telegraph->data['user_id'] = $userId;

        /** @var array<string|bool> $permissions */
        $permissions = collect(ChatAdminPermissions::available_permissions())
            ->mapWithKeys(fn (string $value) => [$value => false])
            ->toArray();

        foreach ($permissions as $permission => $enabled) {
            $telegraph->data[$permission] = $enabled;
        }

        return $telegraph;
    }
}
