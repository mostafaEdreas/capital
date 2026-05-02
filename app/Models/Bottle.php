<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bottle extends Model
{
    /** @use HasFactory<\Database\Factories\BottleFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'size',
        'price',
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'bottle_id');
    }

}
