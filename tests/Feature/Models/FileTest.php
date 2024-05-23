<?php

namespace Tests\Feature\Models;

use App\Models\File;
use App\Models\Movement;
use App\Models\Quick;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FileTest extends TestCase
{
    use RefreshDatabase;

    /**
     * deve retornar o usuário
     */
    public function test_user_method(): void
    {
        $user = User::factory()->create();
        $file = File::factory()->create([
            "user_id" => $user,
            "fileable_type" => Quick::class,
            "fileable_id" => Quick::factory()->create()
        ]);

        $this->assertEquals($user->id, $file->user->id);
    }

    /**
     * deve retornar o usuário mesmo deletado
     */
    public function test_user_method_trashed_user(): void
    {
        $user = User::factory()->trashed()->create();
        $file = File::factory()->create([
            "user_id" => $user,
            "fileable_type" => Quick::class,
            "fileable_id" => Quick::factory()->create()
        ]);

        $this->assertEquals($user->id, $file->user->id);
    }

    /**
     * deve retornar o model
     */
    public function test_fileable_method_using_quick(): void
    {
        Quick::factory(2)->create();

        $quick = Quick::factory()->create();
        $file = File::factory()->create([
            "fileable_type" => Quick::class,
            "fileable_id" => $quick
        ]);

        $this->assertInstanceOf(Quick::class, $file->fileable);
        $this->assertEquals($quick->id, $file->fileable->id);
    }

    /**
     * deve retornar o model
     */
    public function test_fileable_method_using_movement(): void
    {
        Movement::factory(2)->create([
            "movementable_type" => Quick::class,
            "movementable_id" => Quick::factory()->create()
        ]);

        $movement = Movement::factory()->create([
            "movementable_type" => Quick::class,
            "movementable_id" => Quick::factory()->create()
        ]);
        $file = File::factory()->create([
            "fileable_type" => Movement::class,
            "fileable_id" => $movement
        ]);

        $this->assertInstanceOf(Movement::class, $file->fileable);
        $this->assertEquals($movement->id, $file->fileable->id);
    }

     /**
     * deve retornar o model mesmo deletado
     */
    public function test_fileable_method_using_trashed_quick(): void
    {
        Quick::factory(2)->create();

        $quick = Quick::factory()->trashed()->create();
        $file = File::factory()->create([
            "fileable_type" => Quick::class,
            "fileable_id" => $quick
        ]);

        $this->assertInstanceOf(Quick::class, $file->fileable);
        $this->assertEquals($quick->id, $file->fileable->id);
    }
}
