<?php

namespace Tests\Feature\Models;

use App\Models\Leave;
use App\Models\Movement;
use App\Models\QuickEntry;
use App\Models\QuickLeave;
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
        Movement::factory(10)->create([
            "movementable_type" => Leave::class,
            "movementable_id" => Leave::factory([
                "leaveable_type" => QuickLeave::class,
                "leaveable_id" => QuickLeave::factory()->create()
            ])->create()
        ]);

        $leave = Leave::factory()->create([
            "leaveable_type" => QuickLeave::class,
            "leaveable_id" => QuickLeave::factory()->create()
        ]);

        $this->assertNull($leave->movement);
    }

    /**
     * deve retornar o movement correspondente
     */
    public function test_movement_method(): void
    {
        Movement::factory(10)->create([
            "movementable_type" => Leave::class,
            "movementable_id" => Leave::factory([
                "leaveable_type" => QuickLeave::class,
                "leaveable_id" => QuickLeave::factory()->create()
            ])->create()
        ]);

        $leave = Leave::factory()->create([
            "leaveable_type" => QuickLeave::class,
            "leaveable_id" => QuickLeave::factory()->create()
        ]);
        $movement = Movement::factory()->create([
            "movementable_type" => Leave::class,
            "movementable_id" => $leave
        ]);

        $this->assertEquals($movement->id, $leave->movement->id);
    }
}
