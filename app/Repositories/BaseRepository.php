<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Repositories\Contracts\BaseRepositoryContract;
use App\Repositories\Contracts\EntryRepositoryContract;
use App\Repositories\Contracts\LeaveRepositoryContract;
use App\Repositories\Contracts\MovementRepositoryContract;
use Error;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository implements BaseRepositoryContract
{
    protected Model $_model;

    public function __construct(Model $model)
    {
        $this->_model = $model;
    }

    public function allByUser(int $id, bool $onlyCurrentMonth = false): Collection
    {
        return $this->_model->query()
            ->when($onlyCurrentMonth, function (Builder $query): void {
                $query->whereYear("created_at", date("Y"))
                    ->whereMonth("created_at", (date("m")));
            })
            ->where("user_id", $id)
            ->get();
    }

    public function update(int $id, array $attributes): bool
    {
        if (!($entity = $this->_model->query()->find($id))) {
            return false;
        }
        $entity->fill($attributes);
        $entity->save();
        return true;
    }

    public function delete(int $id): bool
    {
        if (!($identifier = $this->_model->query()->find($id))) {
            return false;
        }
        $identifier->delete();
        return true;
    }

}
