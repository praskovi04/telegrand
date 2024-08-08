<?php

namespace Praskovi04\Telegrand\Facades;

use Praskovi04\Telegrand\Contracts\Downloadable;
use Praskovi04\Telegrand\Keyboard\Keyboard;
use Praskovi04\Telegrand\Models\TelegraphBot;
use Praskovi04\Telegrand\Models\TelegraphChat;
use Praskovi04\Telegrand\ScopedPayloads\SetChatMenuButtonPayload;
use Praskovi04\Telegrand\ScopedPayloads\TelegrandPollPayload;
use Praskovi04\Telegrand\ScopedPayloads\TelegrandQuizPayload;
use Praskovi04\Telegrand\Support\Testing\Fakes\TelegrandFake;
use Illuminate\Support\Facades\Facade;

/**
 * @method static string getUrl()
 * @method static \Praskovi04\Telegrand\Telegrand  bot(TelegraphBot|string $bot)
 * @method static \Praskovi04\Telegrand\Telegrand  chat(TelegraphChat|string $chat)
 * @method static \Praskovi04\Telegrand\Telegrand  message(string $message)
 * @method static \Praskovi04\Telegrand\Telegrand  withData(string $key, mixed $value)
 * @method static \Praskovi04\Telegrand\Telegrand  inThread(string $thread_id)
 * @method static \Praskovi04\Telegrand\Telegrand  html(string $message)
 * @method static \Praskovi04\Telegrand\Telegrand  reply(int $messageId)
 * @method static \Praskovi04\Telegrand\Telegrand  edit(string $messageId)
 * @method static \Praskovi04\Telegrand\Telegrand  markdown(string $message)
 * @method static \Praskovi04\Telegrand\Telegrand  markdownV2(string $message)
 * @method static \Praskovi04\Telegrand\Telegrand  registerWebhook()
 * @method static \Praskovi04\Telegrand\Telegrand  unregisterWebhook(bool $dropPendingUpdates = false)
 * @method static \Praskovi04\Telegrand\Telegrand  registerBotCommands(array $commands)
 * @method static \Praskovi04\Telegrand\Telegrand  getRegisteredCommands()
 * @method static \Praskovi04\Telegrand\Telegrand  unregisterBotCommands()
 * @method static \Praskovi04\Telegrand\Telegrand  getWebhookDebugInfo()
 * @method static \Praskovi04\Telegrand\Telegrand  replyWebhook(string $callbackQueryId, string $message)
 * @method static \Praskovi04\Telegrand\Telegrand  replaceKeyboard(string $messageId, Keyboard|callable $newKeyboard)
 * @method static \Praskovi04\Telegrand\Telegrand  deleteKeyboard(string $messageId)
 * @method static \Praskovi04\Telegrand\Telegrand  deleteMessage(string $messageId)
 * @method static \Praskovi04\Telegrand\Telegrand  forwardMessage($fromChat, $messageId)
 * @method static \Praskovi04\Telegrand\Telegrand  pinMessage(string $messageId)
 * @method static \Praskovi04\Telegrand\Telegrand  unpinMessage(string $messageId)
 * @method static \Praskovi04\Telegrand\Telegrand  unpinAllMessages()
 * @method static \Praskovi04\Telegrand\Telegrand  editCaption(string $messageId)
 * @method static \Praskovi04\Telegrand\Telegrand  editMedia(string $messageId)
 * @method static \Praskovi04\Telegrand\Telegrand  answerInlineQuery(string $inlineQueryID, array $results)
 * @method static \Praskovi04\Telegrand\Telegrand  document(string $path, string $filename = null)
 * @method static \Praskovi04\Telegrand\Telegrand  photo(string $path, string $filename = null)
 * @method static \Praskovi04\Telegrand\Telegrand  animation(string $path, string $filename = null)
 * @method static \Praskovi04\Telegrand\Telegrand  voice(string $path, string $filename = null)
 * @method static \Praskovi04\Telegrand\Telegrand  location(float $latitude, float $longitude)
 * @method static \Praskovi04\Telegrand\Telegrand  contact(string $phoneNumber, string $firstName)
 * @method static \Praskovi04\Telegrand\Telegrand  video(string $path, string $filename = null)
 * @method static \Praskovi04\Telegrand\Telegrand  audio(string $path, string $filename = null)
 * @method static \Praskovi04\Telegrand\Telegrand  dice()
 * @method static \Praskovi04\Telegrand\Telegrand  mediaGroup(string $path, array $media)
 * @method static \Praskovi04\Telegrand\Telegrand  botUpdates()
 * @method static \Praskovi04\Telegrand\Telegrand  botInfo()
 * @method static \Praskovi04\Telegrand\Telegrand  setBaseUrl(string|null $url)
 * @method static \Praskovi04\Telegrand\Telegrand  setTitle(string $title)
 * @method static \Praskovi04\Telegrand\Telegrand  setDescription(string $description)
 * @method static \Praskovi04\Telegrand\Telegrand  setChatPhoto(string $path)
 * @method static \Praskovi04\Telegrand\Telegrand  chatInfo()
 * @method static \Praskovi04\Telegrand\Telegrand  generateChatPrimaryInviteLink()
 * @method static \Praskovi04\Telegrand\Telegrand  createChatInviteLink()
 * @method static \Praskovi04\Telegrand\Telegrand  editChatInviteLink()
 * @method static \Praskovi04\Telegrand\Telegrand  revokeChatInviteLink()
 * @method static \Praskovi04\Telegrand\Telegrand  chatMemberCount()
 * @method static \Praskovi04\Telegrand\Telegrand  chatMember(string $userId)
 * @method static \Praskovi04\Telegrand\Telegrand  setChatPermissions(array $permissions)
 * @method static \Praskovi04\Telegrand\Telegrand  banChatMember(string $userId)
 * @method static \Praskovi04\Telegrand\Telegrand  unbanChatMember(string $userId)
 * @method static \Praskovi04\Telegrand\Telegrand  restrictChatMember(string $userId, array $permissions)
 * @method static \Praskovi04\Telegrand\Telegrand  promoteChatMember(string $userId, array $permissions)
 * @method static \Praskovi04\Telegrand\Telegrand  demoteChatMember(string $userId)
 * @method static \Praskovi04\Telegrand\Telegrand  userProfilePhotos(string $userId)
 * @method static \Praskovi04\Telegrand\Telegrand  chatMenuButton()
 * @method static SetChatMenuButtonPayload  setChatMenuButton()
 * @method static TelegrandPollPayload poll(string $question)
 * @method static TelegrandQuizPayload quiz(string $question)
 * @method static string store(Downloadable $attachment, string $path, string $filename = null)
 * @method static void  dumpSentData()
 * @method static void  assertSentData(string $endpoint, array $data = null, bool $exact = true)
 * @method static void  assertSentFiles(string $endpoint, array $files = null)
 * @method static void  assertSent(string $message, bool $exact = true)
 * @method static void  assertNothingSent()
 * @method static void  assertRegisteredWebhook()
 * @method static void  assertUnregisteredWebhook()
 * @method static void  assertRequestedWebhookDebugInfo()
 * @method static void  assertRepliedWebhook(string $message)
 * @method static void  assertRepliedWebhookIsAlert()
 * @method static void  assertStoredFile(string $fileId)
 *
 * @see \Praskovi04\Telegrand\Telegrand
 */
class Telegraph extends Facade
{
    protected static $cached = false;

    /**
     * @param array<string, array<mixed>> $replies
     */
    public static function fake(array $replies = []): TelegrandFake
    {
        TelegrandFake::reset();
        static::swap($fake = new TelegrandFake($replies));

        return $fake;
    }

    public static function getFacadeRoot()
    {
        $instance = parent::getFacadeRoot();
        if ($instance instanceof TelegrandFake) {
            $instance->prepareForNewRequest();
        }

        return $instance;
    }

    protected static function getFacadeAccessor(): string
    {
        return 'telegraph';
    }
}
