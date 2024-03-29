<?php

namespace Tests\Feature\Models;

use App\Models\Leave;
use App\Models\Movement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LeaveTest extends TestCase
{
    use RefreshDatabase;

    /**
     * deve retornar null
     */
    public function test_movement_method_without_movement(): void
    {
        Movement::factory(10)->create();
        $leave = Leave::factory()->create();

        $this->assertNull($leave->movement);
    }

    /**
     * deve retornar o movement correspondente
     */
    public function test_movement_method(): void
    {
        Movement::factory(10)->create();
        $leave = Leave::factory()->create();
        $movement = Movement::factory()->create([
            "movementable_type" => Leave::class,
            "movementable_id" => $leave
        ]);

        $this->assertEquals($movement->id, $leave->movement->id);
    }
}
