<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Debtor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DebtorControllerTest extends TestCase
{
    /**
     * deve redirecionar para página de login
     */
    public function test_index_action_unauthenticated(): void
    {
        $this->get(route("debtors.index"))
            ->assertRedirect(route("auth.index"));
    }

    /**
     * deve ter status 200 e view debtor.index
     */
    public function test_index_action(): void
    {
        $this->actingAs($this->_user())->get(route("debtors.index"))
            ->assertOk()
            ->assertViewIs("debtor.index");
    }

    /**
     * deve redirecionara para página de login
     */
    public function test_create_action_unauthenticated(): void
    {
        $this->get(route("debtors.create"))->assertRedirect(route("auth.index"));
    }

    /**
     * deve ter status code 200
     */
    public function test_create_action(): void
    {
        $this->actingAs($this->_user())->get(route("debtors.create"))
            ->assertOk()
            ->assertViewIs("debtor.create");
    }

    /**
     * deve redirecionar para página de login
     */
    public function test_store_action_unauthenticated(): void
    {
        $this->post(route("debtors.store"))->assertRedirect(route("auth.index"));
    }

    public function test_store_action_without_data(): void
    {
        $this->actingAs($this->_user())->post(route("debtors.store"))
            ->assertStatus(302)
            ->assertSessionHasErrors([
                "title",
                "amount"
            ])
            ->assertSessionDoesntHaveErrors([
                "description",
            ]);
    }

    /**
     * deve redirecionar com erros de validação
     */
    public function test_store_action_duplicated_title(): void
    {
        $user = $this->_user();
        $debtor = Debtor::factory()->create([
            "user_id" => $user->id
        ]);
        $data = Debtor::factory()->make([
            "title" => $debtor->title,
            "amount" => "99,30"
        ])->toArray();

        $this->actingAs($user)->post(route("debtors.store"), $data)
            ->assertStatus(302)
            ->assertSessionHasErrors("title")
            ->assertSessionDoesntHaveErrors([
                "amonut",
                "description",
            ]);
    }

    /**
     * deve criar o registro
     */
    public function test_store_action(): void
    {
        $user = $this->_user();
        $data = Debtor::factory()->make([
            "amount" => "10.000,00"
        ])->toArray();

        $this->actingAs($user)->post(route("debtors.store"), $data)
            ->assertRedirect(route("debtors.index"))
            ->assertSessionHas("alert_type", "success");
        $this->assertDatabaseHas("debtors", [
            ...$data,
            "amount" => 10000.00,
            "user_id" => $user->id
        ]);
    }

    /**
     * deve criar registro com título repetido, desde que não seja do usuário
     */
    public function test_store_action_same_title_of_the_other_user(): void
    {
        $user = $this->_user();
        $debtor = Debtor::factory()->create();
        $data = Debtor::factory()->make([
            "title" => $debtor->title,
            "amount" => "99,00"
        ])->toArray();

        $this->actingAs($user)->post(route("debtors.store"), $data)
            ->assertRedirect(route("debtors.index"))
            ->assertSessionHas("alert_type", "success");
        $this->assertDatabaseHas("debtors", [
            ...$data,
            "amount" => 99.00,
            "user_id" => $user->id
        ]);
    }

    /**
     * deve redirecionar para página de login
     */
    public function test_edit_action_unauthenticated(): void
    {
        $debtor = Debtor::factory()->create();

        $this->get(route("debtors.edit", $debtor))
            ->assertRedirect(route("auth.index"));
    }

    /**
     * deve ter status 404
     */
    public function test_edit_action_nonexistent(): void
    {
        $this->actingAs($this->_user())->get(route("debtors.edit", 0))
            ->assertStatus(404);
    }

    /**
     * deve ter status 404
     */
    public function test_edit_action_is_not_owner(): void
    {
        $debtor = Debtor::factory()->create();

        $this->actingAs($this->_user())->get(route("debtors.edit", $debtor))
            ->assertStatus(404);
    }

    /**
     * deve acessar normalmente
     */
    public function test_edit_action(): void
    {
        $user = $this->_user();
        $debtor = Debtor::factory()->create([
            "user_id" => $user->id
        ]);

        $this->actingAs($user)->get(route("debtors.edit", $debtor))
            ->assertOk()
            ->assertViewIs("debtor.edit")
            ->assertViewHas("debtor");
    }

    /**
     * deve redirecionar para página de login
     */
    public function test_update_action_unauthenticated(): void
    {
        $debtor = Debtor::factory()->create();

        $this->put(route("debtors.update", $debtor))
            ->assertRedirect(route("auth.index"));
    }

    /**
     * deve ter status 404
     */
    public function test_update_action_is_not_owner(): void
    {
        $debtor = Debtor::factory()->create();
        $data = Debtor::factory()->make([
            "amount" => "200,00"
        ])->toArray();


        $this->actingAs($this->_user())->put(route("debtors.update", $debtor), $data)
            ->assertNotFound();
    }

    /**
     * deve ter status 404
     *
     */
    public function test_update_nonexistent(): void
    {
        $data = Debtor::factory()->make([
            "amount" => "29,90"
        ])->toArray();

        $this->actingAs($this->_user())->put(route("debtors.update", 0), $data)
            ->assertNotFound();
    }

    /**
     * deve redirecionar com erros de validação
     */
    public function test_update_action_without_data(): void
    {
        $user = $this->_user();
        $debtor = Debtor::factory()->create([
            "user_id" => $user
        ]);

        $this->actingAs($user)->put(route("debtors.update", $debtor))
            ->assertFound()
            ->assertSessionHasErrors([
                "title",
                "amount"
            ])
            ->assertSessionDoesntHaveErrors("description");
    }

    /**
     * deve redirecionar com erro de validação no campo title
     */
    public function test_update_action_duplicated_title(): void
    {
        $user = $this->_user();
        $outherdebtor = Debtor::factory()->create([
            "user_id" => $user
        ]);
        $debtor = Debtor::factory()->create([
            "user_id" => $user
        ]);
        $data = Debtor::factory()->make([
            "title" => $outherdebtor->title,
            "amount" => "29,90"
        ])->toArray();

        $this->actingAs($user)->put(route("debtors.update", $debtor), $data)
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
        $debtor = Debtor::factory()->create([
            "user_id" => $user
        ]);
        $data = Debtor::factory()->make([
            "amount" => "500,00"
        ])->toArray();

        $this->actingAs($user)->put(route("debtors.update", $debtor), $data)
            ->assertRedirect(route("debtors.edit", $debtor))
            ->assertSessionHas("alert_type", "success");
        $this->assertDatabaseHas("debtors", [
            ...$data,
            "user_id" => $user->id,
            "amount" => 500
        ]);
    }

    /**
     * deve redirecionar com mensagem de sucesso
     */
    public function test_update_action_same_title(): void
    {
        $user = $this->_user();
        $debtor = Debtor::factory()->create([
            "user_id" => $user
        ]);
        $data = Debtor::factory()->make([
            "amount" => "500,00",
            "title" => $debtor->title
        ])->toArray();

        $this->actingAs($user)->put(route("debtors.update", $debtor), $data)
            ->assertRedirect(route("debtors.edit", $debtor))
            ->assertSessionHas("alert_type", "success");
        $this->assertDatabaseHas("debtors", [
            ...$data,
            "user_id" => $user->id,
            "amount" => 500
        ]);
    }

    /**
     * deve redirecionar para login
     */
    public function test_destroy_action_unauthenticated(): void
    {
        $debtor = Debtor::factory()->create();

        $this->delete(route("debtors.destroy", $debtor))
            ->assertRedirect(route("auth.index"));
    }

    public function test_destroy_action_is_not_owner(): void
    {
        $debtor = Debtor::factory()->create();

        $this->actingAs($this->_user())->delete(route("debtors.destroy", $debtor))
            ->assertNotFound();
    }

    /**
     * deve ter status 404
     */
    public function test_destroy_action_non_existent(): void
    {
        $this->actingAs($this->_user())->delete(route("debtors.destroy", 0))
            ->assertNotFound();
    }

    /**
     * deve redirecionar com mensagem de sucesso
     */
    public function test_destroy_action(): void
    {
        $user = $this->_user();
        $debtor = Debtor::factory()->create([
            "user_id" => $user
        ]);

        $this->actingAs($user)->delete(route("debtors.destroy", $debtor))
        ->assertRedirect(route("debtors.index"))
            ->assertSessionHas("alert_type", "success");
        $this->assertSoftDeleted($debtor);
    }
}
