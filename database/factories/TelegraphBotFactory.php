<?php

namespace Praskovi04\Telegrand\Database\Factories;

use Praskovi04\Telegrand\Models\TelegraphBot;
use Illuminate\Database\Eloquent\Factories\Factory;

class TelegraphBotFactory extends Factory
{
    protected $model = TelegraphBot::class;

    /**
     * @inheritDoc
     */
    public function definition(): array
    {
        return [
            'token' => $this->faker->uuid,
            'name' => $this->faker->word,
        ];
    }
}
