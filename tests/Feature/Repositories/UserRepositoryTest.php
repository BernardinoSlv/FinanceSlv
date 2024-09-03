<?php

namespace Tests\Feature\Repositories;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryContract;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class UserRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected $seeds = true;

    /**
     * deve retornar um nove usuário
     */
    public function test_create_method(): void
    {
        $data = [
            ...User::factory()->make()->toArray(),
            'password' => 'password',
        ];
        $this->assertNotNull($this->_repository()->create($data));
        $this->assertDatabaseHas(
            'users',
            Arr::except($data, ['email_verified_at', 'password'])
        );
    }

    protected function _repository(): UserRepositoryContract
    {
        return App::make(UserRepositoryContract::class);
    }
}
