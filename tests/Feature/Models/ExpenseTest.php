<?php

namespace Tests\Feature\Models;

use App\Models\Expense;
use App\Models\Identifier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ExpenseTest extends TestCase
{
    use RefreshDatabase;
    /**
     * deve retornar o User
     */
    public function test_user_relation(): void
    {
        $user = User::factory()->create();
        $expense = Expense::factory()->create([
            "user_id" => $user
        ]);

        $this->assertEquals($user->id, $expense->user->id);
    }

    /**
     * deve retornar o User mesmo deletado
     */
    public function test_user_relation_trashed_user(): void
    {
        $user = User::factory()->trashed()->create();
        $expense = Expense::factory()->create([
            "user_id" => $user
        ]);

        $this->assertEquals($user->id, $expense->user->id);
    }

    /**
     * deve retornar Identifier
     */
    public function test_identifier_relation(): void
    {
        $identifier = Identifier::factory()->create();
        $expense = Expense::factory()->create([
            "identifier_id" => $identifier
        ]);

        $this->assertEquals($identifier->id, $expense->identifier->id);
    }

    /**
     * deve retornar Identifier mesmo deletado
     */
    public function test_identifier_relation_trashed_identifier(): void
    {
        $identifier = Identifier::factory()->trashed()->create();
        $expense = Expense::factory()->create([
            "identifier_id" => $identifier
        ]);

        $this->assertEquals($identifier->id, $expense->identifier->id);
    }
}
