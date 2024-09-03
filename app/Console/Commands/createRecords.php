<?php

namespace App\Console\Commands;

use App\Enums\MovementTypeEnum;
use App\Models\Debt;
use App\Models\Identifier;
use App\Models\Movement;
use App\Models\Quick;
use Illuminate\Console\Command;

class createRecords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-records {user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Criar registros no banco de dados';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $identifiers = Identifier::factory(30)->create(['user_id' => $this->argument('user')]);
        Debt::factory(20)
            ->sequence(...$identifiers->map(
                fn ($identifier) => ['identifier_id' => $identifier->id]
            )->toArray())
            ->create(['user_id' => $this->argument('user')]);
        Quick::factory(20)
            ->sequence(...$identifiers->map(
                fn ($identifier) => ['identifier_id' => $identifier->id]
            )->toArray())
            ->has(Movement::factory()->state(['type' => MovementTypeEnum::IN->value, 'identifier_id' => $identifiers->first(), 'user_id' => $this->argument('user')]), 'movement')
            ->create(['user_id' => $this->argument('user')]);
        Quick::factory(20)
            ->sequence(...$identifiers->map(
                fn ($identifier) => ['identifier_id' => $identifier->id]
            )->toArray())
            ->has(Movement::factory()->state(['type' => MovementTypeEnum::OUT->value, 'identifier_id' => $identifiers->first(), 'user_id' => $this->argument('user')]), 'movement')
            ->create(['user_id' => $this->argument('user')]);
    }
}
