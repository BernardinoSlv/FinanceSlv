<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Debt;
use App\Models\Identifier;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Tests\TestCase;

class DebtControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * deve redirecionar para login
     */
    public function test_index_action_unauthenticated(): void
    {
        $this->get(route("debts.index"))
            ->assertRedirectToRoute("auth.index");
    }

    /**
     * deve te status 200 e view debts.index
     */
    public function test_index_action(): void
    {
        Debt::factory(2)->create();

        $user = User::factory()->has(Debt::factory(2))->create();

        $this->actingAs($user)->get(route('debts.index'))
            ->assertOk()
            ->assertViewIs("debts.index")
            ->assertViewHas("debts", function (LengthAwarePaginator $debts): bool {
                if (!$debts->first()->loadCount('movements')) return false;
                return $debts->total() === 2;
            });
    }

    /**
     * deve redirecionar para login
     */
    public function test_create_action_unauthenticated(): void
    {
        $this->get(route("debts.create"))
            ->assertRedirectToRoute("auth.index");
    }

    /**
     * deve ter status 200 e view debts.index
     */
    public function test_create_action(): void
    {
        Identifier::factory(2)->create();

        $user = User::factory()->has(Identifier::factory(2))->create();

        $this->actingAs($user)->get(route("debts.create"))
            ->assertOk()
            ->assertViewIs("debts.create")
            ->assertViewHas("identifiers", function (Collection $identifiers): bool {
                return $identifiers->count() === 2;
            });
    }

    /** deve redirecionar para login  */
    public function test_store_action_unauthenticated(): void
    {
        $this->post(route("debts.store"))
            ->assertRedirectToRoute("auth.index");
    }

    /**
     * deve redirecionar com erros de validação
     */
    public function test_store_action_without_data(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route("debts.store"))
            ->assertFound()
            ->assertSessionHasErrors([
                "identifier_id",
                "amount",
                "title",
                "due_date"
            ]);
    }

    /**
     * deve redirecionar com mensagem de sucesso
     */
    public function test_store_action(): void
    {
        $user = User::factory()->has(Identifier::factory())->create();
        $data = Debt::factory()->make([
            "identifier_id" => $user->identifiers->first(),
            "amount" => "200,00"
        ])->toArray();

        $this->actingAs($user)->post(route("debts.store"), $data)
            ->assertFound()
            ->assertSessionHas("alert_type", "success");
        $this->assertDatabaseHas("debts", [
            ...$data,
            "due_date" => date("Y-m-d", strtotime($data["due_date"])),
            "amount" => 200,
            "user_id" => $user->id
        ]);
    }
}
