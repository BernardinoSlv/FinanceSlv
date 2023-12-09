<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Entry;
use App\Repositories\Contracts\EntryRepositoryContract;
use Illuminate\Database\Eloquent\Collection;

class EntryRepository extends BaseRepository implements EntryRepositoryContract
{
    public function __construct(Entry $entry)
    {
        parent::__construct($entry);
    }

    public function allByUser(int $id): Collection
    {
        return $this->_model->query()->where("user_id", $id)->get();
    }

    public function create(int $userId, array $attributes): Entry
    {
        $entry = new Entry($attributes);
        $entry->user_id = $userId;
        $entry->save();
        return $entry;
    }
}
