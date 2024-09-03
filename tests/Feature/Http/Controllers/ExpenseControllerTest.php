<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class ExpenseControllerTest extends TestCase
{
    /**
     * deve redirecionar para login
     */
    public function test_index_action_unauthenticated(): void
    {
        $this->get(route("expenses.index"))
            ->assertRedirect(route("auth.index"));
    }

    /**
     * deve ter status 200
     */
    public function test_index_action(): void
    {
        $user = User::factory()
            ->has(Expense::factory(2))
            ->create();

        $this->actingAs($user)->get(route("expenses.index"))
            ->assertOk()
            ->assertViewHas("expenses", function (LengthAwarePaginator $expenses): bool {
                return $expenses->total() === 2;
            })
            ->assertViewIs("expenses.index");
    }
}
