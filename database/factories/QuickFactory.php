<?php

namespace Database\Factories;

use App\Models\Identifier;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Quick>
 */
class QuickFactory extends Factory
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
            "identifier_id" => Identifier::factory()->create(),
            "title" => fake()->title(),
            "description" => fake()->text(100),
        ];
    }
}
