<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Investiment;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class InvestimentControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * deve redirecionar para login
     */
    public function test_index_action_unauthenticated(): void
    {
        $this->get(route("investiments.index"))->assertRedirectToRoute("auth.index");
    }

    /**
     * deve ter status 200 e view investiment.index
     */
    public function test_index_action(): void
    {
        Investiment::factory(20)->create();
        $user = $this->_user();
        Investiment::factory(10)->create([
            "user_id" => $user->id
        ]);

        $this->actingAs($user)->get(route("investiments.index"))
            ->assertOk()
            ->assertViewIs("investiment.index")
            ->assertViewHas("investiments", function (Collection $investiments): bool {
                return $investiments->count() === 10;
            });
    }

    /**
     * deve redirecionar para login
     */
    public function test_create_action_unauthenticated(): void
    {
        $this->get(route("investiments.create"))->assertRedirectToRoute("auth.index");
    }

    /**
     * deve ter status 200 e view investiment.create
     */
    public function test_create_action(): void
    {
        $this->actingAs($this->_user())->get(route("investiments.create"))
            ->assertOk()
            ->assertViewIs("investiment.create");
    }

    /**
     * deve redirecionar para login
     */
    public function test_store_action_unauthenticated(): void
    {
        $this->post(route("investiments.store"))->assertRedirectToRoute("auth.index");
    }

    /**
     * deve redirecionar com erros de validação
     */
    public function test_store_action_without_data(): void
    {
        $this->actingAs($this->_user())->post(route("investiments.store"))
            ->assertFound()
            ->assertSessionHasErrors([
                "title",
                "amount"
            ])
            ->assertSessionDoesntHaveErrors("description");
    }

    /**
     * deve redirecionar com erro no campo title
     */
    public function test_store_action_duplicated_title(): void
    {
        $user = $this->_user();
        $investiment = Investiment::factory()->create([
            "user_id" => $user
        ]);
        $data = Investiment::factory()->make([
            "title" => $investiment->title,
            "amount" => "1.000,00"
        ])->toArray();


        $this->actingAs($user)->post(route("investiments.store"), $data)
            ->assertFound()
            ->assertSessionHasErrors([
                "title",
            ])
            ->assertSessionDoesntHaveErrors([
                "description",
                "amount"
            ]);
    }

    /**
     * deve redirecionar com mensagem de sucesso
     */
    public function test_store_action(): void
    {
        $user = $this->_user();
        $data = Investiment::factory()->make([
            "amount" => "1.000,00"
        ])->toArray();

        $this->actingAs($user)->post(route("investiments.store"), $data)
            ->assertRedirectToRoute("investiments.index")
            ->assertSessionHas("alert_type", "success");
        $this->assertDatabaseHas("investiments", [
            ...$data,
            "user_id" => $user->id,
            "amount" => 1000.00
        ]);
    }

    /**
     * deve redirecionar com mensagem de sucesso
     */
    public function test_store_action_same_title_of_the_other_user(): void
    {
        $user = $this->_user();
        $data = Investiment::factory()->make([
            "amount" => "1.000,00",
            "title" => Investiment::factory()->create()->title
        ])->toArray();

        $this->actingAs($user)->post(route("investiments.store"), $data)
            ->assertRedirectToRoute("investiments.index")
            ->assertSessionHas("alert_type", "success");
        $this->assertDatabaseHas("investiments", [
            ...$data,
            "user_id" => $user->id,
            "amount" => 1000.00
        ]);
    }

    /**
     * deve redirecionar para login
     */
    public function test_edit_action_unauthenticated(): void
    {
        $investiment = Investiment::factory()->create();

        $this->get(route("investiments.edit", $investiment))->assertRedirect(route("auth.index"));
    }

    /**
     * deve ter status 404
     */
    public function test_edit_action_nonexistent(): void
    {
        $this->actingAs($this->_user())->get(route("investiments.edit", 0))
            ->assertNotFound();
    }

    /**
     * deve ter status 404
     */
    public function test_edit_action_is_not_owner(): void
    {
        $investiment = Investiment::factory()->create();

        $this->actingAs($this->_user())->get(route("investiments.edit", $investiment))
            ->assertNotFound();
    }

    /**
     * deve ter status 200 e view investiment.edit
     */
    public function test_edit_action(): void
    {
        $user = $this->_user();
        $investiment = Investiment::factory()->create([
            "user_id" => $user->id
        ]);

        $this->actingAs($user)->get(route("investiments.edit", $investiment))
            ->assertOk()
            ->assertViewIs("investiment.edit")
            ->assertViewHas("investiment");
    }

    /**
     * deve redirecionar para login
     */
    public function test_update_action_unauthenticated(): void
    {
        $investiment = Investiment::factory()->create();

        $this->put(route("investiments.update", $investiment))
            ->assertRedirect(route("auth.index"));
    }

    /**
     * deve ter status 404
     */
    public function test_update_action_is_not_owner(): void
    {
        $investiment = Investiment::factory()->create();
        $data = Investiment::factory()->make([
            "amount" => "5.000,00"
        ])->toArray();

        $this->actingAs($this->_user())->put(route("investiments.update", $investiment), $data)
            ->assertNotFound();
    }

    /**
     * deve redirecionar com erros de validação
     */
    public function test_update_action_without_data(): void
    {
        $user = $this->_user();
        $investiment = Investiment::factory()->create([
            "user_id" => $user
        ]);

        $this->actingAs($user)->put(route("investiments.update", $investiment))
            ->assertFound()
            ->assertSessionHasErrors([
                "title",
                "amount"
            ])
            ->assertSessionDoesntHaveErrors("description");
    }

    /**
     * deve redirecionar com erro no campo title
     */
    public function test_update_action_duplicated_title(): void
    {
        $user = $this->_user();
        $investiment = Investiment::factory()->create([
            "user_id" => $user
        ]);
        $otherInvestiment = Investiment::factory()->create([
            "user_id" => $user,
        ]);
        $data = Investiment::factory()->make([
            "title" => $otherInvestiment->title,
            "amount" => "5.000,00"
        ])->toArray();

        $this->actingAs($user)->put(route("investiments.update", $investiment), $data)
            ->assertFound()
            ->assertSessionHasErrors("title")
            ->assertSessionDoesntHaveErrors([
                "amount",
                "description"
            ]);
    }

    /**
     * deve redireionar com mensagem de sucesso
     */
    public function test_update_action(): void
    {
        $user = $this->_user();
        $investiment = Investiment::factory()->create([
            "user_id" => $user
        ]);
        $data = Investiment::factory()->make([
            "amount" => "5.000,00"
        ])->toArray();

        $this->actingAs($user)->put(route("investiments.update", $investiment), $data)
            ->assertRedirectToRoute("investiments.index")
            ->assertSessionHas("alert_type", "success");
        $this->assertDatabaseHas("investiments", [
            ...$data,
            "id" => $investiment->id,
            "user_id" => $user->id,
            "amount" => 5000.00
        ]);
    }

    /**
     * deve redireionar com mensagem de sucesso
     */
    public function test_update_action_old_title(): void
    {
        $user = $this->_user();
        $investiment = Investiment::factory()->create([
            "user_id" => $user
        ]);
        $data = Investiment::factory()->make([
            "title" => $investiment->title,
            "amount" => "5.000,00"
        ])->toArray();

        $this->actingAs($user)->put(route("investiments.update", $investiment), $data)
            ->assertRedirectToRoute("investiments.index")
            ->assertSessionHas("alert_type", "success");
        $this->assertDatabaseHas("investiments", [
            ...$data,
            "id" => $investiment->id,
            "user_id" => $user->id,
            "amount" => 5000.00
        ]);
    }

    /**
     * deve redireionar com mensagem de sucesso
     */
    public function test_update_action_same_title_of_the_other_user(): void
    {
        $user = $this->_user();
        $investiment = Investiment::factory()->create([
            "user_id" => $user
        ]);
        $data = Investiment::factory()->make([
            "title" => Investiment::factory()->create()->title,
            "amount" => "5.000,00"
        ])->toArray();

        $this->actingAs($user)->put(route("investiments.update", $investiment), $data)
            ->assertRedirectToRoute("investiments.index")
            ->assertSessionHas("alert_type", "success");
        $this->assertDatabaseHas("investiments", [
            ...$data,
            "id" => $investiment->id,
            "user_id" => $user->id,
            "amount" => 5000.00
        ]);
    }

    /**
     * deve redirecionar para login
     */
    public function test_destroy_action_unauthenticated(): void
    {
        $investiment = Investiment::factory()->create();

        $this->delete(route("investiments.destroy", $investiment))
            ->assertRedirect(route("auth.index"));
    }

    /**
     * deve ter status 404
     */
    public function test_destroy_action_nonexistent(): void
    {
        $this->delete(route("investiments.destroy", 0))
            ->assertNotFound();
    }

    /**
     * deve ter status 404
     */
    public function test_destroy_action_is_not_owner(): void
    {
        $investiment = Investiment::factory()->create();

        $this->actingAs($this->_user())->delete(route("investiments.destroy", $investiment))
            ->assertNotFound();
    }

    /**
     * deve redirecionar com mensagem de sucesso
     */
    public function test_destroy_action(): void
    {
        $user = $this->_user();
        $investiment = Investiment::factory()->create([
            "user_id" => $user
        ]);

        $this->actingAs($user)->delete(route("investiments.destroy", $investiment))
            ->assertRedirect(route("investiments.index"))
            ->assertSessionHas("alert_type", "success");
        $this->assertSoftDeleted($investiment);
    }
}
