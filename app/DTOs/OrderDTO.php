<?php

namespace App\DTOs;

class OrderDTO
{
    public function __construct(
        public int $total_bottles_quantity,
        public float $total_price,
    ) {}

    public function toArray(): array
    {
        return [
            'total_bottles_quantity' => $this->total_bottles_quantity,
            'total_price' => $this->total_price,
        ];
    }
    public static function format(array $data): OrderDTO
    {
        return new self(
            $data['order_num'] ?? 0,
            $data['total_price'] ?? 0,
        );
    }


    
}   