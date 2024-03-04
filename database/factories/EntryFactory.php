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
        $user = User::factory()->create();
        $debtor = Debtor::factory()->create([
            "user_id" => $user,
            "identifier_id" => Identifier::factory()->create([
                "user_id" => $user
            ])
        ]);

        return [
            "user_id" => $user,
            "entryable_type" => Debtor::class,
            "entryable_id" => $debtor
        ];
    }
}
