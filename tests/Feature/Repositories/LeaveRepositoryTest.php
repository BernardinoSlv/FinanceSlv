<?php

namespace Tests\Feature\Repositories;

use App\Models\Leave;
use App\Models\User;
use App\Repositories\Contracts\LeaveRepositoryContract;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class LeaveRepositoryTest extends TestCase
{
    public function test_create(): void
    {
        $user = User::factory()->create();
        $data = Leave::factory()->make()->toArray();

        $this->_repository()->create($user->id, $data);
        $this->assertDatabaseHas("leaves", [
            ...$data,
            "user_id" => $user->id
        ]);
    }

    protected function _repository(): LeaveRepositoryContract
    {
        return App::make(LeaveRepositoryContract::class);
    }
}
