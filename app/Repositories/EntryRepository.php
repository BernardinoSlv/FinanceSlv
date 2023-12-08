<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Entry;
use App\Repositories\Contracts\EntryRepositoryContract;

class EntryRepository extends BaseRepository implements EntryRepositoryContract
{
    public function __construct(Entry $entry)
    {
        parent::__construct($entry);
    }
}
