<?php

namespace App\DTOs;

class OrderItemDTO
{
    public function __construct(
        public int $bottle_id,
        public float $bottle_price,
        public int $bottle_quantity,
        public float $bottle_total_price,
        public float $bottle_total_price_with_ingredients,
    ) {}

    public function toArray(): array
    {
        return [
            'bottle_id' => $this->bottle_id,
            'bottle_price' => $this->bottle_price,
            'bottle_quantity' => $this->bottle_quantity,
            'bottle_total_price' => $this->bottle_total_price,
            'bottle_total_price_with_ingredients' => $this->bottle_total_price_with_ingredients,
        ];
    }

    public static function format(array $data): OrderItemDTO
    {
        return new self(
            $data['bottle_id'],
            $data['bottle_price'],
            $data['bottle_quantity'],
            $data['bottle_total_price'] ?? 0,
            $data['bottle_total_price_with_ingredients'] ?? 0,
        );
    }
}