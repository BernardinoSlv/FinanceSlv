<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Need;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class NeedControllerTest extends TestCase
{
    use RefreshDatabase;

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

    /**
     * deve redirecionar para login
     */
    public function test_update_action_unauthenticated(): void
    {
        $need = Need::factory()->create();

        $this->put(route("needs.update", $need))
            ->assertRedirectToRoute("auth.index");
    }

    /**
     * deve ter status 404
     */
    public function test_update_action_nonexistent(): void
    {
        $this->actingAs($this->_user())->put(route("needs.update", 0))
            ->assertNotFound();
    }

    /**
     * deve redirecionar com erros de validação
     */
    public function test_update_action_without_data(): void
    {
        $user = $this->_user();
        $need = Need::factory()->create([
            "user_id" => $user
        ]);

        $this->actingAs($user)->put(route("needs.update", $need))
            ->assertFound()
            ->assertSessionHasErrors([
                "title",
                "amount"
            ])
            ->assertSessionDoesntHaveErrors("description");
    }

    /**
     * deve ter status 404
     *
     * @return void
     */
    public function test_update_action_without_permission(): void
    {
        $need = Need::factory()->create([]);
        $data = Need::factory()->make([
            "amount" => "200,00"
        ])->toArray();

        $this->actingAs($this->_user())->put(route("needs.update", $need), $data)
            ->assertNotFound();
    }

    /**
     * deve redirecionar com erro de validação apenas do campo title
     */
    public function test_update_action_duplicated_title(): void
    {
        $user = $this->_user();
        $otherNeed = Need::factory()->create([
            "user_id" => $user
        ]);
        $need = Need::factory()->create([
            "user_id" => $user
        ]);
        $data = Need::factory()->make([
            "title" => $otherNeed->title,
            "amount" => "120,00"
        ])->toArray();

        $this->actingAs($user)->put(route("needs.update", $need), $data)
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
    public function test_update_action(): void
    {
        $user = $this->_user();
        $need = Need::factory()->create([
            "user_id" => $user
        ]);
        $data = Need::factory()->make([
            "amount" => "120,00"
        ])->toArray();

        $this->actingAs($user)->put(route("needs.update", $need), $data)
            ->assertRedirectToRoute("needs.edit", $need)
            ->assertSessionHas("alert_type", "success");
        $this->assertDatabaseHas("needs", [
            ...$data,
            "amount" => 120.00,
            "user_id" => $user->id
        ]);
    }

    /**
     * deve redirecionar com mensagem de sucesso
     */
    public function test_update_action_same_title(): void
    {
        $user = $this->_user();
        $need = Need::factory()->create([
            "user_id" => $user
        ]);
        $data = Need::factory()->make([
            "title" => $need->title,
            "amount" => "120,00"
        ])->toArray();

        $this->actingAs($user)->put(route("needs.update", $need), $data)
            ->assertRedirectToRoute("needs.edit", $need)
            ->assertSessionHas("alert_type", "success");
        $this->assertDatabaseHas("needs", [
            ...$data,
            "amount" => 120.00,
            "user_id" => $user->id
        ]);
    }

    /**
     * deve redirecionar para login
     */
    public function test_destroy_action_unauthenticated(): void
    {
        $need = Need::factory()->create();

        $this->delete(route("needs.destroy", $need))
            ->assertRedirectToRoute("auth.index");
    }

    /**
     * deve ter status 404
     */
    public function test_destroy_action_nonexistent(): void
    {
        $this->actingAs($this->_user())->delete(route("needs.destroy", 0))
            ->assertNotFound();
    }

    /**
     * deve ter status 404
     */
    public function test_destroy_action_without_permission(): void
    {
        $need = Need::factory()->create();

        $this->actingAs($this->_user())->delete(route("needs.destroy", $need))
            ->assertNotFound();
    }

    /**
     * deve redirecionar com mensagem de sucesso
     */
    public function test_destroy_action(): void
    {
        $user = User::factory()->create();
        $need = Need::factory()->create([
            "user_id" => $user
        ]);

        $this->actingAs($user)->delete(route("needs.destroy", $need))
            ->assertRedirectToRoute("needs.index")
            ->assertSessionHas("alert_type", "success");
        $this->assertSoftDeleted($need);
    }
}
