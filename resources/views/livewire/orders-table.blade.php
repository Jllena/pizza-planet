<div class="p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Recent orders</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Search by customer name or order ID.</p>
        </div>

        <div class="w-full sm:w-72">
            <label class="sr-only" for="order-search">Search orders</label>
            <input
                id="order-search"
                type="search"
                placeholder="e.g. Sarah or 152"
                wire:model.live.debounce.300ms="search"
                class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500"
            >
        </div>
    </div>

    <div class="overflow-hidden border border-gray-200 dark:border-gray-700 rounded-lg">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900/50">
                <tr>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300">Order</th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300">Customer</th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300">Pizza</th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300">Payment</th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300">Total</th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300">Placed</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                @forelse ($orders as $order)
                    <tr wire:key="order-{{ $order->id }}">
                        <td class="px-4 py-3 text-sm font-semibold text-gray-900 dark:text-gray-100">
                            #{{ $order->id }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-200">
                            {{ $order->customer_name ?: 'Guest' }}
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $order->topping_count }} topping{{ $order->topping_count === 1 ? '' : 's' }}
                            </div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-200">
                            <div class="flex items-center gap-2">
                                <span class="font-medium text-gray-900 dark:text-gray-100">{{ $order->pizza->name ?? 'Unknown pizza' }}</span>
                                @if ($order->is_custom)
                                    <span class="rounded-full bg-indigo-100 text-indigo-800 dark:bg-indigo-900/60 dark:text-indigo-200 px-2 py-0.5 text-xs font-semibold">Custom</span>
                                @endif
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $order->toppings->pluck('name')->implode(', ') ?: 'No toppings recorded' }}
                            </div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-200">
                            {{ ucfirst($order->payment_method) }}
                        </td>
                        <td class="px-4 py-3 text-sm font-semibold text-gray-900 dark:text-gray-100">
                            {{ $order->formattedTotal() }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-200">
                            <div>{{ optional($order->created_at)->format('M j, Y g:i A') }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ optional($order->created_at)->diffForHumans() }}</div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-600 dark:text-gray-300">
                            @if ($search)
                                No orders matched “{{ $search }}”.
                            @else
                                No orders yet. New orders will appear here.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if ($orders->hasPages())
            <div class="px-4 py-3 bg-gray-50 dark:bg-gray-900/60 border-t border-gray-200 dark:border-gray-700">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
