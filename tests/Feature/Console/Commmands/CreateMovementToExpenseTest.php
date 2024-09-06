<?php

namespace Tests\Feature\Console\Commmands;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateMovementToExpenseTest extends TestCase
{
    use RefreshDatabase;
    /** não deve adicionar movimentação nova */
    public function test_handle_method_without_available_expense_to_create_movement(): void {}

    /** deve criar 2 novas movimentações */
    public function test_handle_method_with_available_expense_to_create_movement(): void {}

    public function test_handle_method_with_available_expense_but_have_movement_to_this_month(): void {}
}
