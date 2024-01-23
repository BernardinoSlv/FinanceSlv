<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Tests\TestCase;

class ExpenseControllerTest extends TestCase
{
    /**
     * deve redirecionar para página de login
     */
    public function test_index_unautheticated(): void
    {
        $this->get(route("expenses.index"))->assertRedirect(route("auth.index"));
    }

    /**
     * deve ter status 200 e view expense.index
     */
    public function test_index(): void
    {
        $this->actingAs($this->_user())->get(route("expenses.index"))
            ->assertOk()
            ->assertViewIs("expenses.index")
            ->assertViewHas("expenses");
    }

    /**
     * deve redirecionar para página de login
     */
    public function test_create_unathenticated(): void
    {
        $this->get(route("expenses.create"))->assertRedirect(route("auth.index"));
    }

    /**
     * deve ter status 200 e view expense.create
     */
    public function test_create(): void
    {
        $this->actingAs($this->_user())->get(route("expenses.create"))
            ->assertOk()
            ->assertViewIs("expenses.create");
    }

    /**
     * deve redirecionar para página de login
     */
    public function test_store_unauthenticated(): void
    {
        $this->post(route("expenses.store"))
            ->assertRedirect(route("auth.index"));
    }

    /**
     * deve redirecionar com erros de validação
     */
    public function test_store_without_data(): void
    {
        $this->actingAs($this->_user())->post(route("expenses.store"))
            ->assertStatus(302)
            ->assertSessionHasErrors([
                "title",
                "amount"
            ])
            ->assertSessionDoesntHaveErrors([
                "quantity",
                "description",
                "effetive_at"
            ]);
    }

    /**
     * deve redirecionar com erro no campo title
     */
    public function test_store_duplicated_title(): void
    {
        $user = $this->_user();

        $expense = Expense::factory()->create([
            "user_id" => $user->id
        ]);
        $data = [
            "title" => $expense->title,
            "amount" => "10,00",
        ];

        $this->actingAs($user)->post(route("expenses.store"), $data)
            ->assertStatus(302)
            ->assertSessionHasErrors("title")
            ->assertSessionDoesntHaveErrors([
                "amount",
                "quantity",
                "description",
                "effetive_at"
            ]);
    }

    /**
     * deve redirecionar com mensagem de sucesso
     */
    public function test_store(): void
    {
        $user = $this->_user();
        $data = Expense::factory()->make([
            "amount" => "100,00"
        ])->toArray();

        $this->actingAs($user)->post(route("expenses.store"), $data)
            ->assertRedirect(route("expenses.index"))
            ->assertSessionHas("alert_type", "success");
        $this->assertDatabaseHas("expenses", [
            ...Arr::except($data, "user_id"),
            "user_id" => $user->id,
            "amount" => 100.00
        ]);
    }

    /**
     * deve persistir mesmo se existir mesmo titúlo de despesa, desde que ela seja de outro usuário
     */
    public function test_store_duplicated_title_of_the_other_user(): void
    {
        $user = $this->_user();
        $expenseOtherUser = Expense::factory()->create();
        $data = Expense::factory()->make([
            "title" => $expenseOtherUser->title,
            "amount" => "100,00"
        ])->toArray();

        $this->actingAs($user)->post(route("expenses.store"), $data)
            ->assertRedirect(route("expenses.index"))
            ->assertSessionHas("alert_type", "success");
        $this->assertDatabaseHas("expenses", [
            ...Arr::except($data, "user_id"),
            "user_id" => $user->id,
            "amount" => 100.00
        ]);
    }

    /**
     * deve persistir com o effetive_at sendo o horário atual
     */
    public function test_store_without_effetive_at_input(): void
    {
        $user = $this->_user();
        $data = Expense::factory()->make([
            "amount" => "100,00",
        ])->toArray();
        $data = Arr::except($data, "effetive_at");

        $this->assertArrayNotHasKey("effetive_at", $data);
        $this->actingAs($user)->post(route("expenses.store"), $data)
            ->assertRedirect(route("expenses.index"))
            ->assertSessionHas("alert_type", "success");
        $this->assertDatabaseHas("expenses", [
            ...Arr::except($data, "user_id"),
            "user_id" => $user->id,
            "amount" => 100.00
        ]);
    }

    /**
     * deve redirecionar para página de login
     */
    public function test_edit_action_unauthenticated(): void
    {
        $expense = Expense::factory()->create();

        $this->get(route("expenses.edit", $expense->id))
            ->assertRedirect(route("auth.index"));
    }

    /**
     * deve ter status 404
     */
    public function test_edit_action_nonexistent(): void
    {
        $this->actingAs($this->_user())->get(route("expenses.edit", 0))
            ->assertStatus(404);
    }

    /**
     * deve ter status 404
     */
    public function test_edit_action_is_not_owner(): void
    {
        $expense = Expense::factory()->create();

        $this->actingAs($this->_user())->get(route("expenses.edit", $expense->id))
            ->assertStatus(404);
    }

    /**
     * deve ter status 200 e view expense.edit
     */
    public function test_edit_action(): void
    {
        $user = $this->_user();
        $expense = Expense::factory()->create([
            "user_id" => $user->id
        ]);

        $this->actingAs($user)->get(route("expenses.edit", $expense))
            ->assertOk()
            ->assertViewIs("expenses.edit")
            ->assertViewHas([
                "expense"
            ]);
    }

    /**
     * deve redirecionar para página de login
     */
    public function test_update_action_unauthenticated(): void
    {
        $expense = Expense::factory()->create();

        $this->put(route("expenses.update", $expense))->assertRedirect(route("auth.index"));
    }

    public function test_update_action_nonexistent(): void
    {
        $this->actingAs($this->_user())->put(route("expenses.update", 0))
            ->assertStatus(404);
    }

    /**
     * deve ter status 404
     */
    public function test_update_action_is_not_owner(): void
    {
        $expense = Expense::factory()->create();
        $data = Expense::factory()->make([
            "amount" => "10,00"
        ])->toArray();

        $this->actingAs($this->_user())->put(route("expenses.update", $expense), $data)
            ->assertStatus(404);
    }

    /**
     * deve redirecionar com erros de validação
     */
    public function test_update_action_without_data(): void
    {
        $expense = Expense::factory()->create();

        $this->actingAs($this->_user())->put(route("expenses.update", $expense))
            ->assertStatus(302)
            ->assertSessionHasErrors([
                "title",
                "amount",
            ])
            ->assertSessionDoesntHaveErrors([
                "quantity",
                "description",
                "effetive_at",
            ]);
    }

    /**
     * deve redirecionar com erro no campo title
     */
    public function test_update_action_duplicated_title(): void
    {
        $user = $this->_user();
        $otherExpense = Expense::factory()->create([
            "user_id" => $user->id
        ]);
        $expense = Expense::factory()->create();
        $data = Expense::factory()->make([
            "amount" => "10,00",
            "title" => $otherExpense->title
        ])->toArray();

        $this->actingAs($user)->put(route("expenses.update", $expense), $data)
            ->assertStatus(302)
            ->assertSessionHasErrors("title")
            ->assertSessionDoesntHaveErrors([
                "amount",
                "quantity",
                "description",
                "effetive_at",
            ]);
    }

    /**
     * deve atualizar despesa
     */
    public function test_update_action(): void
    {
        $user = $this->_user();
        $expense = Expense::factory()->create([
            "user_id" => $user->id
        ]);
        $data = Expense::factory()->make([
            "amount" => "99,00",
        ])->toArray();

        $this->actingAs($user)->put(route("expenses.update", $expense), $data)
            ->assertRedirect(route("expenses.edit", $expense))
            ->assertSessionHas("alert_type", "success");
        $this->assertDatabaseHas("expenses", [
            ...$data,
            "amount" => 99,
            "user_id" => $user->id
        ]);
    }

    public function test_update_action_same_title(): void
    {
        $user = $this->_user();
        $expense = Expense::factory()->create([
            "user_id" => $user->id
        ]);
        $data = Expense::factory()->make([
            "amount" => "99,00",
            "title" => $expense->title,
        ])->toArray();

        $this->actingAs($user)->put(route("expenses.update", $expense), $data)
            ->assertRedirect(route("expenses.edit", $expense))
            ->assertSessionHas("alert_type", "success");
        $this->assertDatabaseHas("expenses", [
            ...$data,
            "amount" => 99,
            "user_id" => $user->id
        ]);
    }

    /**
     * deve redirecionar para página de login
     */
    public function test_destroy_action_unauthenticated(): void
    {
        $expense = Expense::factory()->create();

        $this->delete(route("expenses.destroy", $expense))->assertRedirect(route("auth.index"));
    }

    /**
     * deve ter status 404
     */
    public function test_destroy_action_nonexistent(): void
    {
        $this->actingAs($this->_user())->delete(route("expenses.destroy", 0))
            ->assertStatus(404);
    }

    /**
     * deve ter status 404
     */
    public function test_destroy_action_is_not_owner(): void
    {
        $expense = Expense::factory()->create();

        $this->actingAs($this->_user())->delete(route("expenses.destroy", $expense))
            ->assertStatus(404);
    }

    /**
     * deve deletar a despesa
     */
    public function test_destroy_action(): void
    {
        $user = $this->_user();
        $expense = Expense::factory()->create([
            "user_id" => $user->id
        ]);

        $this->actingAs($user)->delete(route("expenses.destroy", $expense))
            ->assertRedirect(route("expenses.index"))
            ->assertSessionHas("alert_type", "success");
        $this->assertSoftDeleted($expense);
    }
}
