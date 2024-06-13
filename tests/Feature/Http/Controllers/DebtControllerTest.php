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

    /**
     * deve redirecionar para login
     */
    public function test_edit_action_unauthenticated(): void
    {
        $debt = Debt::factory()->create();

        $this->get(route("debts.edit", $debt))
            ->assertRedirectToRoute("auth.index");
    }

    /**
     * deve ter status 404
     */
    public function test_edit_action_nonexistent(): void
    {
        $user = User::factory()->create();

        $this->get(route("debts.edit", 0))
            ->assertNotFound();
    }

    /**
     * deve ter status 403
     */
    public function test_edit_action_is_not_owner(): void
    {
        $user = User::factory()->create();
        $debt = Debt::factory()->create();

        $this->actingAs($user)->get(route("debts.edit", $debt))
            ->assertForbidden();
    }

    /**
     * deve ter status 200 e view debts.edit
     */
    public function test_edit_action(): void
    {
        $user = User::factory()->has(Debt::factory())->create();
        $debt = $user->debts->first();

        $this->actingAs($user)->get(route("debts.edit", $debt))
            ->assertOk()
            ->assertViewIs("debts.edit")
            ->assertViewHas([
                "debt",
                "identifiers"
            ]);
    }

    /**
     * deve redirecionar para login
     */
    public function test_update_action_unauthenticated(): void
    {
        $debt = Debt::factory()->create();

        $this->put(route("debts.update", $debt))
            ->assertRedirectToRoute("auth.index");
    }

    /**
     * deve ter status 404
     */
    public function test_update_action_nonexistent(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->put(route('debts.update', 0))
            ->assertNotFound();
    }

    /**
     * deve redirecionar com erros de validação
     */
    public function test_update_action_without_data(): void
    {
        $user = User::factory()->has(Debt::factory())->create();
        $debt = $user->debts->first();

        $this->actingAs($user)->put(route("debts.update", $debt))
            ->assertFound()
            ->assertSessionHasErrors([
                "identifier_id",
                "title",
                "amount",
                "due_date"
            ]);
    }

    /**
     * deve ter status 403
     */
    public function test_update_action_is_not_owner(): void
    {
        $user = User::factory()->has(Identifier::factory())->create();
        $debt = Debt::factory()->create();
        $data = Debt::factory()->make([
            "identifier_id" => $user->identifiers->first(),
            "amount" => "500,00",
        ])->toArray();

        $this->actingAs($user)->put(route("debts.update", $debt), $data)
            ->assertForbidden();
    }

    /**
     * deve redirecionar com mensagem de sucesso
     */
    public function test_update_action(): void
    {
        $user = User::factory()
            ->has(Identifier::factory())
            ->has(Debt::factory())
            ->create();
        $debt = $user->debts->first();
        $data = Debt::factory()->make([
            "identifier_id" => $user->identifiers->first(),
            "amount" => "500,00",
        ])->toArray();

        $this->actingAs($user)->put(route("debts.update", $debt), $data)
            ->assertRedirect(route("debts.edit", $debt))
            ->assertSessionHas("alert_type", "success");
        $this->assertDatabaseHas("debts", [
            ...$data,
            "due_date" => date("Y-m-d", strtotime($data["due_date"])),
            "amount" => 500,
            "user_id" => $user->id,
            "id" => $debt->id,
        ]);
    }

    /**
     * deve redirecionar para login
     */
    public function test_destroy_action_unauthenticated(): void
    {
        $debt = Debt::factory()->create();

        $this->delete(route("debts.destroy", $debt))
            ->assertRedirectToRoute("auth.index");
    }

    /**
     * deve ter status 404
     */
    public function test_destroy_action_nonexistent(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->delete(route("debts.destroy", 0))
            ->assertNotFound();
    }

    /**
     * deve ter status 403
     */
    public function test_destroy_action_is_not_owner(): void
    {
        $user = User::factory()->create();
        $debt = Debt::factory()->create();

        $this->actingAs($user)->delete(route("debts.destroy", $debt))
            ->assertForbidden();
    }

    /**
     * deve redirecionar com mensagem de sucesso
     */
    public function test_destroy_action(): void
    {
        $user = User::factory()->has(Debt::factory())->create();
        $debt = $user->debts->first();

        $this->actingAs($user)->delete(route('debts.destroy', $debt))
            ->assertRedirect(route("debts.index"))
            ->assertSessionHas("alert_type", "success");
        $this->assertSoftDeleted($debt);
    }
}
