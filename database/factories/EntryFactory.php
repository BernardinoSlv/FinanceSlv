<?php

namespace Database\Factories;

use App\Models\Debtor;
use App\Models\Identifier;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Entry>
 */
class EntryFactory extends Factory
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
            // "entryable_type" => ,
            // "entryable_id" => ,
            "amount" => rand(0, 500)
        ];
    }
}
