<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Models\Pizza;
use App\Models\Topping;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function create()
    {
        $pizzas = Pizza::with('toppings')->orderBy('is_custom')->orderBy('name')->get();
        $toppings = Topping::orderBy('name')->get();

        return view('orders.create', compact('pizzas', 'toppings'));
    }

    public function store(StoreOrderRequest $request)
    {
        $data = $request->validated();
        $pizza = Pizza::with('toppings')->findOrFail($data['pizza_id']);

        $selectedToppingIds = collect($data['toppings'] ?? []);
        $toppings = $pizza->is_custom
            ? Topping::whereIn('id', $selectedToppingIds)->get()
            : collect();

        if ($pizza->is_custom && $toppings->count() > 4) {
            return back()->withErrors(['toppings' => 'You can choose up to 4 toppings.'])->withInput();
        }

        $finalToppings = $pizza->is_custom ? $toppings : $pizza->toppings;
        $toppingCost = $pizza->is_custom ? (float) $toppings->sum('price') : 0;
        $basePrice = (float) $pizza->price;
        $total = $basePrice + $toppingCost;

        $order = Order::create([
            'pizza_id' => $pizza->id,
            'customer_name' => $data['customer_name'] ?? null,
            'payment_method' => $data['payment_method'],
            'base_price' => $basePrice,
            'topping_price' => $toppingCost,
            'total_price' => $total,
            'currency' => $pizza->currency,
            'is_custom' => $pizza->is_custom,
            'topping_count' => $finalToppings->count(),
        ]);

        // Attach toppings for custom orders, or record the default toppings on named pizzas.
        $order->toppings()->sync($finalToppings->pluck('id'));

        Log::info('Payment logged for pizza order', [
            'order_id' => $order->id,
            'payment_method' => $order->payment_method,
            'amount' => $order->total_price,
            'currency' => $order->currency,
        ]);

        return redirect()->route('orders.show', $order);
    }

    public function show(Order $order)
    {
        $order->load(['pizza', 'toppings']);

        return view('orders.show', compact('order'));
    }
}
