<?php

namespace App\Models;

use App\DTOs\OrderItemDTO;
use App\Enums\OrderStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;

    protected $fillable = [
        'total_bottles_quantity',
        'total_price',
    ];
    
    protected $casts = [
        'total_bottles_quantity' => 'integer',
        'total_price' => 'float',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function status()
    {
        return $this->hasMany(OrderStatus::class, 'order_id');
    }

    public function addOrderItems(array|Collection $items)
    {
        $items = is_array($items) ? collect($items) : $items;
        $items->each(function ($item) {
            $itemModel = OrderItemDTO::format($item);
            $orderItem = $this->items()->create($itemModel->toArray());
            $orderItem->addIngredients($item['ingredients']);
            return $orderItem;
        });
        return $this->items()->get();
    }

    public function pending()
    {
        $this->status()->create(['status' => OrderStatusEnum::PENDING->value]);
        return $this;
    }

    public function processing()
    {
        $this->status()->create(['status' => OrderStatusEnum::PROCESSING->value]);
        return $this;
    }

    public function complete()
    {
        $this->status()->create(['status' => OrderStatusEnum::COMPLETED->value]);
        return $this;
    }

    public function cancel()
    {
        $this->status()->create(['status' => OrderStatusEnum::CANCELLED->value]);
        return $this;
    }

    public function hold()
    {
        $this->status()->create(['status' => OrderStatusEnum::ON_HOLD->value]);
        return $this;
    }
    
    public function currentStatusObject()
    {
        return $this->hasOne(OrderStatus::class)->latestOfMany();
    }
    
    public function getCurrentStatusAttribute()
    {
        return $this->currentStatusObject?->status ?? OrderStatusEnum::PENDING->value;
    }

}
