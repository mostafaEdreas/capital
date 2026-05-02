<?php

namespace App\Services;

use App\Repositories\IngredientRepository;
use Illuminate\Support\Collection;

class IngredientService
{
    public function __construct(
        public IngredientRepository $ingredientRepository
    ) {}

    public function getAll(array $with = [], array $select = ['*']): Collection
    {
        return $this->ingredientRepository->getAll($with, $select);
    }

}   