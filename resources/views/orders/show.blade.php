@extends('layouts.dashboard')

@section('title', 'Order Details')

@section('content')
    @php
        $statusValue = $order->current_status?->value ?? 'pending';
        $statusLabel = $order->current_status?->label() ?? 'Pending';
        $statusColorMap = [
            'pending' => 'bg-slate-100 text-slate-700 border-slate-200',
            'processing' => 'bg-blue-100 text-blue-700 border-blue-200',
            'completed' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
            'cancelled' => 'bg-red-100 text-red-700 border-red-200',
            'on_hold' => 'bg-amber-100 text-amber-700 border-amber-200',
        ];
        $statusClass = $statusColorMap[$statusValue] ?? 'bg-slate-100 text-slate-700 border-slate-200';
    @endphp

    <div class="mx-auto max-w-7xl space-y-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900">Order #{{ $order->id }}</h1>
                <p class="mt-1 text-sm text-slate-500">
                    Created {{ optional($order->created_at)->format('d M Y, h:i A') }}
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a
                    href="{{ route('orders.index') }}"
                    class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-300"
                >
                    Back to Orders
                </a>
                <a
                    href="{{ route('orders.create') }}"
                    class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-400"
                >
                    New Order
                </a>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-4">
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Order Status</p>
                <span class="mt-2 inline-flex rounded-full border px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">
                    {{ $statusLabel }}
                </span>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Bottle Items</p>
                <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $order->items->count() }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Total Bottles</p>
                <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $order->total_bottles_quantity }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Grand Total</p>
                <p class="mt-2 text-2xl font-semibold text-emerald-600">${{ number_format($order->total_price, 2) }}</p>
            </div>
        </div>

        <section class="rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-5 py-4">
                <h2 class="text-base font-semibold text-slate-900">Status Actions</h2>
            </div>
            <div class="flex flex-wrap gap-2 px-5 py-4">
                @if($statusValue !== 'cancelled' && $statusValue !== 'completed')
                    @if($statusValue === 'pending')
                        <form method="POST" action="{{ route('orders.processing', $order->id) }}">
                            @csrf
                            <button type="submit" class="rounded-lg border border-blue-200 px-3 py-2 text-xs font-medium text-blue-700 transition hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-300">
                                Start Processing
                            </button>
                        </form>
                    @endif

                    @if($statusValue === 'processing')
                        <form method="POST" action="{{ route('orders.complete', $order->id) }}">
                            @csrf
                            <button type="submit" class="rounded-lg border border-emerald-200 px-3 py-2 text-xs font-medium text-emerald-700 transition hover:bg-emerald-50 focus:outline-none focus:ring-2 focus:ring-emerald-300">
                                Mark Completed
                            </button>
                        </form>
                    @endif

                    <form method="POST" action="{{ route('orders.cancel', $order->id) }}" onsubmit="return confirm('Cancel this order?')">
                        @csrf
                        <button type="submit" class="rounded-lg border border-red-200 px-3 py-2 text-xs font-medium text-red-700 transition hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-300">
                            Cancel Order
                        </button>
                    </form>
                @endif
            </div>
        </section>

        <section class="space-y-4">
            <h2 class="text-base font-semibold text-slate-900">Order Items</h2>

            @forelse($order->items as $itemIndex => $item)
                @php
                    // Service logic:
                    // item_total = bottle_total_price + (sum(ingredient sold_total_price) * bottle_quantity)
                    $perBottleIngredientsTotal = $item->ingredients->sum('sold_total_price');
                    $totalIngredientsForItem = $perBottleIngredientsTotal * $item->bottle_quantity;
                    $perBottleIngredientsGrams = $item->ingredients->sum('sold_quantity_grams');
                    $totalIngredientsGramsForItem = $perBottleIngredientsGrams * $item->bottle_quantity;
                    $perBottleTotal = $item->bottle_price + $perBottleIngredientsTotal;
                @endphp
                <article class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4">
                        <div>
                            <h3 class="text-sm font-semibold text-slate-900">
                                Item #{{ $itemIndex + 1 }} - {{ optional($item->bottle)->name ?? 'Bottle' }}
                            </h3>
                            <p class="mt-1 text-xs text-slate-500">
                                Size: {{ optional($item->bottle)->size ?? 0 }}g | Quantity: {{ $item->bottle_quantity }}
                            </p>
                        </div>
                        <p class="text-sm font-semibold text-slate-900">
                            Item Total: ${{ number_format($item->bottle_total_price_with_ingredients, 2) }}
                        </p>
                    </div>

                    <div class="grid gap-4 px-5 py-4 md:grid-cols-3">
                        <div class="rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 text-sm">
                            <p class="text-slate-500">Bottle Total (Qty x Price)</p>
                            <p class="mt-1 font-semibold text-slate-900">
                                {{ $item->bottle_quantity }} x ${{ number_format($item->bottle_price, 2) }} = ${{ number_format($item->bottle_total_price, 2) }}
                            </p>
                        </div>
                        <div class="rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 text-sm">
                            <p class="text-slate-500">Ingredients Total (Per Bottle x Qty)</p>
                            <p class="mt-1 font-semibold text-slate-900">
                                ${{ number_format($perBottleIngredientsTotal, 2) }} x {{ $item->bottle_quantity }} = ${{ number_format($totalIngredientsForItem, 2) }}
                            </p>
                        </div>
                        <div class="rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 text-sm">
                            <p class="text-slate-500">Ingredients Grams (Per Bottle x Qty)</p>
                            <p class="mt-1 font-semibold text-slate-900">
                                {{ $perBottleIngredientsGrams }}g x {{ $item->bottle_quantity }} = {{ $totalIngredientsGramsForItem }}g
                            </p>
                        </div>
                    </div>

                    <div class="px-5 pb-4">
                        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                            <span class="font-semibold">Calculation:</span>
                            (Per bottle total ${{ number_format($perBottleTotal, 2) }}) x {{ $item->bottle_quantity }}
                            = ${{ number_format($item->bottle_total_price_with_ingredients, 2) }}
                        </div>
                    </div>

                    <div class="px-5 pb-5">
                        <div class="overflow-x-auto rounded-lg border border-slate-200">
                            <table class="min-w-full divide-y divide-slate-200 text-sm">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Ingredient</th>
                                        <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Type</th>
                                        <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Qty / Bottle (g)</th>
                                        <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Unit Price</th>
                                        <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Line / Bottle</th>
                                        <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Line / Item</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 bg-white">
                                    @forelse($item->ingredients as $ingredientLine)
                                        <tr>
                                            <td class="px-4 py-2 text-slate-700">
                                                {{ optional(optional($ingredientLine->ingredientType)->ingredient)->name ?? 'N/A' }}
                                            </td>
                                            <td class="px-4 py-2 text-slate-700">
                                                {{ optional($ingredientLine->ingredientType)->name ?? 'N/A' }}
                                            </td>
                                            <td class="px-4 py-2 text-slate-700">{{ $ingredientLine->sold_quantity_grams }}</td>
                                            <td class="px-4 py-2 text-slate-700">
                                                ${{ number_format($ingredientLine->price, 2) }} / {{ $ingredientLine->for_gram }}g
                                            </td>
                                            <td class="px-4 py-2 font-medium text-slate-900">
                                                ${{ number_format($ingredientLine->sold_total_price, 2) }}
                                            </td>
                                            <td class="px-4 py-2 font-medium text-slate-900">
                                                ${{ number_format($ingredientLine->sold_total_price * $item->bottle_quantity, 2) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-4 py-6 text-center text-sm text-slate-500">
                                                No ingredient lines for this item.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </article>
            @empty
                <div class="rounded-xl border border-dashed border-slate-300 bg-white p-10 text-center text-slate-500">
                    This order has no items.
                </div>
            @endforelse
        </section>
    </div>
@endsection
