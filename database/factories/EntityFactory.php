<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Entity>
 */
class EntityFactory extends Factory
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
            "name" => fake()->name() . rand(0, 1000) . rand(0, 1000) . time(),
            "phone" => fake("pt_BR")->phoneNumber(),
            "description" => fake()->text()
        ];
    }
}
