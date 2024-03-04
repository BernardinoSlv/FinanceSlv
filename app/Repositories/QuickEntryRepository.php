<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\QuickEntry;
use App\Repositories\Contracts\QuickEntryRepositoryContract;

class QuickEntryRepository extends BaseRepository implements QuickEntryRepositoryContract
{
    public function __construct(QuickEntry $quickEntry)
    {
        parent::__construct($quickEntry);
    }

    public function create(int $userId, array $attributes): QuickEntry
    {
        $quickEntry = new QuickEntry($attributes);
        $quickEntry->user_id = $userId;
        $quickEntry->save();

        return $quickEntry;
    }
}
