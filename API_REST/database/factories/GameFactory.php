<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Game>
 */
class GameFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $dice1 = $this->faker->numberBetween(1, 6);
        $dice2 = $this->faker->numberBetween(1, 6);
        $result = $dice1 + $dice2;
        $winner = $result == 7; // Supongamos que ganar es cuando el resultado es 7

        return [
            'user_id' => User::factory(), // Crea un nuevo usuario si no se proporciona uno
            'dice1' => $dice1,
            'dice2' => $dice2,
            'winner' => $winner,
        ];
    }
}
