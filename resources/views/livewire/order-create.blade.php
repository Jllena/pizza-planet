<div class="max-w-5xl mx-auto py-8 px-4 space-y-8">
    <div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Pizza Planet Orders</h1>
        <p class="mt-2 text-gray-600 dark:text-gray-300">Pick a pizza or build your own. Custom pizzas cost £10 plus £1 per topping (up to 4 toppings).</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Menu</h2>
            <div class="mt-4 overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900/40">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Pizza</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Toppings</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Price</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($pizzas as $pizza)
                            <tr class="align-top">
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $pizza->name }}
                                    @if ($pizza->is_custom)
                                        <span class="ml-2 rounded-full bg-indigo-100 text-indigo-800 dark:bg-indigo-900/60 dark:text-indigo-200 px-2 py-0.5 text-xs font-semibold">Custom</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-200">
                                    @if ($pizza->toppings->isEmpty())
                                        <span class="text-gray-500 dark:text-gray-400">No preset toppings</span>
                                    @else
                                        {{ $pizza->toppings->pluck('name')->implode(', ') }}
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $pizza->formattedPrice() }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Place an order</h2>

            @if ($errors->any())
                <div class="mt-4 rounded-lg bg-red-50 text-red-800 dark:bg-red-900/50 dark:text-red-100 px-4 py-3">
                    <ul class="list-disc list-inside space-y-1 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form wire:submit.prevent="placeOrder" class="mt-4 space-y-4">
                <div>
                    <label for="customer_name" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Customer name (optional)</label>
                    <input type="text" id="customer_name" wire:model.defer="customerName" class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="pizza_id" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Choose a pizza</label>
                    <select id="pizza_id" wire:model.live="selectedPizzaId" class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500">
                        @foreach ($pizzas as $pizza)
                            <option value="{{ $pizza->id }}">
                                {{ $pizza->name }} ({{ $pizza->formattedPrice() }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Toppings (custom pizzas only, max 4)</label>
                    <div class="mt-2 grid grid-cols-2 sm:grid-cols-3 gap-2">
                        @foreach ($toppings as $topping)
                            @php
                                $atLimit = count($selectedToppings ?? []) >= 4;
                                $checked = in_array($topping->id, $selectedToppings ?? []);
                            @endphp
                            <label wire:key="topping-{{ $topping->id }}" class="flex items-center space-x-2 text-sm text-gray-700 dark:text-gray-200 {{ $isCustom ? '' : 'opacity-50' }}">
                                <input
                                    type="checkbox"
                                    value="{{ $topping->id }}"
                                    wire:model.live="selectedToppings"
                                    @disabled(! $isCustom || ($atLimit && ! $checked))
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                >
                                <span>{{ $topping->name }}</span>
                            </label>
                        @endforeach
                    </div>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">If you pick a preset pizza, we’ll use its default toppings.</p>
                </div>

                <div>
                    <label for="payment_method" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Payment method</label>
                    <select id="payment_method" wire:model="paymentMethod" class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="card">Card</option>
                        <option value="paypal">PayPal</option>
                    </select>
                </div>

                <button type="submit" class="inline-flex items-center px-4 py-2 rounded-md bg-indigo-600 text-white font-semibold hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Place order
                </button>
            </form>
        </div>
    </div>
</div>
