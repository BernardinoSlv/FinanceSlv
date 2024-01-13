<?php

namespace Tests\Feature\Repositories;

use App\Models\Investiment;
use App\Models\User;
use App\Repositories\Contracts\InvestimentRepositoryContract;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class InvestimentRepositoryTest extends TestCase
{
    public function test_create_method(): void
    {
        $user = $this->_user();
        $data = Investiment::factory()->make()->toArray();

        $this->_repository()->create($user->id, $data);

        $this->assertDatabaseHas("investiments", [
            ...$data,
            "user_id" => $user->id
        ]);
    }


    protected function _repository(): InvestimentRepositoryContract
    {
        return app(InvestimentRepositoryContract::class);
    }
}
