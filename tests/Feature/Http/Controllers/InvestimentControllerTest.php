<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Investiment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class InvestimentControllerTest extends TestCase
{
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
}
