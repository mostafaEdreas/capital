<?php


namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Models\Order;
use App\Services\OrderService;
use App\Services\BottleService;
use App\Services\IngredientService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class OrderController extends Controller
{

    public function __construct(
        protected OrderService $orderService ,
        protected BottleService $bottleService,
        protected IngredientService $ingredientService
    ) {
    }

  
    public function index(): View
    {
        $select = ['id', 'total_bottles_quantity', 'total_price', 'created_at'];
        $with = ['currentStatusObject'];
        $orders = $this->orderService->getAll($with, $select);

        return view('orders.index', compact('orders'));
    }

    public function create(): View
    {
        $data = [
            'bottles' => $this->bottleService->getAll(),
            'ingredients' => $this->ingredientService->getAll(['types']),
        ];
        return view('orders.create', $data);
    }


    public function store(OrderRequest $request): RedirectResponse
    {
        try {
            $order = $this->orderService->create($request);

            return redirect()
                ->route('orders.show', $order->id)
                ->with('success', 'Order created successfully.');
        } catch (\Exception $e) {
            if(config('app.debug')) {
                throw $e;
            }
            return back()
                ->withInput()
                ->withErrors('Creation failed: Please try again.');
        }
    }

    public function show(int $id): View
    {
        $with = [
            'currentStatusObject',
            'status',
            'items.bottle',
            'items.ingredients.ingredientType.ingredient',
        ];
        $order = $this->orderService->findById($id, $with);
        return view('orders.show', compact('order'));
    }


    public function processing(Order $order): RedirectResponse
    {
        $this->orderService->processing($order); 
        return back()->with('status', 'Order is now processing.');
    }

    public function complete(Order $order): RedirectResponse
    {
        $this->orderService->complete($order); 
        return back()->with('status', 'Order marked as completed.');
    }

    public function cancel(Order $order): RedirectResponse
    {
        $this->orderService->cancel($order); 
        return back()->with('status', 'Order cancelled.');
    }

}