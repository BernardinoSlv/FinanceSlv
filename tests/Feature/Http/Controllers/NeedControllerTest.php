<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Identifier;
use App\Models\Leave;
use App\Models\Movement;
use App\Models\Need;
use App\Models\User;
use App\Repositories\Contracts\LeaveRepositoryContract;
use App\Repositories\Contracts\MovementRepositoryContract;
use App\Repositories\Contracts\NeedRepositoryContract;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Mockery;
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
            ->assertViewIs("needs.create")
            ->assertViewHas([
                "identifiers"
            ]);
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
            ])
            ->assertSessionDoesntHaveErrors("description");
    }

    /**
     * deve ter erro de validação apenas no campo identifier_id
     */
    public function test_store_action_using_identifier_is_not_owner(): void
    {
        $user = $this->_user();
        $data = Need::factory()->make([
            "identifier_id" => Identifier::factory()->create(),
            "amount" => "100,00"
        ])->toArray();

        $this->actingAs($user)->post(route("needs.store"), $data)
            ->assertFound()
            ->assertSessionHasErrors("identifier_id")
            ->assertSessionDoesntHaveErrors(array_keys(Arr::except($data, "identifier_id")));
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

        $this->instance(
            NeedRepositoryContract::class,
            Mockery::mock(NeedRepositoryContract::class)
                ->shouldReceive("create")
                ->with($user->id, [
                    ...Arr::except($data, ["user_id"]),
                    "amount" => 100.00
                ])
                ->once()
                ->getMock()
        );

        $this->actingAs($user)->post(route("needs.store"), $data)
            ->assertRedirectToRoute("needs.index")
            ->assertSessionHas("alert_type", "success");
        Mockery::close();
    }

    /**
     * deve redirecionar com mensagem de suceso
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
            ->assertRedirect(route("needs.index"))
            ->assertSessionHas("alert_type", "success");
    }

    /**
     * deve redirecionar com mensagem de sucesso
     */
    public function test_store_action_sended_identifier(): void
    {
        $user = $this->_user();
        $data = Need::factory()->make([
            "identifier_id" => Identifier::factory()->create(["user_id" => $user]),
            "amount" => "100,00"
        ])->toArray();

        $this->instance(
            NeedRepositoryContract::class,
            Mockery::mock(NeedRepositoryContract::class)
                ->shouldReceive("create")
                ->with($user->id, [
                    ...Arr::except($data, ["user_id"]),
                    "amount" => 100
                ])
                ->once()
                ->getMock()
        );

        $this->actingAs($user)->post(route("needs.store"), $data)
            ->assertRedirectToRoute("needs.index")
            ->assertSessionHas("alert_type", "success");

        Mockery::close();
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
            ->assertViewHas([
                "need",
                "identifiers"
            ]);
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
        $user = $this->_user();
        $need = Need::factory()->create([]);
        $data = Need::factory()->make([
            "identifier_id" => Identifier::factory()->create(["user_id" => $user]),
            "amount" => "200,00"
        ])->toArray();

        $this->actingAs($user)->put(route("needs.update", $need), $data)
            ->assertNotFound();
    }

    /**
     * deve ter erro de validação apenas no campo identifier_id
     */
    public function test_update_action_using_identifier_is_not_owner(): void
    {
        $user = $this->_user();
        $need = Need::factory()->create([
            "user_id" => $user
        ]);
        $data = Need::factory()->make([
            "identifier_id" => Identifier::factory()->create(),
            "amount" => "120,00"
        ])->toArray();

        $this->actingAs($user)->put(route("needs.update", $need), $data)
            ->assertFound()
            ->assertSessionHasErrors("identifier_id")
            ->assertSessionDoesntHaveErrors(array_keys(Arr::except($data, "identifier_id")));
    }

    /**
     * deve ter erro de validação apenas no campo identifier_id
     */
    public function test_update_action_without_identifier_when_status_is_completed(): void
    {
        $user = $this->_user();
        $need = Need::factory()->create([
            "user_id" => $user
        ]);
        $data = Need::factory()->make([
            "amount" => "120,00",
            "completed" => 1,
        ])->toArray();

        $this->actingAs($user)->put(route("needs.update", $need), $data)
            ->assertFound()
            ->assertSessionHasErrors("identifier_id")
            ->assertSessionDoesntHaveErrors(
                array_keys(Arr::except($data, "identifier_id"))
            );
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
            "identifier_id" => Identifier::factory()->create(["user_id" => $user]),
            "amount" => "120,00",
            "completed" => 1,
        ])->toArray();

        $this->instance(
            NeedRepositoryContract::class,
            Mockery::mock(NeedRepositoryContract::class)
                ->shouldReceive("update")
                ->with($need->id, [
                    ...Arr::except($data, ["user_id"]),
                    "amount" => 120.00
                ])
                ->once()
                ->getMock()
        );

        $this->actingAs($user)->put(route("needs.update", $need), $data)
            ->assertRedirectToRoute("needs.edit", $need)
            ->assertSessionHas("alert_type", "success");
        Mockery::close();
    }

    /**
     * deve redirecionar com mensagem de sucesso
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
            ->assertRedirect(route("needs.edit", $need))
            ->assertSessionHas("alert_type", "success");
    }

    /**
     * deve redirecionar com mensagem de sucesso
     */
    public function test_update_action_to_complete(): void
    {
        $user = $this->_user();
        $need = Need::factory()->create([
            "user_id" => $user
        ]);
        $data = Need::factory()->make([
            "identifier_id" => Identifier::factory()->create(["user_id" => $user]),
            "amount" => "120,00",
            "completed" => 1,
        ])->toArray();

        $this->instance(
            LeaveRepositoryContract::class,
            Mockery::mock(app(LeaveRepositoryContract::class), [new Leave])
                ->shouldReceive("create")
                ->with(
                    $user->id,
                    Mockery::on(function (array $attributes) use ($need): bool {
                        if ($attributes["leaveable_type"] !== Need::class) {
                            return false;
                        } elseif (intval($attributes["leaveable_id"]) !== $need->id) {
                            return false;
                        }
                        return true;
                    })
                )
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
                    } elseif (!is_int($attributes["movementable_id"])) {
                        return false;
                    }
                    return true;
                }))
                ->once()
                ->getMock()
        );

        $this->actingAs($user)->put(route("needs.update", $need), $data)
            ->assertRedirectToRoute("needs.edit", $need)
            ->assertSessionHas("alert_type", "success");
        Mockery::close();
    }

    /**
     * deve redirecionar com mensagem de sucesso
     */
    public function test_update_action_without_identifier_with_status_completed_zero_field(): void
    {
        $user = $this->_user();
        $need = Need::factory()
            ->create([
                "user_id" => $user,
                "completed" => 1
            ]);
        $leave = Leave::factory()->has(Movement::factory())->create([
            "leaveable_type" => Need::class,
            "leaveable_id" => $need
        ]);
        $movement = $leave->movement;
        $data = Need::factory()->make([
            "amount" => "120,00",
            "completed" => 0,
        ])->toArray();

        $this->instance(
            LeaveRepositoryContract::class,
            Mockery::mock(app(LeaveRepositoryContract::class), [new Leave])
                ->shouldReceive("forceDelete")
                ->with($leave->id)
                ->once()
                ->passthru()
                ->getMock()
        );
        $this->instance(
            MovementRepositoryContract::class,
            Mockery::mock(MovementRepositoryContract::class)
                ->shouldReceive("forceDelete")
                ->with($movement->id)
                ->once()
                ->getMock()
        );

        $this->actingAs($user)->put(route("needs.update", $need), $data)
            ->assertRedirectToRoute("needs.edit", $need)
            ->assertSessionHas("alert_type", "success");
        Mockery::close();
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
            "identifier_id" => Identifier::factory()->create(["user_id" => $user]),
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
        $user = $this->_user();
        $need = Need::factory()
            ->create([
                "user_id" => $user,
                "completed" => 1
            ]);
        $leave = Leave::factory()->has(Movement::factory())->create([
            "leaveable_type" => Need::class,
            "leaveable_id" => $need
        ]);
        $movement = $leave->movement;
        $data = Need::factory()->make([
            "amount" => "120,00",
            "completed" => 0,
        ])->toArray();

        $this->instance(
            LeaveRepositoryContract::class,
            Mockery::mock(app(LeaveRepositoryContract::class), [new Leave])
                ->shouldReceive("forceDelete")
                ->with($leave->id)
                ->once()
                ->passthru()
                ->getMock()
        );
        $this->instance(
            MovementRepositoryContract::class,
            Mockery::mock(MovementRepositoryContract::class)
                ->shouldReceive("forceDelete")
                ->with($movement->id)
                ->once()
                ->getMock()
        );

        $this->actingAs($user)->delete(route("needs.destroy", $need))
            ->assertRedirectToRoute("needs.index")
            ->assertSessionHas("alert_type", "success");

        Mockery::close();
    }
}
