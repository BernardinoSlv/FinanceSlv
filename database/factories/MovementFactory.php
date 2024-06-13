<?php

namespace Database\Factories;

use App\Enums\MovementTypeEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Movement>
 */
class MovementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "user_id" => User::factory()->create(),
            // "movementable_type" => ,
            // "movementable_id" => ,
            "type" => MovementTypeEnum::IN->value,
            "amount" => rand(1, 1000),
        ];
    }
}
