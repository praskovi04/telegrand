<?php

namespace Praskovi04\Telegrand;

use Praskovi04\Telegrand\Commands\CreateNewBotCommand;
use Praskovi04\Telegrand\Commands\CreateNewChatCommand;
use Praskovi04\Telegrand\Commands\GetTelegramWebhookDebugInfoCommand;
use Praskovi04\Telegrand\Commands\SetTelegramWebhookCommand;
use Praskovi04\Telegrand\Commands\UnsetTelegramWebhookCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class TelegrandServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('telegraph')
            ->hasConfigFile()
            ->hasRoute('api')
            ->hasMigration('create_telegraph_bots_table')
            ->hasMigration('create_telegraph_chats_table')
            ->hasCommand(CreateNewBotCommand::class)
            ->hasCommand(CreateNewChatCommand::class)
            ->hasCommand(SetTelegramWebhookCommand::class)
            ->hasCommand(UnsetTelegramWebhookCommand::class)
            ->hasCommand(GetTelegramWebhookDebugInfoCommand::class)
            ->hasTranslations();

        $this->app->bind('telegraph', fn () => new Telegrand());
    }
}
