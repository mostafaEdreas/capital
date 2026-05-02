<?php

namespace App\Http\Resources;

use BackedEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'total_bottles_quantity' => $this->total_bottles_quantity,
            'total_price' => $this->total_price,
            'current_status' => $this->resolveCurrentStatus(),
            'created_at' => $this->created_at?->toIso8601String(),
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
        ];
    }

    protected function resolveCurrentStatus(): ?string
    {
        if ($this->relationLoaded('currentStatusObject') && $this->currentStatusObject !== null) {
            $status = $this->currentStatusObject->status;

            return $status instanceof BackedEnum ? $status->value : (string) $status;
        }

        $status = $this->current_status;

        if ($status instanceof BackedEnum) {
            return $status->value;
        }

        return is_string($status) ? $status : null;
    }
}
