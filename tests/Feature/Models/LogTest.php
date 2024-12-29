<?php

namespace Tests\Feature\Models;

use App\Models\Log;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LogTest extends TestCase
{
    use RefreshDatabase;

    /** deve retornar o usuário  */
    public function test_user_relation(): void
    {
        User::factory(2)->create();

        $user = User::factory()->create();
        $log = Log::factory()->create([
            "user_id" => $user
        ]);

        $this->assertEquals($user->id, $log->user->id);
    }

    /** deve retornar o usuário mesmo deletado */
    public function test_user_relation_trashed(): void
    {
        User::factory(2)->create();

        $user = User::factory()->trashed()->create();
        $log = Log::factory()->create([
            "user_id" => $user
        ]);

        $this->assertEquals($user->id, $log->user->id);
    }
}
