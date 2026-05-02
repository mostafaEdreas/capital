<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService,
    ) {}

    public function index(Request $request): Response
    {
        $select = ['id', 'total_bottles_quantity', 'total_price', 'created_at'];
        $with = ['currentStatusObject'];
        $orders = $this->orderService->getAll($with, $select);

        return OrderResource::collection($orders)->response();
    }

    public function store(OrderRequest $request): JsonResponse
    {
        try {
            $order = $this->orderService->create($request);

            return (new OrderResource(
                $order->loadMissing([
                    'currentStatusObject',
                    'items.bottle',
                    'items.ingredients.ingredientType.ingredient',
                ])
            ))
                ->response()
                ->setStatusCode(201);
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            if (config('app.debug')) {
                throw $e;
            }

            return response()->json([
                'message' => 'Creation failed. Please try again.',
            ], 500);
        }
    }

    public function show(Order $order): OrderResource
    {
        $with = [
            'currentStatusObject',
            'status',
            'items.bottle',
            'items.ingredients.ingredientType.ingredient',
        ];
        $order = $this->orderService->findById($order->id, $with);

        return new OrderResource($order);
    }


    public function processing(Order $order): OrderResource
    {
        $order = $this->orderService->processing($order);

        return new OrderResource($this->reloadOrderForResource($order));
    }

    public function complete(Order $order): OrderResource
    {
        $order = $this->orderService->complete($order);

        return new OrderResource($this->reloadOrderForResource($order));
    }

    public function cancel(Order $order): OrderResource
    {
        $order = $this->orderService->cancel($order);

        return new OrderResource($this->reloadOrderForResource($order));
    }

    protected function reloadOrderForResource(Order $order): Order
    {
        return $order->loadMissing([
            'currentStatusObject',
            'items.bottle',
            'items.ingredients.ingredientType.ingredient',
        ]);
    }
}
