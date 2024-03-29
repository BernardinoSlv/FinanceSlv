<?php

namespace Tests\Feature\Models;

use App\Models\Debt;
use App\Models\Leave;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DebtTest extends TestCase
{
    use RefreshDatabase;

    /**
     * deve retornar uma coleção vazia
     */
    public function test_leaves_method_without_leave(): void
    {
        Leave::factory(10)->create();
        $debt = Debt::factory()->create();

        $this->assertCount(0, $debt->leaves);
    }

    /**
     * deve retornar uma coleção com 10 items
     */
    public function test_leaves_method(): void
    {
        Leave::factory(10)->create();
        $debt = Debt::factory()->create();
        Leave::factory(10)->create([
            "leaveable_type" => Debt::class,
            "leaveable_id" => $debt
        ]);

        $this->assertCount(10, $debt->leaves);
    }
}
