<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Need;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class NeedControllerTest extends TestCase
{
    /**
     * deve redirecionar para login
     */
    public function test_index_action_unauthenticated(): void
    {
        $this->get(route("needs.index"))
            ->assertRedirectToRoute("auth.index");
    }

    /**
     * deve ter status 200
     */
    public function test_index_action(): void
    {
        $this->actingAs($this->_user())->get(route("needs.index"))
            ->assertOk()
            ->assertViewIs("needs.index")
            ->assertViewHas("needs");
    }

    /**
     * deve redirecionar para login
     */
    public function test_create_action_unauthenticated(): void
    {
        $this->get(route("needs.create"))
            ->assertRedirectToRoute("auth.index");
    }

    /**
     * deve ter status 200
     */
    public function test_create_action(): void
    {
        $this->actingAs($this->_user())->get(route("needs.create"))
            ->assertOk()
            ->assertViewIs("needs.create");
    }

    /**
     * deve redirecionar para login
     */
    public function test_store_action_unauthenticated(): void
    {
        $this->post(route("needs.store"))
            ->assertRedirectToRoute("auth.index");
    }

    /**
     * deve redirecionar com erros de validação
     */
    public function test_store_action_without_data(): void
    {
        $this->actingAs($this->_user())->post(route("needs.store"))
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
        $need = Need::factory()->create([
            "user_id" => $user
        ]);
        $data = Need::factory()->make([
            "title" => $need->title,
            "amount" => "50,00"
        ])->toArray();

        $this->actingAs($user)->post(route("needs.store"), $data)
            ->assertFound()
            ->assertSessionHasErrors("title")
            ->assertSessionDoesntHaveErrors([
                "amount",
                "description"
            ]);
    }

    /**
     * deve redirecionar com mensagem de sucesso
     */
    public function test_store_action(): void
    {
        $user = $this->_user();
        $data = Need::factory()->make([
            "amount" => "100,00"
        ])->toArray();

        $this->actingAs($user)->post(route("needs.store"), $data)
            ->assertRedirectToRoute("needs.index")
            ->assertSessionHas("alert_type", "success");
        $this->assertDatabaseHas("needs", [
            ...$data,
            "amount" => 100.00,
            "user_id" => $user->id
        ]);
    }

    /**
     * deve redirecionar com mensagem de sucesso
     */
    public function test_store_action_same_title_of_the_other_user(): void
    {
        $user = $this->_user();
        $otherUserNeed = Need::factory()->create();
        $data = Need::factory()->make([
            "title" => $otherUserNeed->title,
            "amount" => "100,00"
        ])->toArray();

        $this->actingAs($user)->post(route("needs.store"), $data)
            ->assertRedirectToRoute("needs.index")
            ->assertSessionHas("alert_type", "success");
        $this->assertDatabaseHas("needs", [
            ...$data,
            "amount" => 100.00,
            "user_id" => $user->id
        ]);
    }

    /**
     * deve redirecionar para login
     */
    public function test_edit_action_unauthenticated(): void
    {
        $need = Need::factory()->create();

        $this->get(route("needs.edit", $need))
            ->assertRedirectToRoute("auth.index");
    }

    /**
     * deve ter status 404
     */
    public function test_edit_action_nonexistent(): void
    {
        $this->actingAs($this->_user())->get(route("needs.edit", 0))
            ->assertNotFound();
    }

    /**
     * deve ter status 404
     */
    public function test_edit_action_is_not_owner(): void
    {
        $need = Need::factory()->create();

        $this->actingAs($this->_user())->get(route("needs.edit", $need))
            ->assertNotFound();
    }

    /**
     * deve ter status 200
     */
    public function test_edit_action(): void
    {
        $user = $this->_user();
        $need = Need::factory()->create([
            "user_id" => $user
        ]);

        $this->actingAs($user)->get(route("needs.edit", $need))
            ->assertOk()
            ->assertViewIs("needs.edit")
            ->assertViewHas("need");
    }
}
