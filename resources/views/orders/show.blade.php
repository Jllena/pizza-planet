<x-guest-layout>
    <div class="max-w-3xl mx-auto py-10 px-4">
        <div class="rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Order confirmation</h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">Payment logged via {{ ucfirst($order->payment_method) }}.</p>
            </div>
            <div class="px-6 py-4 space-y-3">
                <div class="flex justify-between text-sm text-gray-700 dark:text-gray-200">
                    <span>Pizza</span>
                    <span class="font-semibold">{{ $order->pizza->name }}</span>
                </div>
                <div class="flex justify-between text-sm text-gray-700 dark:text-gray-200">
                    <span>Toppings</span>
                    @if ($order->toppings->isEmpty())
                        <span class="text-gray-500 dark:text-gray-400">No toppings</span>
                    @else
                        <span class="text-right">{{ $order->toppings->pluck('name')->implode(', ') }}</span>
                    @endif
                </div>
                <div class="flex justify-between text-sm text-gray-700 dark:text-gray-200">
                    <span>Base price</span>
                    <span>{{ $order->currencySymbol() }}{{ number_format((float) $order->base_price, 2) }}</span>
                </div>
                <div class="flex justify-between text-sm text-gray-700 dark:text-gray-200">
                    <span>Toppings total</span>
                    <span>{{ $order->currencySymbol() }}{{ number_format((float) $order->topping_price, 2) }}</span>
                </div>
                <div class="flex justify-between text-base font-semibold text-gray-900 dark:text-white pt-2 border-t border-gray-200 dark:border-gray-700">
                    <span>Total</span>
                    <span>{{ $order->formattedTotal() }}</span>
                </div>
            </div>
        </div>

        <div class="mt-6">
            <a href="{{ route('orders.create') }}" class="text-indigo-600 dark:text-indigo-300 hover:underline font-semibold">Place another order</a>
        </div>
    </div>
</x-guest-layout>
