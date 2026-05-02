<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'bottle_id' => $this->bottle_id,
            'bottle_quantity' => $this->bottle_quantity,
            'bottle_price' => $this->bottle_price,
            'bottle_total_price' => $this->bottle_total_price,
            'bottle_total_price_with_ingredients' => $this->bottle_total_price_with_ingredients,
            'bottle' => $this->whenLoaded('bottle'),
            'ingredients' => OrderItemIngredientResource::collection($this->whenLoaded('ingredients')),
        ];
    }
}
