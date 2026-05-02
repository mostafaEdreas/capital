<?php

namespace App\Services;

use App\DTOs\OrderDTO;
use App\DTOs\OrderItemDTO;
use App\Enums\OrderStatusEnum;
use App\Http\Requests\OrderRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Repositories\OrderRepository;
use App\Repositories\BottleRepository;
use App\Repositories\IngredientRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function __construct(
        public OrderRepository $orderRepository,
        public BottleRepository $bottleRepository,
        public IngredientRepository $ingredientRepository,
    ) {}


    public function findById(int $id, array $with = [], array $select = ['*']): Order
    {
        return $this->orderRepository->findById($id, $with, $select);
    }

    public function getAll(array $with = [], array $select = ['*'], int $perPage = 25): LengthAwarePaginator
    {
        return $this->orderRepository->getAll($with, $select, $perPage);
    }

    public function create(OrderRequest $request): Order
    {
        return DB::transaction(function () use ($request) {
                $items = $this->orderMapping($request);
                $order = $this->orderRepository->create($items);
                return $order->pending()->refresh()->load('items.ingredients');
        });
    }   

    protected function orderMapping(OrderRequest $request): array
    {
        $items = [];
        $items['items'] = $this->orderItemMapping($request->validated('items'));
        $items['total_bottles_quantity'] = $items['items']->sum('bottle_quantity');
        $items['total_price'] = $items['items']->sum('bottle_total_price_with_ingredients');
        return $items;
    }

    protected function orderItemMapping(array $items): Collection
    {
        $items = collect($items);
        $bottleIds = $items->pluck('bottle_id')->toArray();
        $bottles = $this->bottleRepository->getByBottleIds($bottleIds);
        $items = $items->map(function ($item) use ($bottles) {
            $bottle = $bottles->firstWhere('id', $item['bottle_id']);
             $orderItem = [
                'bottle_id' => $bottle->id,
                'bottle_quantity' => $item['bottle_quantity'],
                'bottle_price' => $bottle->price,
                'bottle_total_price' => $bottle->price * $item['bottle_quantity'],
            ];
            $orderItem['ingredients'] = $this->orderItemIngredientMapping($item['ingredients']);
            $orderItem['bottle_total_price_with_ingredients'] = $orderItem['bottle_total_price'] + ($orderItem['ingredients']->sum('sold_total_price') * $item['bottle_quantity']);
            if($bottle->size < $orderItem['ingredients']->sum('sold_quantity_grams')) {
                throw ValidationException::withMessages(['The bottle size is not enough for the ingredients']);
            }
            return $orderItem;
        });
        return $items;
    }

    protected function orderItemIngredientMapping(array $ingredientsValidated) : Collection
    {
        
        $ingredientsValidated = collect($ingredientsValidated);
        $ingredientIds = $ingredientsValidated->pluck('ingredient_id')->toArray();
        $with = ['types'];
        $ingredientsModels = $this->ingredientRepository->getByIngredientIds($ingredientIds, ['*'], $with);
        $ingredients = $ingredientsValidated->map(function ($ingredient) use ($ingredientsModels) {
            $ingredientModel = $ingredientsModels->firstWhere('id', $ingredient['ingredient_id']);
            if (!$ingredientModel) {
                throw ValidationException::withMessages(['Ingredient not found']);
            }
            $types = $ingredientModel->types;
            $type = $types->firstWhere('id', $ingredient['ingredient_type_id']);
            if (!$type) {
                throw new \Exception('Ingredient type not found');
            }
            return collect([
                'ingredient_type_id' => $type->id,
                'price' => $type->price,
                'for_gram' => $type->for_gram,
                'sold_quantity_grams' => $ingredient['sold_quantity_grams'],
                'sold_total_price' => ( $ingredient['sold_quantity_grams'] / $type->for_gram )  * $type->price,
            ]);

        });
        return $ingredients;
    }
    public function processing(Order $order): Order
    {
        if($order->currentStatus->value !== OrderStatusEnum::PENDING->value) {
            throw ValidationException::withMessages(['Order is not pending']);
        }
        return $this->orderRepository->processing($order);
    }

    public function complete(Order $order): Order
    {
        if($order->currentStatus->value !== OrderStatusEnum::PROCESSING->value) {
            throw ValidationException::withMessages(['Order is not processing']);
        }
        return $this->orderRepository->complete($order);
    }

    public function cancel(Order $order): Order
    {
        if($order->currentStatus->value === OrderStatusEnum::COMPLETED->value) {
            throw ValidationException::withMessages(['Order is already completed']);
        }
        return $this->orderRepository->cancel($order);
    }

}