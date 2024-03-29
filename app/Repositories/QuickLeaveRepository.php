<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\QuickLeave;
use App\Repositories\Contracts\QuickLeaveRepositoryContract;

class QuickLeaveRepository extends BaseRepository implements QuickLeaveRepositoryContract
{
    public function __construct(QuickLeave $quickLeave)
    {
        parent::__construct($quickLeave);
    }

    public function create(int $userId, array $attributes): QuickLeave
    {
        $quickLeave = new QuickLeave($attributes);
        $quickLeave->user_id = $userId;
        $quickLeave->save();

        return $quickLeave;
    }
}
