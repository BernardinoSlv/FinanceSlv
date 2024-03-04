<?php

namespace App\Providers;

use App\Repositories\Contracts\DebtorRepositoryContract;
use App\Repositories\Contracts\DebtRepositoryContract;
use App\Repositories\Contracts\IdentifierRepositoryContract;
use App\Repositories\Contracts\EntryRepositoryContract;
use App\Repositories\Contracts\ExpenseRepositoryContract;
use App\Repositories\Contracts\InvestimentRepositoryContract;
use App\Repositories\Contracts\LeaveRepositoryContract;
use App\Repositories\Contracts\MovementRepositoryContract;
use App\Repositories\Contracts\NeedRepositoryContract;
use App\Repositories\Contracts\QuickEntryRepositoryContract;
use App\Repositories\Contracts\UserRepositoryContract;
use App\Repositories\DebtorRepository;
use App\Repositories\DebtRepository;
use App\Repositories\IdentifierRepository;
use App\Repositories\EntryRepository;
use App\Repositories\ExpenseRepository;
use App\Repositories\InvestimentRepository;
use App\Repositories\LeaveRepository;
use App\Repositories\MovementRepository;
use App\Repositories\NeedRepository;
use App\Repositories\QuickEntryRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            UserRepositoryContract::class,
            UserRepository::class
        );

        $this->app->bind(
            EntryRepositoryContract::class,
            EntryRepository::class
        );

        $this->app->bind(
            LeaveRepositoryContract::class,
            LeaveRepository::class
        );

        $this->app->bind(
            ExpenseRepositoryContract::class,
            ExpenseRepository::class
        );

        $this->app->bind(
            DebtorRepositoryContract::class,
            DebtorRepository::class
        );

        $this->app->bind(
            DebtRepositoryContract::class,
            DebtRepository::class
        );

        $this->app->bind(
            InvestimentRepositoryContract::class,
            InvestimentRepository::class
        );

        $this->app->bind(
            NeedRepositoryContract::class,
            NeedRepository::class
        );

        $this->app->bind(
            IdentifierRepositoryContract::class,
            IdentifierRepository::class
        );

        $this->app->bind(
            MovementRepositoryContract::class,
            MovementRepository::class
        );

        $this->app->bind(
            QuickEntryRepositoryContract::class,
            QuickEntryRepository::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
