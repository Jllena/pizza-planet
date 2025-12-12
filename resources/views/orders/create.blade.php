<x-guest-layout>
    <div class="max-w-5xl mx-auto py-8 px-4">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Pizza Planet Orders</h1>
        <p class="mt-2 text-gray-600 dark:text-gray-300">Pick a pizza or build your own. Custom pizzas cost £10 plus £1 per topping (up to 4 toppings).</p>

        <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-8">
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

                <form action="{{ route('orders.store') }}" method="POST" class="mt-4 space-y-4">
                    @csrf
                    <div>
                        <label for="customer_name" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Customer name (optional)</label>
                        <input type="text" name="customer_name" id="customer_name" value="{{ old('customer_name') }}" class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label for="pizza_id" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Choose a pizza</label>
                        <select name="pizza_id" id="pizza_id" class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500">
                            @foreach ($pizzas as $pizza)
                                <option value="{{ $pizza->id }}" data-custom="{{ $pizza->is_custom ? '1' : '0' }}" @selected(old('pizza_id') == $pizza->id)>
                                    {{ $pizza->name }} ({{ $pizza->formattedPrice() }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Toppings (custom pizzas only, max 4)</label>
                        <div class="mt-2 grid grid-cols-2 sm:grid-cols-3 gap-2">
                            @foreach ($toppings as $topping)
                                <label class="flex items-center space-x-2 text-sm text-gray-700 dark:text-gray-200">
                                    <input type="checkbox" name="toppings[]" value="{{ $topping->id }}" @checked(in_array($topping->id, old('toppings', []))) class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 topping-option">
                                    <span>{{ $topping->name }}</span>
                                </label>
                            @endforeach
                        </div>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">If you pick a preset pizza, we’ll use its default toppings.</p>
                    </div>

                    <div>
                        <label for="payment_method" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Payment method</label>
                        <select name="payment_method" id="payment_method" class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="card" @selected(old('payment_method') === 'card')>Card</option>
                            <option value="paypal" @selected(old('payment_method') === 'paypal')>PayPal</option>
                        </select>
                    </div>

                    <button type="submit" class="inline-flex items-center px-4 py-2 rounded-md bg-indigo-600 text-white font-semibold hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Place order
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const select = document.getElementById('pizza_id');
            const checkboxes = Array.from(document.querySelectorAll('.topping-option'));

            const updateToppingState = () => {
                const selected = select.options[select.selectedIndex];
                const isCustom = selected?.dataset.custom === '1';

                checkboxes.forEach((checkbox) => {
                    checkbox.disabled = !isCustom;
                    checkbox.parentElement.classList.toggle('opacity-50', !isCustom);
                    if (!isCustom) {
                        checkbox.checked = false;
                    }
                });
            };

            select.addEventListener('change', updateToppingState);
            updateToppingState();
        });
    </script>
@endpush
