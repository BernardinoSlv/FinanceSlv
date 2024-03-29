<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Debt;
use App\Models\Debtor;
use App\Models\Entry;
use App\Models\User;
use App\Repositories\Contracts\EntryRepositoryContract;
use App\Repositories\Contracts\MovementRepositoryContract;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use Tests\TestCase;

class DebtorPaymentControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * deve redirecionar para login
     */
    public function test_index_unauthenticated(): void
    {
        $debtor = Debtor::factory()->create();

        $this->get(route("debtors.payments.index", $debtor))
            ->assertRedirect(route("auth.index"));
    }

    /**
     * deve ter status 404
     */
    public function test_index_action_nonexistent_debtor(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route("debtors.payments.index", 0))
            ->assertNotFound();
    }

    /**
     * deve ter status 403
     */
    public function test_index_action_is_not_owner(): void
    {
        $user = User::factory()->create();
        $debtor = Debtor::factory()->create();

        $this->actingAs($user)->get(route("debtors.payments.index", $debtor))
            ->assertForbidden();
    }

    /**
     * deve ter status 200
     */
    public function test_index_action(): void
    {
        $user = User::factory()->create();
        $debtor = Debtor::factory()->create([
            "user_id" => $user
        ]);

        $this->actingAs($user)->get(route("debtors.payments.index", $debtor))
            ->assertOk()
            ->assertViewIs("debtors.payments.index")
            ->assertViewHas("debtor", function (Debtor $debtor): bool {
                return $debtor->isRelation("entries");
            });
    }

    /**
     * deve redirecionar para login
     */
    public function test_create_action_unauthenticated(): void
    {
        $debtor = Debtor::factory()->create();

        $this->get(route("debtors.payments.create", $debtor))
            ->assertRedirect(route("auth.index"));
    }

    /**
     * deve ter status 404
     */
    public function test_create_action_nonexistent_debtor(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route("debtors.payments.create", 0))
            ->assertNotFound();
    }

    /**
     * deve ter status 403
     */
    public function test_create_action_is_not_owner(): void
    {
        $user = User::factory()->create();
        $debtor = Debtor::factory()->create();

        $this->actingAs($user)->get(route("debtors.payments.create", $debtor))
            ->assertForbidden();
    }

    /**
     * deve ter status 200
     */
    public function test_create_action(): void
    {
        $user = User::factory()->create();
        $debtor = Debtor::factory()->create([
            "user_id" => $user
        ]);

        $this->actingAs($user)->get(route("debtors.payments.create", $debtor))
            ->assertOk()
            ->assertViewIs("debtors.payments.create")
            ->assertViewHas("debtor", function (Debtor $actualDebtor) use ($debtor): bool {
                return $debtor->id === $actualDebtor->id;
            });
    }

    /**
     * deve redirecionar para login
     */
    public function test_store_action_unauthenticated(): void
    {
        $debtor = Debtor::factory()->create();

        $this->post(route("debtors.payments.store", $debtor))
            ->assertRedirect(route("auth.index"));
    }

    /**
     * deve ter status 404
     */
    public function test_store_action_nonexistent(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route("debtors.payments.store", 0))
            ->assertNotFound();
    }

    /**
     * deve redirecionar com erros de validação
     */
    public function test_store_action_without_data(): void
    {
        $user = User::factory()->create();
        $debtor = Debtor::factory()->create([
            "user_id" => $user
        ]);

        $this->actingAs($user)->post(route("debtors.payments.store", $debtor))
            ->assertFound()
            ->assertSessionHasErrors("amount");
    }

    /**
     * deve ter status 403
     */
    public function test_store_action_is_not_owner(): void
    {
        $user = User::factory()->create();
        $debtor = Debtor::factory()->create();
        $data = [
            "amount" => "1.000,00"
        ];

        $this->actingAs($user)->post(route("debtors.payments.store", $debtor), $data)
            ->assertForbidden();
    }

    /**
     * deve redirecionar com mensagem de sucesso
     */
    public function test_store_action(): void
    {
        $user = User::factory()->create();
        $debtor = Debtor::factory()->create(["user_id" => $user]);
        $data = [
            "amount" => "1.000,00"
        ];

        $this->instance(
            EntryRepositoryContract::class,
            Mockery::mock(app(EntryRepositoryContract::class))
                ->shouldReceive("create")
                ->with($user->id, Mockery::on(function (array $attributes): bool {

                    if ($attributes["entryable_type"] !== Debtor::class) {
                        return false;
                    } else if (!is_numeric($attributes["entryable_id"])) {
                        return false;
                    } else if ($attributes["amount"] !== 1000.0) {
                        return false;
                    }
                    return true;
                }))
                ->once()
                ->passthru()
                ->getMock()
        );
        $this->instance(
            MovementRepositoryContract::class,
            Mockery::mock(app(MovementRepositoryContract::class))
                ->shouldReceive("create")
                ->with($user->id, Mockery::on(function (array $attributes): bool {
                    if ($attributes["movementable_type"] !== Entry::class) {
                        return false;
                    } else if (!is_int($attributes["movementable_id"])) {
                        return false;
                    }
                    return true;
                }))
                ->once()
                ->getMock()
        );

        $this->actingAs($user)->post(route("debtors.payments.store", $debtor), $data)
            ->assertRedirect(route("debtors.payments.index", $debtor))
            ->assertSessionHas("alert_type", "success");
        Mockery::close();
    }

    /**
     * deve redirecionar para login
     */
    public function test_edit_action_unauthenticated(): void
    {
        $debtor = Debtor::factory()->create();
        $entry = Entry::factory()->create([
            "entryable_type" => Debtor::class,
            "entryable_id" => $debtor
        ]);

        $this->get(route("debtors.payments.edit", [
            "debtor" => $debtor,
            "entry" => $entry
        ]))
            ->assertRedirectToRoute("auth.index");
    }

    /**
     * deve ter status 404
     */
    public function test_edit_action_nonexistent_debtor_id(): void
    {
        $user = User::factory()->create();
        $entry = Entry::factory()->create([
            "user_id" => $user
        ]);

        $this->actingAs($user)->get(route("debtors.payments.edit", [
            "debtor" => 0,
            "entry" => $entry
        ]))
            ->assertNotFound();
    }

    /**
     * deve ter status 404
     */
    public function test_edit_action_nonexistent_id(): void
    {
        $user = User::factory()->create();
        $debtor = Debtor::factory()->create([
            "user_id" => $user
        ]);

        $this->actingAs($user)->get(route("debtors.payments.edit", [
            "debtor" => $debtor,
            "entry" => 0
        ]))
            ->assertNotFound();
    }

    /**
     * deve ter status 404
     */
    public function test_edit_action_entry_is_not_of_debtor(): void
    {
        $user = User::factory()->create();
        $debtor = Debtor::factory()->create([
            "user_id" => $user
        ]);
        $entry = Entry::factory()->create([
            "user_id" => $user
        ]);

        $this->actingAs($user)->get(route("debtors.payments.edit", [
            "debtor" => $debtor,
            "entry" => $entry
        ]))
            ->assertNotFound();
    }

    /**
     * deve ter status 403
     */
    public function test_edit_action_is_not_owner(): void
    {
        $user = User::factory()->create();
        $debtor = Debtor::factory()->create([
            "user_id" => $user
        ]);
        $entry = Entry::factory()->create([
            "entryable_type" => Debtor::class,
            "entryable_id" => $debtor
        ]);

        $this->actingAs($user)->get(route("debtors.payments.edit", [
            "debtor" => $debtor,
            "entry" => $entry
        ]))
            ->assertForbidden();
    }

    /**
     * deve ter status 200
     */
    public function test_edit_action(): void
    {
        $user = User::factory()->create();
        $debtor = Debtor::factory()->create([
            "user_id" => $user
        ]);
        $entry = Entry::factory()->create([
            "user_id" => $user,
            "entryable_type" => Debtor::class,
            "entryable_id" => $debtor
        ]);

        $this->actingAs($user)->get(route("debtors.payments.edit", [
            "debtor" => $debtor,
            "entry" => $entry
        ]))
            ->assertOk()
            ->assertViewIs("debtors.payments.edit")
            ->assertViewHas(["debtor", "entry"]);
    }
}
