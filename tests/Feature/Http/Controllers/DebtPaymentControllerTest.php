<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Debt;
use App\Models\Leave;
use App\Models\Movement;
use App\Models\User;
use App\Repositories\Contracts\LeaveRepositoryContract;
use App\Repositories\Contracts\MovementRepositoryContract;
use App\Repositories\LeaveRepository;
use App\Repositories\MovementRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Mockery\Mock;
use Tests\TestCase;

class DebtPaymentControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * deve redirecionar para login
     */
    public function test_index_action_unauthenticated(): void
    {
        $debt = Debt::factory()->create();

        $this->get(route("debts.payments.index", $debt))
            ->assertRedirectToRoute("auth.index");
    }

    /**
     * deve ter status 404
     */
    public function test_index_action_nonexistent_debt(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route("debts.payments.index", 0))
            ->assertNotFound();
    }

    /**
     * deve ter status 404
     */
    public function test_index_action_with_debt_is_not_owner(): void
    {
        $user = User::factory()->create();
        $debt = Debt::factory()->create();

        $this->actingAs($user)->get(route("debts.payments.index", $debt))
            ->assertNotFound();
    }

    /**
     * deve ter status 200
     */
    public function test_index_action(): void
    {
        $user = User::factory()->create();
        $debt = Debt::factory()->create(["user_id" => $user]);

        $this->actingAs($user)->get(route("debts.payments.index", $debt))
            ->assertOk()
            ->assertViewIs("debts.payments.index")
            ->assertViewHas("debt", function (Debt $actualDebt) use ($debt): bool {
                if ($actualDebt->id !== $debt->id) {
                    return false;
                } else if (!$actualDebt->relationLoaded("leaves")) {
                    return false;
                }
                return true;
            });
    }

    /**
     * deve redirecionar para login
     */
    public function test_create_action_unauthenticated(): void
    {
        $debt = Debt::factory()->create();

        $this->get(route("debts.payments.create", $debt))
            ->assertRedirectToRoute("auth.index");
    }

    /**
     * deve ter status 404
     */
    public function test_create_action_nonexistent_debt(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route("debts.payments.create", $user))
            ->assertNotFound();
    }

    /**
     * deve ter status 404
     */
    public function test_create_action_with_debt_is_not_owner(): void
    {
        $user = User::factory()->create();
        $debt = Debt::factory()->create();

        $this->actingAs($user)->get(route("debts.payments.create", $debt))
            ->assertNotFound();
    }

    /**
     * deve ter status 200
     */
    public function test_create_action(): void
    {
        $user = User::factory()->create();
        $debt = Debt::factory()->create(["user_id" => $user]);

        $this->actingAs($user)->get(route("debts.payments.create", $debt))
            ->assertOk()
            ->assertViewIs("debts.payments.create")
            ->assertViewHas("debt");
    }

    /**
     * deve redirecionar para login
     */
    public function test_store_action_unauthenticated(): void
    {
        $debt = Debt::factory()->create();

        $this->post(route("debts.payments.store", $debt))
            ->assertRedirectToRoute("auth.index");
    }

    /**
     * deve ter status 404
     */
    public function test_store_action_nonexistent_debt(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route("debts.payments.store", 0))
            ->assertNotFound();
    }

    /**
     * deve redirecionar com erros de validação
     */
    public function test_store_action_without_data(): void
    {
        $user = User::factory()->create();
        $debt = Debt::factory()->create(["user_id" => $user]);

        $this->actingAs($user)->post(route("debts.payments.store", $debt))
            ->assertFound()
            ->assertSessionHasErrors("amount");
    }

    public function test_store_action_with_debt_is_not_owner(): void
    {
        $user = User::factory()->create();
        $debt = Debt::factory()->create();
        $data = [
            "amount" => "100,00"
        ];

        $this->actingAs($user)->post(route("debts.payments.store", $debt), $data)
            ->assertNotFound();
    }

    /**
     * deve redirecionar com mensagem de sucesso
     */
    public function test_store_action(): void
    {
        $user = User::factory()->create();
        $debt = Debt::factory()->create(["user_id" => $user]);
        $data = [
            "amount" => "100,00"
        ];

        $this->instance(
            LeaveRepositoryContract::class,
            Mockery::mock(app(LeaveRepositoryContract::class))
                ->shouldReceive("create")
                ->with($user->id, [
                    "leaveable_type" => Debt::class,
                    "leaveable_id" => $debt->id,
                    "amount" => 100.00
                ])
                ->once()
                ->passthru()
                ->getMock()
        );
        $this->instance(
            MovementRepositoryContract::class,
            Mockery::mock(MovementRepositoryContract::class)
                ->shouldReceive("create")
                ->with($user->id, Mockery::on(function (array $attributes): bool {
                    if ($attributes["movementable_type"] !== Leave::class) {
                        return false;
                    } else if (!is_int($attributes["movementable_id"])) {
                        return false;
                    }
                    return true;
                }))
                ->once()
                ->getMock()
        );

        $this->actingAs($user)->post(route("debts.payments.store", $debt), $data)
            ->assertRedirect(route("debts.payments.index", $debt))
            ->assertSessionHas([
                "alert_type" => "success"
            ]);
        Mockery::close();
    }

    /**
     * deve redirecionar com mensagem de sucesso
     */
    public function test_store_action_sended_images(): void
    {
        $this->markTestIncomplete("...");
        Storage::fake("public");

        $user = User::factory()->create();
        $debt = Debt::factory()->create(["user_id" => $user]);
        $data = [
            "amount" => "100,00",
            "files[]" => [
                UploadedFile::fake()->image("test.png"),
                UploadedFile::fake()->image("test2.jpg")
            ]
        ];

        $this->instance(
            LeaveRepositoryContract::class,
            Mockery::mock(app(LeaveRepositoryContract::class))
                ->shouldReceive("create")
                ->with($user->id, [
                    "leaveable_type" => Debt::class,
                    "leaveable_id" => $debt->id,
                    "amount" => 100.00
                ])
                ->once()
                ->passthru()
                ->getMock()
        );
        $this->instance(
            MovementRepositoryContract::class,
            Mockery::mock(MovementRepositoryContract::class)
                ->shouldReceive("create")
                ->with($user->id, Mockery::on(function (array $attributes): bool {
                    if ($attributes["movementable_type"] !== Leave::class) {
                        return false;
                    } else if (!is_int($attributes["movementable_id"])) {
                        return false;
                    }
                    return true;
                }))
                ->once()
                ->getMock()
        );

        $this->actingAs($user)->post(route("debts.payments.store", $debt), $data)
            ->assertRedirect(route("debts.payments.index", $debt))
            ->assertSessionHas([
                "alert_type" => "success"
            ]);
        Mockery::close();
    }

    /**
     * deve redirecionar para login
     */
    public function test_edit_action_unauthenticated(): void
    {
        $debt = Debt::factory()->create();
        $leave = Leave::factory()->create([
            "leaveable_type" => Debt::class,
            "leaveable_id" => $debt,
            "amount" => 1000
        ]);

        $this->get(route("debts.payments.edit", [
            "debt" => $debt,
            "leave" => $leave
        ]))
            ->assertRedirect(route("auth.index"));
    }

    /**
     * deve ter status 404
     */
    public function test_edit_action_leave_is_not_of_the_debt(): void
    {
        $user = User::factory()->create();
        $debt = Debt::factory()->create([
            "user_id" => $user
        ]);
        $leave = Leave::factory()->create([
            "user_id" => $user
        ]);

        $this->actingAs($user)->get(route("debts.payments.edit", [
            "debt" => $debt,
            "leave" => $leave
        ]))
            ->assertNotFound();
    }

    /**
     * deve ter status 403
     */
    public function test_edit_action_is_not_owner(): void
    {
        $user = User::factory()->create();
        $debt = Debt::factory()->create([]);
        $leave = Leave::factory()->create([
            "leaveable_type" => Debt::class,
            "leaveable_id" => $debt
        ]);

        $this->actingAs($user)->get(route("debts.payments.edit", [
            "debt" => $debt,
            "leave" => $leave
        ]))
            ->assertForbidden();
    }

    /**
     * deve ter status 200
     */
    public function test_edit_action(): void
    {
        $user = User::factory()->create();
        $debt = Debt::factory()->create([
            "user_id" => $user
        ]);
        $leave = Leave::factory()->create([
            "leaveable_type" => Debt::class,
            "leaveable_id" => $debt,
            "user_id" => $user
        ]);

        $this->actingAs($user)->get(route("debts.payments.edit", [
            "debt" => $debt,
            "leave" => $leave
        ]))
            ->assertOk()
            ->assertViewIs("debts.payments.edit")
            ->assertViewHas(["debt", "leave"]);
    }

    /**
     * deve redirecionar para login
     */
    public function test_update_action_unauthenticated(): void
    {
        $debt = Debt::factory()->create();
        $leave = Leave::factory()->create([
            "leaveable_type" => Debt::class,
            "leaveable_id" => $debt
        ]);

        $this->put(route("debts.payments.update", [
            "debt" => $debt,
            "leave" => $leave
        ]))
            ->assertRedirectToRoute("auth.index");
    }

    /**
     * deve ter status 403
     */
    public function test_update_action_is_not_owner(): void
    {
        $user = User::factory()->create();
        $debt = Debt::factory()->hasLeaves(1)->create();
        $leave = $debt->leaves->first();
        $data = [
            "amount" => "100,00"
        ];

        $this->actingAs($user)->put(route("debts.payments.update", [
            "debt" => $debt,
            "leave" => $leave
        ]), $data)
            ->assertForbidden();
    }

    /**
     * deve ter status 404
     */
    public function test_update_action_leave_is_not_of_debt(): void
    {
        $user = User::factory()->create();
        $debt = Debt::factory()->create([
            "user_id" => $user
        ]);
        $leave = Leave::factory()->create([
            "user_id" => $user
        ]);

        $this->actingAs($user)->put(route("debts.payments.update", [
            "debt" => $debt,
            "leave" => $leave
        ]))
            ->assertNotFound();
    }

    /**
     * deve redirecionar com erros de validação
     */
    public function test_update_action_without_data(): void
    {
        $user = User::factory()->create();
        $debt = Debt::factory()->hasLeaves(1, ["user_id" => $user])->create([
            "user_id" => $user
        ]);
        $leave = $debt->leaves->first();

        $this->actingAs($user)->put(route("debts.payments.update", [
            "debt" => $debt,
            "leave" => $leave
        ]))
            ->assertFound()
            ->assertSessionHasErrors("amount");
    }
    /**
     * deve redirecionar com mensagem de sucesso
     */
    public function test_update_action(): void
    {
        $user = User::factory()->create();
        $debt = Debt::factory()->hasLeaves(1, ["user_id" => $user])->create([
            "user_id" => $user
        ]);
        $leave = $debt->leaves->first();
        $data = [
            "amount" => "100,00"
        ];

        $this->instance(
            LeaveRepositoryContract::class,
            Mockery::mock(LeaveRepositoryContract::class)
                ->shouldReceive("update")
                ->with($leave->id, [
                    "amount" => 100.00
                ])
                ->once()
                ->getMock()
        );

        $this->actingAs($user)->put(route("debts.payments.update", [
            "debt" => $debt,
            "leave" => $leave
        ]), $data)
            ->assertRedirect(route("debts.payments.edit", [
                "debt" => $debt,
                "leave" => $leave
            ]))
            ->assertSessionHas("alert_type", "success");
        Mockery::close();
    }

    /**
     * deve redirecionar para login
     */
    public function test_destroy_action_unauthenticated(): void
    {
        $debt = Debt::factory()->hasLeaves(1)->create([]);
        $leave = $debt->leaves->first();

        $this->delete(route("debts.payments.destroy", [
            "debt" => $debt,
            "leave" => $leave
        ]))
            ->assertRedirect(route("auth.index"));
    }

    /**
     * deve ter status 404
     */
    public function test_destroy_action_leave_is_not_debt(): void
    {
        $user = User::factory()->create();
        $debt = Debt::factory()->create(["user_id" => $user]);
        $leave = Leave::factory()->create(["user_id" => $user]);

        $this->actingAs($user)->delete(route("debts.payments.destroy", [
            "debt" => $debt,
            "leave" => $leave
        ]))
            ->assertNotFound();
    }

    /**
     * deve ter status 403
     */
    public function test_destroy_action_is_not_owner(): void
    {
        $user = User::factory()->create();
        $debt = Debt::factory()->hasLeaves(1)->create();
        $leave = $debt->leaves->first();

        $this->actingAs($user)->delete(route("debts.payments.destroy", [
            "debt" => $debt,
            "leave" => $leave
        ]))
            ->assertForbidden();
    }

    /**
     * deve redirecionar com mensagem de sucesso
     */
    public function test_destroy_action(): void
    {
        $user = User::factory()->create();
        $debt = Debt::factory()->hasLeaves(1, ["user_id" => $user])
            ->create(["user_id" => $user]);
        $leave = $debt->leaves->first();
        $movement = Movement::factory()->create([
            "movementable_type" => Leave::class,
            "movementable_id" => $leave
        ]);

        $this->instance(
            LeaveRepositoryContract::class,
            Mockery::mock(LeaveRepositoryContract::class)
                ->shouldReceive("delete")
                ->with($leave->id)
                ->once()
                ->getMock()
        );
        $this->instance(
            MovementRepositoryContract::class,
            Mockery::mock(MovementRepositoryContract::class)
                ->shouldReceive("delete")
                ->with($movement->id)
                ->once()
                ->getMock()
        );

        $this->actingAs($user)->delete(route("debts.payments.destroy", [
            "debt" => $debt,
            "leave" => $leave
        ]))
            ->assertRedirect(route("debts.payments.index", $debt))
            ->assertSessionHas("alert_type", "success");
        Mockery::close();
    }
}
