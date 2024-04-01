<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Identifier;
use App\Models\Investiment;
use App\Models\Leave;
use App\Models\User;
use App\Repositories\Contracts\InvestimentRepositoryContract;
use App\Repositories\Contracts\LeaveRepositoryContract;
use App\Repositories\Contracts\MovementRepositoryContract;
use App\Repositories\LeaveRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Mockery;
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
     * deve ter status 200 e view investiments.index
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
            ->assertViewIs("investiments.index")
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
     * deve ter status 200 e view investiments.create
     */
    public function test_create_action(): void
    {
        $this->actingAs($this->_user())->get(route("investiments.create"))
            ->assertOk()
            ->assertViewIs("investiments.create")
            ->assertViewHas("identifiers");
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
            ])
            ->assertSessionDoesntHaveErrors("description");
    }

    /**
     * deve ter erro de validação apenas no campo identifier_id
     */
    public function test_store_action_using_identifier_is_not_owner(): void
    {
        $user = $this->_user();
        $data = Investiment::factory()->make([
            "identifier_id" => Identifier::factory()->create(),
        ])->toArray();

        $this->actingAs($user)->post(route("investiments.store"), $data)
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
        $data = Investiment::factory()->make([
            "identifier_id" => Identifier::factory()->create(["user_id" => $user]),
        ])->toArray();

        $this->instance(
            InvestimentRepositoryContract::class,
            Mockery::mock(app(InvestimentRepositoryContract::class))
                ->shouldReceive("create")
                ->with($user->id, [
                    ...Arr::only($data, ["identifier_id", "title", "description"]),
                ])
                ->passthru()
                ->once()
                ->getMock()
        );
        $this->instance(
            LeaveRepositoryContract::class,
            Mockery::mock(app(LeaveRepositoryContract::class))
                ->shouldReceive("create")
                ->withArgs(function (int $userId, array $attributes) use ($user): bool {
                    if ($userId !== $user->id)
                        return false;
                    elseif (count($attributes) !== 2)
                        return false;
                    elseif ($attributes["leaveable_type"] !== Investiment::class)
                        return false;
                    elseif (!array_key_exists("leaveable_id", $attributes))
                        return false;
                    return true;
                })
                ->passthru()
                ->once()
                ->getMock()
        );
        $this->instance(
            MovementRepositoryContract::class,
            Mockery::mock(MovementRepositoryContract::class)
                ->shouldReceive("create")
                ->withArgs(function (int $userId, array $attributes) use ($user): bool {
                    if ($userId !== $user->id)
                        return false;
                    elseif (count($attributes) !== 2)
                        return false;
                    elseif ($attributes["movementable_type"] !== Leave::class)
                        return false;
                    elseif (!array_key_exists("movementable_id", $attributes))
                        return false;
                    return true;
                })
                ->once()
                ->getMock()
        );

        $this->actingAs($user)->post(route("investiments.store"), $data)
            ->assertRedirectToRoute("investiments.index")
            ->assertSessionHas("alert_type", "success");
        Mockery::close();
    }

    /**
     * deve persistir com sucesso
     */
    public function test_store_action_duplicated_title(): void
    {
        $user = $this->_user();
        $investiment = Investiment::factory()->create([
            "user_id" => $user
        ]);
        $data = Investiment::factory()->make([
            "identifier_id" => Identifier::factory()->create(["user_id" => $user]),
            "title" => $investiment->title,
        ])->toArray();


        $this->actingAs($user)->post(route("investiments.store"), $data)
            ->assertRedirectToRoute("investiments.index")
            ->assertSessionHas("alert_type", "success");
    }

    /**
     * deve redirecionar com mensagem de sucesso
     */
    public function test_store_action_same_title_of_the_other_user(): void
    {
        $user = $this->_user();
        $data = Investiment::factory()->make([
            "identifier_id" => Identifier::factory()->create(["user_id" => $user]),
            "title" => Investiment::factory()->create()->title
        ])->toArray();

        $this->actingAs($user)->post(route("investiments.store"), $data)
            ->assertRedirectToRoute("investiments.index")
            ->assertSessionHas("alert_type", "success");
        $this->assertDatabaseHas("investiments", [
            ...$data,
            "user_id" => $user->id,
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
     * deve ter status 200 e view investiments.edit
     */
    public function test_edit_action(): void
    {
        $user = $this->_user();
        $investiment = Investiment::factory()->create([
            "user_id" => $user->id
        ]);

        $this->actingAs($user)->get(route("investiments.edit", $investiment))
            ->assertOk()
            ->assertViewIs("investiments.edit")
            ->assertViewHas(["investiment", "identifiers"]);
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
        $user = $this->_user();
        $investiment = Investiment::factory()->create();
        $data = Investiment::factory()->make([
            "identifier_id" => Identifier::factory()->create(["user_id" => $user]),
        ])->toArray();

        $this->actingAs($user)->put(route("investiments.update", $investiment), $data)
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
            ])
            ->assertSessionDoesntHaveErrors("description");
    }

    /**
     * deve redirecionar com erro de validação apenas no campo identifier_id
     */
    public function test_update_action_using_identifier_is_not_owner(): void
    {
        $user = $this->_user();
        $investiment = Investiment::factory()->create([
            "user_id" => $user
        ]);
        $data = Investiment::factory()->make([
            "identifier_id" => Identifier::factory()->create(),
        ])->toArray();

        $this->actingAs($user)->put(route("investiments.update", $investiment), $data)
            ->assertFound()
            ->assertSessionHasErrors("identifier_id")
            ->assertSessionDoesntHaveErrors(array_keys(Arr::except($data, "identifier_id")));
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
            "identifier_id" => Identifier::factory()->create(["user_id" => $user]),
        ])->toArray();

        $this->actingAs($user)->put(route("investiments.update", $investiment), $data)
            ->assertRedirectToRoute("investiments.index")
            ->assertSessionHas("alert_type", "success");
        $this->assertDatabaseHas("investiments", [
            ...$data,
            "id" => $investiment->id,
            "user_id" => $user->id,
        ]);
    }

    /**
     * deve atualizar registro
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
            "identifier_id" => Identifier::factory()->create(["user_id" => $user]),
            "title" => $otherInvestiment->title,
        ])->toArray();

        $this->actingAs($user)->put(route("investiments.update", $investiment), $data)
            ->assertRedirectToRoute("investiments.index")
            ->assertSessionHas("alert_type", "success");
        $this->assertDatabaseHas("investiments", [
            ...$data,
            "id" => $investiment->id,
            "user_id" => $user->id,
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
            "identifier_id" => Identifier::factory()->create(["user_id" => $user]),
            "title" => $investiment->title,
        ])->toArray();

        $this->actingAs($user)->put(route("investiments.update", $investiment), $data)
            ->assertRedirectToRoute("investiments.index")
            ->assertSessionHas("alert_type", "success");
        $this->assertDatabaseHas("investiments", [
            ...$data,
            "id" => $investiment->id,
            "user_id" => $user->id,
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
            "identifier_id" => Identifier::factory()->create(["user_id" => $user]),
            "title" => Investiment::factory()->create()->title,
        ])->toArray();

        $this->actingAs($user)->put(route("investiments.update", $investiment), $data)
            ->assertRedirectToRoute("investiments.index")
            ->assertSessionHas("alert_type", "success");
        $this->assertDatabaseHas("investiments", [
            ...$data,
            "id" => $investiment->id,
            "user_id" => $user->id,
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
        $leave = Leave::factory()->create([
            "leaveable_type" => Investiment::class,
            "leaveable_id" => $investiment->id
        ]);

        $this->instance(
            InvestimentRepositoryContract::class,
            Mockery::mock(InvestimentRepositoryContract::class)
                ->shouldReceive("delete")
                ->with($investiment->id)
                ->once()
                ->getMock()
        );
        $this->instance(
            LeaveRepositoryContract::class,
            Mockery::mock(LeaveRepositoryContract::class)
                ->shouldReceive("deletePolymorph")
                ->with(Investiment::class, $investiment->id)
                ->once()
                ->getMock()
        );
        $this->instance(
            MovementRepositoryContract::class,
            Mockery::mock(MovementRepositoryContract::class)
                ->shouldReceive("deletePolymorph")
                ->withArgs(function (string $movementableType, int $movementableId): bool {
                    if ($movementableType !== Leave::class)
                        return false;
                    return true;
                })
                ->once()
                ->getMock()
        );


        $this->actingAs($user)->delete(route("investiments.destroy", $investiment))
            ->assertRedirect(route("investiments.index"))
            ->assertSessionHas("alert_type", "success");
        Mockery::close();
    }
}
