<?php

namespace App\Repositories;

use App\DTOs\OrderDTO;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OrderRepository
{


    public function getAll(array $with = [], array $select = ['*'], int $perPage = 25): LengthAwarePaginator
    {
        return Order::with($with)->select($select)->paginate($perPage)->withQueryString();
    }

    public function today(array $with = [], array $select = ['*']): Collection
    {
        return Order::with($with)->select($select)->whereDate('created_at', now()->today())->get();
    }

    public function thisMonth(array $with = [], array $select = ['*']): Collection
    {
        return Order::with($with)->select($select)->whereMonth('created_at', now()->month)->get();
    }

    public function thisYear(array $with = [], array $select = ['*']): Collection
    {
        return Order::with($with)->select($select)->whereYear('created_at', now()->year)->get();
    }

    public function create(array $orderData ): Order
    {
        $orderDataDTO = OrderDTO::format($orderData);
        $order = Order::create($orderDataDTO->toArray());
        $order->addOrderItems($orderData['items']);
         return $order;
    }

    public function addOrderItems( Order $order,array $orderItems = []): Collection
    {
        $items = collect($orderItems);
        $items->each(function ($item) use ($order) {
            $order->items()->create($item);
        });
        return $order->items()->get();
    }

    public function syncOrderItemIngredients( OrderItem $orderItem,array $orderItemIngredients = []): Collection
    {
        $orderItem->ingredients()->upsert($orderItemIngredients, ['id'], ['sold_quantity_grams', 'sold_total_price', 'price_at_purchase', 'price_per_gram', 'quantity_per_gram', 'ingredient_type_id']);
        return $orderItem->ingredients()->get();
    }

    public function findById(int $id , array $with = [], array $select = ['*']): Order
    {
        $order = Order::with($with)->select($select)->findOrFail($id);
        if (!$order) {
            throw new NotFoundHttpException('Order not found' , null , 404);
        }
        return $order;
    }

    public function pending(Order $order): Order
    {
        $order->pending();
        return $order->refresh();
    }

    public function processing(Order $order): Order
    {
        $order->processing();
        return $order->refresh();
    }    
    public function complete(Order $order): Order
    {
        $order->complete();
        return $order->refresh();
    }
    
    public function cancel(Order $order): Order
    {
        $order->cancel();
        return $order->refresh();
    }
    public function hold(Order $order): Order
    {
        $order->hold();
        return $order->refresh();
    }

}