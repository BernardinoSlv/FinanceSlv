<?php

namespace Tests\Feature\Models;

use App\Models\Leave;
use App\Models\Need;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class NeedTest extends TestCase
{
    use RefreshDatabase;

    /**
     * deve retornar null
     */
    public function test_leave_method_without_leave(): void
    {
        Leave::factory(10)
            ->sequence(...Need::factory(10)->create()->map(function (Need $need): array {
                return ["leaveable_id" => $need->id];
            }))
            ->create([
                "leaveable_type" => Need::class,
            ]);

        $need = Need::factory()->create();

        $this->assertNull($need->leave);
    }

    /**
     * deve retornar a saída correta
     */
    public function test_leave_method(): void
    {
        Leave::factory(10)
            ->sequence(...Need::factory(10)->create()->map(function (Need $need): array {
                return ["leaveable_id" => $need->id];
            }))
            ->create([
                "leaveable_type" => Need::class,
            ]);

        $need = Need::factory()->create();
        $leave = Leave::factory()->create([
            "leaveable_type" => Need::class,
            "leaveable_id" => $need
        ]);

        $this->assertEquals($leave->id, $need->leave->id);
    }
}
