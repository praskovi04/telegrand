<?php

/** @noinspection PhpUnhandledExceptionInspection */

/** @noinspection PhpUnused */

/** @noinspection PhpPropertyOnlyWrittenInspection */

namespace Praskovi04\Telegrand\Support\Testing\Fakes;

use Praskovi04\Telegrand\Concerns\FakesRequests;
use Praskovi04\Telegrand\DTO\Attachment;
use Praskovi04\Telegrand\ScopedPayloads\SetChatMenuButtonPayload;
use Praskovi04\Telegrand\ScopedPayloads\TelegrandPollPayload;
use Praskovi04\Telegrand\ScopedPayloads\TelegrandQuizPayload;
use Praskovi04\Telegrand\Telegrand;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Assert;

class TelegrandFake extends Telegrand
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

    public function prepareForNewRequest(): void
    {
        $this->files = new Collection();
    }

    public function editMedia(int $messageId): TelegraphEditMediaFake
    {
        $fake = new TelegraphEditMediaFake($this->replies);
        $fake->endpoint = self::ENDPOINT_EDIT_MEDIA;
        $fake->data['message_id'] = $messageId;

        return $fake;
    }

    public function poll(string $question): TelegrandPollPayload
    {
        $fake = new TelegraphPollFake($this->replies);
        $fake->endpoint = self::ENDPOINT_SEND_POLL;
        $fake->data['options'] = [];
        $fake->data['question'] = $question;

        return $fake;
    }

    public function setChatMenuButton(): SetChatMenuButtonPayload
    {
        $fake = new TelegraphSetChatMenuButtonFake($this->replies);
        $fake->endpoint = self::ENDPOINT_SET_CHAT_MENU_BUTTON;
        $fake->data = $this->data;

        return $fake;
    }

    public function quiz(string $question): TelegrandQuizPayload
    {
        $fake = new TelegraphQuizFake($this->replies);
        $fake->endpoint = self::ENDPOINT_SEND_POLL;
        $fake->data['options'] = [];
        $fake->data['question'] = $question;
        $fake->data['type'] = 'quiz';

        return $fake;
    }

    /**
     * @param array<string, Attachment> $expectedFiles
     */
    public function assertSentFiles(string $endpoint, array $expectedFiles = []): void
    {
        $foundMessages = collect(self::$sentMessages);

        $foundMessages = $foundMessages
            ->filter(fn (array $message): bool => $message['endpoint'] == $endpoint)
            ->filter(function (array $message) use ($expectedFiles): bool {
                foreach ($expectedFiles as $key => $expectedFile) {
                    /** @var array<string, Attachment> $sentFiles */
                    $sentFiles = $message['files'];

                    if (!Arr::has($sentFiles, $key)) {
                        return false;
                    }

                    if ($expectedFile->filename() !== $sentFiles[$key]->filename()) {
                        return false;
                    }

                    if ($expectedFile->contents() !== $sentFiles[$key]->contents()) {
                        return false;
                    }
                }

                return true;
            });


        if ($foundMessages == null) {
            $errorMessage = sprintf("Failed to assert that a request was sent to [%s] endpoint (sent %d requests so far)", $endpoint, count(self::$sentMessages));
        } else {
            $errorMessage = sprintf("Failed to assert that a request was sent to [%s] endpoint with the given files (sent %d requests so far)", $endpoint, count(self::$sentMessages));
        }

        Assert::assertNotEmpty($foundMessages->toArray(), $errorMessage);
    }

    public function assertStoredFile(string $fileId): void
    {
        $downloadedFiles = collect(self::$downloadedFiles)
            ->filter(fn ($downloadedFileId) => $fileId === $downloadedFileId);

        Assert::assertNotEmpty($downloadedFiles->toArray(), sprintf("Failed to assert that a file with id [%s] was stored (%d files stored so fare)", $fileId, count(self::$downloadedFiles)));
    }

    public function assertSent(string $message, bool $exact = true): void
    {
        $this->assertSentData(Telegrand::ENDPOINT_MESSAGE, [
            'text' => $message,
        ], $exact);
    }

    public function assertNothingSent(): void
    {
        Assert::assertEmpty(self::$sentMessages, sprintf("Failed to assert that no request were sent (sent %d requests so far)", count(self::$sentMessages)));
    }

    public function assertRegisteredWebhook(): void
    {
        $this->assertSentData(Telegrand::ENDPOINT_SET_WEBHOOK);
    }

    public function assertUnregisteredWebhook(): void
    {
        $this->assertSentData(Telegrand::ENDPOINT_UNSET_WEBHOOK);
    }

    public function assertRequestedWebhookDebugInfo(): void
    {
        $this->assertSentData(Telegrand::ENDPOINT_GET_WEBHOOK_DEBUG_INFO);
    }

    public function assertRepliedWebhook(string $message): void
    {
        $this->assertSentData(Telegrand::ENDPOINT_ANSWER_WEBHOOK, [
            'text' => $message,
        ]);
    }

    public function assertRepliedWebhookIsAlert(): void
    {
        $this->assertSentData(Telegrand::ENDPOINT_ANSWER_WEBHOOK, [
            'show_alert' => true,
        ]);
    }
}
