<?php

namespace Database\Factories;

use App\Models\Identifier;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\QuickEntry>
 */
class QuickEntryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::factory()->create();

        return [
            "user_id" => $user,
            "identifier_id" => Identifier::factory()->create([
                "user_id" => $user
            ]),
            "title" => fake("pt-BR")->title(),
            "description" => fake("text"),
            "amount" => 20.00,
        ];
    }
}
