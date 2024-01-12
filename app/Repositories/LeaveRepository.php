<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Leave;
use App\Repositories\BaseRepository;
use App\Repositories\Contracts\LeaveRepositoryContract;

class LeaveRepository extends BaseRepository  implements LeaveRepositoryContract
{
    public function __construct(Leave $leave)
    {
        parent::__construct($leave);
    }

    public function create(int $userId, array $attributes): Leave
    {
        $leave = new Leave($attributes);
        $leave->user_id = $userId;
        $leave->save();
        return $leave;
    }
}
