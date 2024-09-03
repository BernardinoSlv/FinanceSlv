<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Expense;
use App\Models\Identifier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
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

    /**deve ter status 200 */
    public function test_create_action(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route("expenses.create"))
            ->assertOk()
            ->assertViewIs("expenses.create")
            ->assertViewHas(["identifiers"]);
    }

    /**
     * ceve redirecionar com erros de validação
     */
    public function test_store_action_without_data(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route("expenses.store"))
            ->assertFound()
            ->assertSessionHasErrors([
                "identifier_id",
                "title",
                "amount",
                "due_day"
            ])
            ->assertSessionDoesntHaveErrors([
                "description"
            ]);
    }

    /**
     * deve redirecionar apenas com erro no campo identifier_id
     */
    public function test_store_action_is_not_owner_from_the_identifier(): void
    {
        $user = User::factory()->create();
        $data = Expense::factory()->make([
            "amount" => "200,00"
        ])->toArray();

        $this->actingAs($user)->post(route("expenses.store"), $data)
            ->assertFound()
            ->assertSessionHasErrors([
                "identifier_id",

            ])
            ->assertSessionDoesntHaveErrors([
                "title",
                "amount",
                "due_day",
                "description"
            ]);
    }

    /**
     * deve redirecionar com mensagem de sucesso
     */
    public function test_store_action(): void
    {
        $user = User::factory()
            ->has(Identifier::factory())->create();
        $data = Expense::factory()->make([
            "amount" => "200,00",
            "identifier_id" => $user->identifiers->get(0)
        ])->toArray();

        $this->actingAs($user)->post(route("expenses.store"), $data)
            ->assertRedirect(route("expenses.index"))
            ->assertSessionHas("alert_type", "success");
        $this->assertDatabaseHas("expenses", [
            ...Arr::except($data, "user_id"),
            "amount" => 200
        ]);
    }
}
