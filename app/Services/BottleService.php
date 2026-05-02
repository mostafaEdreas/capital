<?php

namespace App\Services;

use App\Repositories\BottleRepository;
use Illuminate\Support\Collection;

class BottleService
{
    public function __construct(
        public BottleRepository $bottleRepository
    ) {}

    public function getAll(array $with = [], array $select = ['*']): Collection
    {
        return $this->bottleRepository->getAll($with, $select);
    }

}       