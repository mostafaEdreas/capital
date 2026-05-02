<?php

namespace App\DTOs;

class OrderItemIngredientDTO
{
    public function __construct(
        public int $ingredient_type_id,
        public float $price,
        public int $for_gram,
        public int $sold_quantity_grams,
        public float $sold_total_price,
    ) {}

    public function toArray(): array
    {
        return [
            'ingredient_type_id' => $this->ingredient_type_id,
            'price' => $this->price,
            'for_gram' => $this->for_gram,
            'sold_quantity_grams' => $this->sold_quantity_grams,
            'sold_total_price' => $this->sold_total_price,
        ];
    }

    public static function format(array $data): OrderItemIngredientDTO
    {
        return new self(
            $data['ingredient_type_id'],
            $data['price'],
            $data['for_gram'],
            $data['sold_quantity_grams'],
            $data['sold_total_price'] ?? 0,
        );
    }
}