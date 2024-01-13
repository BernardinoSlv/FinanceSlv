<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Debt;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DebtControllerTest extends TestCase
{
    /**
     * deve redirecionar para login
     */
    public function test_index_action_unauthenticated(): void
    {
        $this->get(route("debts.index"))->assertRedirectToRoute("auth.index");
    }

    /**
     * deve ter status 200 e view debt.index
     */
    public function test_index_action(): void
    {
        $user = $this->_user();
        Debt::factory(10)->create();
        Debt::factory(5)->create([
            "user_id" => $user
        ]);

        $this->actingAs($user)->get(route("debts.index"))
            ->assertOk()
            ->assertViewIs("debt.index")
            ->assertViewHas("debts", function (Collection $debts): bool {
                return $debts->count() === 5;
            });
    }

    /**
     * deve redirecionar para login
     */
    public function test_create_action_unauthenticated(): void
    {
        $this->get(route("debts.index"))->assertRedirect(route("auth.index"));
    }

    /**
     * deve ter status 200 e view debt.create
     */
    public function test_create_action(): void
    {
        $this->actingAs($this->_user())->get(route("debts.create"))
            ->assertOk()
            ->assertViewIs("debt.create");
    }

    /**
     * deve redirecionar para login
     */
    public function test_store_action_unauthenticated(): void
    {
        $this->post(route("debts.store"))
            ->assertRedirect(route("auth.index"));
    }

    /**
     * deve redirecionar com erros de validação
     */
    public function test_store_action_without_data(): void
    {
        $this->actingAs($this->_user())->post(route("debts.store"))
            ->assertFound()
            ->assertSessionHasErrors([
                "title",
                "amount",
                "start_at"
            ])
            ->assertSessionDoesntHaveErrors([
                "description"
            ]);
    }

    /**
     * deve redirecinoar com erro de validação no campo title
     */
    public function test_store_action_duplicated_title(): void
    {
        $user = $this->_user();
        $debt = Debt::factory()->create([
            "user_id" => $user
        ]);
        $data = Debt::factory()->make([
            "title" => $debt->title,
            "amount" => "70,00"
        ])->toArray();

        $this->actingAs($user)->post(route("debts.store"), $data)
            ->assertFound()
            ->assertSessionHasErrors([
                "title",
            ])
            ->assertSessionDoesntHaveErrors([
                "description",
                "amount",
                "start_at"
            ]);
    }

    /**
     * deve redirecionar com  mensagem de sucesso
     */
    public function test_store_action(): void
    {
        $user = $this->_user();
        $data = Debt::factory()->make([
            "amount" => "70,00"
        ])->toArray();

        $this->actingAs($user)->post(route("debts.store"), $data)
            ->assertRedirect(route("debts.index"))
            ->assertSessionHas("alert_type", "success");
        $this->assertDatabaseHas("debts", [
            ...$data,
            "amount" => 70.00,
            "user_id" => $user->id
        ]);
    }

    /**
     * deve criar registro com mesmo title, no entanto o existente deve pertencer a outro usuário
     */
    public function test_store_action_same_title_but_other_user(): void
    {
        $user = $this->_user();
        $data = Debt::factory()->make([
            "title" => Debt::factory()->create()->title,
            "amount" => "70,00"
        ])->toArray();

        $this->actingAs($user)->post(route("debts.store"), $data)
            ->assertRedirect(route("debts.index"))
            ->assertSessionHas("alert_type", "success");
        $this->assertDatabaseHas("debts", [
            ...$data,
            "amount" => 70.00,
            "user_id" => $user->id
        ]);
    }

    /**
     * deve redirecionar para login
     */
    public function test_edit_action_unauthenticated(): void
    {
        $debt = Debt::factory()->create();

        $this->get(route("debts.edit", $debt))->assertRedirectToRoute("auth.index");
    }

    /**
     * deve ter status 404
     */
    public function test_edit_action_is_not_owner(): void
    {
        $debt = Debt::factory()->create();

        $this->actingAs($this->_user())->get(route("debts.edit", $debt))
            ->assertNotFound();
    }

    /**
     * deve ter status 404
     */
    public function test_edit_action_nonexistent(): void
    {
        $this->actingAs($this->_user())->get(route("debts.edit", 0))
            ->assertNotFound();
    }

    /**
     * deve ter status 200 e view debt.edit
     */
    public function test_edit_action(): void
    {
        $user = $this->_user();
        $debt = Debt::factory()->create([
            "user_id" => $user
        ]);

        $this->actingAs($user)->get(route("debts.edit", $debt))
            ->assertOk()
            ->assertViewIs("debt.edit")
            ->assertViewHas("debt");
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
     * deve redirecionar com erros de validação
     */
    public function test_update_action_without_data(): void
    {
        $user = User::factory()->create();
        $debt = Debt::factory()->create([
            "user_id" => $user
        ]);

        $this->actingAs($user)->put(route("debts.update", $debt))
            ->assertFound()
            ->assertSessionHasErrors([
                "title",
                "amount",
                "start_at"
            ])
            ->assertSessionDoesntHaveErrors("description");
    }

    /**
     * deve ter status 404
     */
    public function test_update_action_is_not_owner(): void
    {
        $debt = Debt::factory()->create();
        $data = Debt::factory()->make([
            "amount" => "75,00"
        ])->toArray();

        $this->actingAs($this->_user())->put(route("debts.update", $debt), $data)
            ->assertNotFound();
    }

    /**
     * deve redirecionar com erro no campo title
     */
    public function test_update_action_duplicated_title(): void
    {
        $user = $this->_user();
        $debt = Debt::factory()->create([
            "user_id" => $user
        ]);
        $otherDebt = Debt::factory()->create([
            "user_id" => $user
        ]);
        $data = Debt::factory()->make([
            "title" => $otherDebt->title,
            "amount" => "55,00"
        ])->toArray();

        $this->actingAs($user)->put(route("debts.update", $debt), $data)
            ->assertFound()
            ->assertSessionHasErrors("title")
            ->assertSessionDoesntHaveErrors([
                "amount",
                "description",
                "start_at"
            ]);
    }

    /**
     * deve redirecionar com mensagem de sucesso
     */
    public function test_update_action(): void
    {
        $user = $this->_user();
        $debt = Debt::factory()->create([
            "user_id" => $user
        ]);
        $data = Debt::factory()->make([
            "amount" => "200,00"
        ])->toArray();

        $this->actingAs($user)->put(route("debts.update", $debt), $data)
            ->assertRedirectToRoute("debts.index")
            ->assertSessionHas("alert_type", "success");
        $this->assertDatabaseHas("debts", [
            ...$data,
            "user_id" => $user->id,
            "amount" => 200
        ]);
    }

    /**
     * deve redirecionar com mensagem de sucesso
     */
    public function test_update_action_same_title(): void
    {
        $user = $this->_user();
        $debt = Debt::factory()->create([
            "user_id" => $user
        ]);
        $data = Debt::factory()->make([
            "title" => $debt->title,
            "amount" => "200,00"
        ])->toArray();

        $this->actingAs($user)->put(route("debts.update", $debt), $data)
            ->assertRedirectToRoute("debts.index")
            ->assertSessionHas("alert_type", "success");
        $this->assertDatabaseHas("debts", [
            ...$data,
            "user_id" => $user->id,
            "amount" => 200
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
        $this->actingAs($this->_user())->delete(route("debts.destroy", 0))
            ->assertNotFound();
    }

    /**
     * deve ter status 404
     */
    public function test_destroy_action_is_not_owner(): void
    {
        $debt = Debt::factory()->create();

        $this->actingAs($this->_user())->delete(route("debts.destroy", $debt))
            ->assertNotFound();
    }

    /**
     * deve redirecionar com mensagem de sucesso
     */
    public function test_destroy_action(): void
    {
        $user = $this->_user();
        $debt = Debt::factory()->create([
            "user_id" => $user
        ]);

        $this->actingAs($user)->delete(route("debts.destroy", $debt))
            ->assertRedirectToRoute("debts.index")
            ->assertSessionHas("alert_type", "success");
        $this->assertSoftDeleted($debt);
    }
}
