@extends('layouts.dashboard')

@section('title', 'Orders')

@section('content')
    @php
        $statusColorMap = [
            'pending' => 'bg-slate-100 text-slate-700',
            'processing' => 'bg-blue-100 text-blue-700',
            'completed' => 'bg-emerald-100 text-emerald-700',
            'cancelled' => 'bg-red-100 text-red-700',
            'on_hold' => 'bg-amber-100 text-amber-700',
        ];
    @endphp

    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Order Operations</h1>
            <p class="text-sm text-slate-500">Monitor all transactions and manage order status transitions.</p>
        </div>
        <a
            href="{{ route('orders.create') }}"
            class="inline-flex items-center rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:ring-offset-2"
        >
            Create Order
        </a>
    </div>

    <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-5 py-4">
            <h2 class="text-base font-semibold text-slate-900">Recent Transactions</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Order</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Date</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Bottle Count</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Total</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Status</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($orders as $order)
                        @php
                            $statusValue = $order->current_status->value;
                            $statusLabel = $order->current_status->label();
                            $statusClass = $statusColorMap[$statusValue] ?? 'bg-slate-100 text-slate-700';
                        @endphp
                        <tr class="transition hover:bg-slate-50">
                            <td class="px-5 py-4 text-sm font-semibold text-slate-900">#{{ $order->id }}</td>
                            <td class="px-5 py-4 text-sm text-slate-600">{{ optional($order->created_at)->format('d M Y, h:i A') }}</td>
                            <td class="px-5 py-4 text-sm text-slate-700">
                                <span class="inline-flex min-w-10 justify-center rounded-md bg-slate-100 px-2.5 py-1 font-semibold text-slate-800">
                                    {{ $order->total_bottles_quantity }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-sm font-semibold text-slate-900">${{ number_format($order->total_price, 2) }}</td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $statusClass }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a
                                        href="{{ route('orders.show', $order->id) }}"
                                        class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 transition hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-300"
                                    >
                                        View
                                    </a>

                                    @if($statusValue !== 'cancelled' && $statusValue !== 'completed')
                                        @if($statusValue === 'pending')
                                            <form action="{{ route('orders.processing', $order->id) }}" method="POST">
                                                @csrf
                                                <button
                                                    type="submit"
                                                    class="rounded-lg border border-blue-200 px-3 py-1.5 text-xs font-medium text-blue-600 transition hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-300"
                                                >
                                                    Processing
                                                </button>
                                            </form>
                                        @endif

                                        @if($statusValue === 'processing')
                                            <form action="{{ route('orders.complete', $order->id) }}" method="POST">
                                                @csrf
                                                <button
                                                    type="submit"
                                                    class="rounded-lg border border-emerald-200 px-3 py-1.5 text-xs font-medium text-emerald-600 transition hover:bg-emerald-50 focus:outline-none focus:ring-2 focus:ring-emerald-300"
                                                >
                                                    Complete
                                                </button>
                                            </form>
                                        @endif

                                        <form action="{{ route('orders.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Cancel this order?')">
                                            @csrf
                                            <button
                                                type="submit"
                                                class="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-medium text-red-600 transition hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-300"
                                            >
                                                Cancel
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center">
                                <p class="text-sm font-medium text-slate-600">No orders available yet.</p>
                                <a href="{{ route('orders.create') }}" class="mt-3 inline-block text-sm font-medium text-emerald-600 hover:text-emerald-700">
                                    Create your first order
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($orders, 'links'))
            <div class="border-t border-slate-100 bg-slate-50 px-5 py-3">
                {{ $orders->links() }}
            </div>
        @endif
    </section>
@endsection