<?php

namespace App\Repositories;

use App\Models\Ingredient;
use Illuminate\Support\Collection;

class IngredientRepository
{
    public function getAll(array $with = [], array $select = ['*'], int $perPage = 25): Collection
    {
        return Ingredient::with($with)->select($select)->get();
    }

    public function getByIngredientIds(array $ingredientTypeIds, array $select = ['*'], array $with = []): Collection
    {
        return Ingredient::with($with)->select($select)->whereIn('id', $ingredientTypeIds)->get();
    }
}