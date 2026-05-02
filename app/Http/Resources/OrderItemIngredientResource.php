<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemIngredientResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ingredient_type_id' => $this->ingredient_type_id,
            'price' => $this->price,
            'for_gram' => $this->for_gram,
            'sold_quantity_grams' => $this->sold_quantity_grams,
            'sold_total_price' => $this->sold_total_price,
            'ingredient_type' => $this->whenLoaded('ingredientType'),
        ];
    }
}
