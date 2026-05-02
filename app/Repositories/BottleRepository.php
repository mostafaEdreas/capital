<?php

namespace App\Repositories;

use App\Models\Bottle;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class BottleRepository
{
    public function getAll(array $with = [], array $select = ['*'], int $perPage = 25): Collection
    {
        return Bottle::with($with)->select($select)->get();
    }

    public function getByBottleIds(array $bottleIds, array $select = ['*'], array $with = []): Collection
    {
        return Bottle::with($with)->select($select)->whereIn('id', $bottleIds)->get();
    }
}