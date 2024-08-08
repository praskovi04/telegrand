<?php

namespace Praskovi04\Telegrand\Database\Factories;

use Praskovi04\Telegrand\Models\TelegraphBot;
use Praskovi04\Telegrand\Models\TelegraphChat;
use Illuminate\Database\Eloquent\Factories\Factory;

class TelegraphChatFactory extends Factory
{
    protected $model = TelegraphChat::class;

    /**
     * @inheritDoc
     */
    public function definition(): array
    {
        return [
            'chat_id' => $this->faker->randomNumber(),
            'name' => $this->faker->word,
            'telegraph_bot_id' => fn () => TelegraphBot::factory()->create(),
        ];
    }
}
