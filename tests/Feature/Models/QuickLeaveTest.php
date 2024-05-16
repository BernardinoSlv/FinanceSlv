<?php

namespace Tests\Feature\Models;

use App\Models\Debt;
use App\Models\Debtor;
use App\Models\Leave;
use App\Models\QuickLeave;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class QuickLeaveTest extends TestCase
{
    /**
     * deve retornar null
     */
    public function test_leave_method_without_leave(): void
    {
        Leave::factory(10)->create([
            "leaveable_type" => QuickLeave::class,
            "leaveable_id" => QuickLeave::factory()->create()
        ]);

        $quickLeave = QuickLeave::factory()->create();

        $this->assertNull($quickLeave->leave);
    }

    /**
     * deve retornar a saída correspondente
     */
    public function test_leave_method(): void
    {
        Leave::factory(10)->create([
            "leaveable_type" => QuickLeave::class,
            "leaveable_id" => QuickLeave::factory()->create()
        ]);

        $quickLeave = QuickLeave::factory()->create();
        $leave = Leave::factory()->create([
            "leaveable_type" => QuickLeave::class,
            "leaveable_id" => $quickLeave
        ]);

        $this->assertEquals($leave->id, $quickLeave->leave->id);
    }
}
