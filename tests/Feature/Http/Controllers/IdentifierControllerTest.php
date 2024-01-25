<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Identifier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class IdentifierControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * deve redirecionar para login
     */
    public function test_index_action_unauthenticated(): void
    {
        $this->get(route("identifiers.index"))
            ->assertRedirectToRoute("auth.index");
    }

    /**
     * deve ter status 200
     */
    public function test_index_action(): void
    {
        $this->actingAs($this->_user())->get(route("identifiers.index"))
            ->assertOk()
            ->assertViewIs("identifiers.index")
            ->assertViewHas("identifiers");
    }

    /**
     * deve redirecionar para login
     */
    public function test_create_action_unauthenticated(): void
    {
        $this->get(route("identifiers.create"))->assertRedirectToRoute("auth.index");
    }

    /**
     * deve ter status 200
     */
    public function test_create_action(): void
    {
        $this->actingAs($this->_user())->get(route("identifiers.create"))
            ->assertOk()
            ->assertViewIs("identifiers.create");
    }

    /**
     * deve redirecionar para login
     */
    public function test_store_action_unauthenticated(): void
    {
        $this->post(route("identifiers.store"))->assertRedirectToRoute("auth.index");
    }

    /**
     * deve redirecionar com erros de validação
     */
    public function test_store_action_without_data(): void
    {
        $this->actingAs($this->_user())->post(route("identifiers.store"))
            ->assertFound()
            ->assertSessionHasErrors([
                "name",
            ])
            ->assertSessionDoesntHaveErrors([
                "phone",
                "avatar",
                "description"
            ]);
    }

    /**
     * deve retornar erro de validação apenas no campo phone
     *
     * @dataProvider invalidPhonesProvider
     */
    public function test_store_action_invalid_phone_format(string $phone): void
    {
        $data = Identifier::factory()->make([
            "phone" => $phone
        ])->toArray();

        $this->actingAs($this->_user())->post(route("identifiers.store"), $data)
            ->assertFound()
            ->assertSessionHasErrors("phone")
            ->assertSessionDoesntHaveErrors([
                "name",
                "avatar",
                "description"
            ]);
    }

    /**
     * deve redirecionar com erro de validação apenas no campo name
     */
    public function test_store_action_duplicated_name(): void
    {
        $user = $this->_user();
        $identifier = Identifier::factory()->create([
            "user_id" => $user
        ]);
        $data = Identifier::factory()->make([
            "name" => $identifier->name
        ])->toArray();

        $this->actingAs($user)->post(route("identifiers.store"), $data)
            ->assertFound()
            ->assertSessionHasErrors("name")
            ->assertSessionDoesntHaveErrors([
                "phone",
                "avatar",
                "description"
            ]);
    }

    /**
     * deve redirecionar com mensagem de sucesso
     */
    public function test_store_action(): void
    {
        $user = $this->_user();
        $data = Identifier::factory()->make()->toArray();

        $this->actingAs($user)->post(route("identifiers.store"), $data)
            ->assertRedirectToRoute("identifiers.index")
            ->assertSessionHas("alert_type", "success");
        $this->assertDatabaseHas("identifiers", [
            ...$data,
            "user_id" => $user->id
        ]);
    }

    /**
     * deve salvar o avatar
     */
    public function test_store_action_send_avatar(): void
    {
        Storage::fake();

        $user = $this->_user();
        $data = Identifier::factory()->make([
            "avatar" => UploadedFile::fake()->image("test.jpg")
        ])->toArray();

        $this->actingAs($user)->post(route("identifiers.store"), $data)
            ->assertRedirectToRoute("identifiers.index")
            ->assertSessionHas("alert_type", "success");

        $identifier = Identifier::query()->where([
            ...Arr::except($data, "avatar"),
            "user_id" => $user->id
        ])->first();
        $this->assertNotNull($identifier->avatar);
        $this->assertStringEndsWith(".jpg", $identifier->avatar);
    }

    /**
     * deve redirecionar com mensagem de sucesso
     */
    public function test_store_action_same_name_other_user(): void
    {
        $user = $this->_user();
        $data = Identifier::factory()->make([
            "name" => Identifier::factory()->create()->name
        ])->toArray();

        $this->actingAs($user)->post(route("identifiers.store"), $data)
            ->assertRedirectToRoute("identifiers.index")
            ->assertSessionHas("alert_type", "success");
        $this->assertDatabaseHas("identifiers", [
            ...$data,
            "user_id" => $user->id
        ]);
    }

    /**
     * deve redirecionar para login
     */
    public function test_edit_action_unauthenticated(): void
    {
        $identifier = Identifier::factory()->create();

        $this->get(route("identifiers.edit", $identifier))
            ->assertRedirectToRoute("auth.index");
    }

    /**
     * deve ter status 404
     */
    public function test_edit_action_nonexistent(): void
    {
        $this->actingAs($this->_user())->get(route("identifiers.edit", 0))
            ->assertNotFound();
    }

    /**
     * deve ter status 404
     */
    public function test_edit_action_is_not_owner(): void
    {
        $identifier = Identifier::factory()->create();

        $this->actingAs($this->_user())->get(route("identifiers.edit", $identifier))
            ->assertNotFound();
    }

    /**
     * deve ter status 404
     */
    public function test_edit_action(): void
    {
        $user = $this->_user();
        $identifier = Identifier::factory()->create([
            "user_id" => $user
        ]);

        $this->actingAs($user)->get(route("identifiers.edit", $identifier))
            ->assertOk()
            ->assertViewIs("identifiers.edit")
            ->assertViewHas("identifier");
    }

    /**
     * deve redirecionar para login
     */
    public function test_update_action_unauthenticated(): void
    {
        $identifier = Identifier::factory()->create();

        $this->put(route("identifiers.update", $identifier))
            ->assertRedirect(route("auth.index"));
    }

    /**
     * deve ter status 404
     */
    public function test_update_action_nonexistent(): void
    {
        $this->actingAs($this->_user())->put(route("identifiers.update", 0))
            ->assertNotFound();
    }

    /**
     * deve redirecionar com erros de validação
     */
    public function test_update_action_without_data(): void
    {
        $user = User::factory()->create();
        $identifier = Identifier::factory()->create([
            "user_id" => $user
        ]);

        $this->actingAs($user)->put(route("identifiers.update", $identifier))
            ->assertFound()
            ->assertSessionHasErrors("name")
            ->assertSessionDoesntHaveErrors([
                "avatar",
                "phone",
                "description"
            ]);
    }

    /**
     * deve ter status 404
     */
    public function test_update_action_is_not_owner(): void
    {
        $identifier = Identifier::factory()->create();
        $data = Identifier::factory()->make()->toArray();

        $this->actingAs($this->_user())->put(route("identifiers.update", $identifier), $data)
            ->assertNotFound();
    }

    /**
     * deve redirecionar com erro de validação apenas no campo name
     */
    public function test_update_action_duplicated_name(): void
    {
        $user = $this->_user();
        $identifier = Identifier::factory()->create([
            "user_id" => $user
        ]);
        $data = Identifier::factory()->make([
            "name" => Identifier::factory()->create(["user_id" => $user])->name
        ])->toArray();

        $this->actingAs($user)->put(route("identifiers.update", $identifier), $data)
            ->assertFound()
            ->assertSessionHasErrors("name")
            ->assertSessionDoesntHaveErrors([
                "avatar",
                "phone",
                "description"
            ]);
    }

    /**
     * deve redirecionar com erro de validação apenas no campo phone
     *
     * @dataProvider invalidPhonesProvider
     */
    public function test_update_action_invalid_phone(string $phone): void
    {
        $user = $this->_user();
        $identifier = Identifier::factory()->create([
            "user_id" => $user
        ]);
        $data = Identifier::factory()->make([
            "phone" => $phone
        ])->toArray();

        $this->actingAs($user)->put(route("identifiers.update", $identifier), $data)
            ->assertFound()
            ->assertSessionHasErrors("phone")
            ->assertSessionDoesntHaveErrors([
                "avatar",
                "name",
                "description"
            ]);
    }

    /**
     * deve redirecionar com mensagem de sucesso
     */
    public function test_update_action(): void
    {
        $user = $this->_user();
        $identifier = Identifier::factory()->create([
            "user_id" => $user
        ]);
        $data = Identifier::factory()->make()->toArray();

        $this->actingAs($user)->put(route("identifiers.update", $identifier), $data)
            ->assertRedirectToRoute("identifiers.edit", $identifier)
            ->assertSessionHas("alert_type", "success");
        $this->assertDatabaseHas("identifiers", [
            ...$data,
            "id" => $identifier->id,
            "user_id" => $user->id
        ]);
    }

    /**
     * deve redirecionar com mensagem de sucesso
     */
    public function test_update_action_same_name_other_user(): void
    {
        $user = $this->_user();
        $identifier = Identifier::factory()->create([
            "user_id" => $user
        ]);
        $data = Identifier::factory()->make([
            "name" => Identifier::factory()->create()->name
        ])->toArray();

        $this->actingAs($user)->put(route("identifiers.update", $identifier), $data)
            ->assertRedirectToRoute("identifiers.edit", $identifier)
            ->assertSessionHas("alert_type", "success");
        $this->assertDatabaseHas("identifiers", [
            ...$data,
            "id" => $identifier->id,
            "user_id" => $user->id
        ]);
    }

    /**
     * deve redirecionar com mensagem de sucesso
     */
    public function test_update_action_same_name(): void
    {
        $user = $this->_user();
        $identifier = Identifier::factory()->create([
            "user_id" => $user
        ]);
        $data = Identifier::factory()->make([
            "name" => $identifier->name
        ])->toArray();

        $this->actingAs($user)->put(route("identifiers.update", $identifier), $data)
            ->assertRedirectToRoute("identifiers.edit", $identifier)
            ->assertSessionHas("alert_type", "success");
        $this->assertDatabaseHas("identifiers", [
            ...$data,
            "id" => $identifier->id,
            "user_id" => $user->id
        ]);
    }

    /**
     * deve salvar o avatar
     */
    public function test_update_action_send_avatar(): void
    {
        Storage::fake();

        $user = $this->_user();
        $identifier = Identifier::factory()->create([
            "user_id" => $user
        ]);
        $data = Identifier::factory()->make([
            "name" => $identifier->name,
            "avatar" => UploadedFile::fake()->image("avatar.png")
        ])->toArray();

        $this->actingAs($user)->put(route("identifiers.update", $identifier), $data)
            ->assertRedirectToRoute("identifiers.edit", $identifier)
            ->assertSessionHas("alert_type", "success");
        $actualIdentifier = Identifier::query()->find($identifier->id);

        $this->assertNotNull($actualIdentifier->avatar);
        $this->assertStringEndsWith(".png", $actualIdentifier->avatar);
    }

    /**
     * deve redirecionar para login
     */
    public function test_destroy_action_unauthenticated(): void
    {
        $identifier = Identifier::factory()->create();

        $this->delete(route("identifiers.destroy", $identifier))
            ->assertRedirectToRoute("auth.index");
    }

    /**
     * deve ter status 404
     */
    public function test_destroy_action_nonexistent(): void
    {
        $this->actingAs($this->_user())->delete(route("identifiers.destroy", 0))
            ->assertNotFound();
    }

    /**
     * deve ter status 404
     */
    public function test_destroy_action_is_not_owner(): void
    {
        $identifier = Identifier::factory()->create();

        $this->actingAs($this->_user())->delete(route("identifiers.destroy", $identifier))
            ->assertNotFound();
    }

    /**
     * deve redirecionar com mensagem de sucesso
     */
    public function test_destroy_action(): void
    {
        $user = $this->_user();
        $identifier = Identifier::factory()->create([
            "user_id" => $user->id
        ]);

        $this->actingAs($user)->delete(route("identifiers.destroy", $identifier))
            ->assertRedirect(route("identifiers.index"))
            ->assertSessionHas("alert_type", "success");
        $this->assertSoftDeleted($identifier);
    }

    public static function invalidPhonesProvider(): array
    {
        return [
            ["(99)987654321"],
            ["(99)9876-54321"],
            ["99987654321"],
            ["9998765-4321"],
            ["99 987654321"],
            ["99 9876-54321"],
            ["99 98765-4321"]
        ];
    }
}
