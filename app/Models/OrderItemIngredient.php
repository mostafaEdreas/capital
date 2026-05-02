<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItemIngredient extends Model
{
    /** @use HasFactory<\Database\Factories\OrderItemIngredientFactory> */
    use HasFactory;

    protected $fillable = [
        'order_item_id',
        'ingredient_type_id',
        'price',
        'for_gram',
        'sold_quantity_grams',
        'sold_total_price',
    ];

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function ingredientType()
    {
        return $this->belongsTo(IngredientType::class);
    }
}
