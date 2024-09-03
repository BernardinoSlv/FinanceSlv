<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Identifier;
use App\Repositories\Contracts\IdentifierRepositoryContract;

class IdentifierRepository extends BaseRepository implements IdentifierRepositoryContract
{
    public function __construct(Identifier $identifier)
    {
        parent::__construct($identifier);
    }

    public function create(int $userId, array $attribures): Identifier
    {
        $identifier = new Identifier($attribures);
        $identifier->user_id = $userId;
        $identifier->save();

        return $identifier;
    }
}
