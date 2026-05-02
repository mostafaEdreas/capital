<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IngredientType extends Model
{
    /** @use HasFactory<\Database\Factories\IngredientTypeFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'for_gram',
        ];

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class);
    }
}
