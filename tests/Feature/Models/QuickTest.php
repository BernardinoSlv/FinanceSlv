<?php

namespace Tests\Feature\Models;

use App\Models\Identifier;
use App\Models\Quick;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class QuickTest extends TestCase
{
    use RefreshDatabase;

    /**
     * deve retornar o User
     */
    public function test_user_method(): void
    {
        User::factory(2)->create();

        $user = User::factory()->create();
        $quick =  Quick::factory()->create([
            "user_id" => $user
        ]);

        $this->assertEquals($user->id, $quick->user->id);
    }

    /**
     * deve retornar o User mesmo deletado
     */
    public function test_user_method_trashed_user(): void
    {
        User::factory(2)->create();

        $user = User::factory()->trashed()->create();
        $quick =  Quick::factory()->create([
            "user_id" => $user
        ]);

        $this->assertSoftDeleted($quick->user);
        $this->assertEquals($user->id, $quick->user->id);
    }

    /**
     * deve retornar o Identifier
     */
    public function test_identifier_method(): void
    {
        Identifier::factory(2)->create();

        $identifier = Identifier::factory()->create();
        $quick = Quick::factory()->create(["identifier_id" => $identifier]);

        $this->assertEquals($identifier->id, $quick->identifier->id);
    }

    /**
     * deve retornar o Identifier mesmo deletado
     */
    public function test_identifier_method_trashed_identifier(): void
    {
        Identifier::factory(2)->create();

        $identifier = Identifier::factory()->trashed()->create();
        $quick = Quick::factory()->create(["identifier_id" => $identifier]);

        $this->assertSoftDeleted($quick->identifier);
        $this->assertEquals($identifier->id, $quick->identifier->id);
    }
}
