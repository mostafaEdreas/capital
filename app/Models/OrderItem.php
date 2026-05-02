<?php

namespace App\Models;

use App\DTOs\OrderItemIngredientDTO;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class OrderItem extends Model
{
        /** @use HasFactory<\Database\Factories\OrderItemFactory> */
    use HasFactory;

    protected $fillable = [
        'order_id',
        'bottle_id',
        'bottle_price',
        'bottle_quantity',
        'bottle_total_price',
        'bottle_total_price_with_ingredients',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function bottle()
    {
        return $this->belongsTo(Bottle::class);
    }

    public function ingredients()
    {
        return $this->hasMany(OrderItemIngredient::class, 'order_item_id');
    }

    public function addIngredients(array|Collection $ingredients)
    {
        $ingredients = is_array($ingredients) ? collect($ingredients) : $ingredients;

        $ingredients->each(function ($ingredient) {
            $ingredientModel = OrderItemIngredientDTO::format($ingredient->toArray());
            $this->ingredients()->create($ingredientModel->toArray());
        });
        return $this->ingredients;
    }
}
