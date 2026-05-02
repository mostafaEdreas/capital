<?php

namespace App\Models;

use App\Enums\OrderStatusEnum;
use Illuminate\Database\Eloquent\Model;

class OrderStatus extends Model
{

    protected $fillable = [
        'status',
        'created_by',
    ];

    protected $casts = [
        'status' => OrderStatusEnum::class,
        'created_by' => 'string',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    


}
