<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\Pizza;
use App\Models\Topping;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class OrderCreate extends Component
{
    public ?int $selectedPizzaId = null;
    public array $selectedToppings = [];
    public ?string $customerName = null;
    public string $paymentMethod = 'card';
    public bool $isCustom = false;

    public function mount(): void
    {
        $this->selectedPizzaId = Pizza::orderBy('is_custom')->orderBy('name')->value('id');
        $this->refreshIsCustom();
    }

    public function updatedSelectedPizzaId(): void
    {
        $this->refreshIsCustom();

        if (! $this->isCustom) {
            $this->selectedToppings = [];
        }
    }

    public function render()
    {
        return view('livewire.order-create', [
            'pizzas' => Pizza::with('toppings')->orderBy('is_custom')->orderBy('name')->get(),
            'toppings' => Topping::orderBy('name')->get(),
            'isCustom' => $this->isCustom,
        ])->layout('layouts.guest');
    }

    public function placeOrder()
    {
        $this->refreshIsCustom();
        $this->validate($this->rules(), $this->messages());

        $pizza = Pizza::with('toppings')->findOrFail($this->selectedPizzaId);
        $toppings = $this->isCustom
            ? Topping::whereIn('id', $this->selectedToppings)->get()
            : $pizza->toppings;

        $toppingCost = $this->isCustom ? (float) $toppings->sum('price') : 0;
        $basePrice = (float) $pizza->price;
        $total = $basePrice + $toppingCost;

        $order = Order::create([
            'pizza_id' => $pizza->id,
            'customer_name' => $this->customerName,
            'payment_method' => $this->paymentMethod,
            'base_price' => $basePrice,
            'topping_price' => $toppingCost,
            'total_price' => $total,
            'currency' => $pizza->currency,
            'is_custom' => $this->isCustom,
            'topping_count' => $toppings->count(),
        ]);

        $order->toppings()->sync($toppings->pluck('id'));

        Log::info('Payment logged for pizza order', [
            'order_id' => $order->id,
            'payment_method' => $order->payment_method,
            'amount' => $order->total_price,
            'currency' => $order->currency,
        ]);

        return redirect()->route('orders.show', $order);
    }

    public function rules(): array
    {
        return [
            'selectedPizzaId' => ['required', 'exists:pizzas,id'],
            'customerName' => ['nullable', 'string', 'max:255'],
            'paymentMethod' => ['required', 'in:card,paypal'],
            'selectedToppings' => ['array', 'max:4'],
            'selectedToppings.*' => ['integer', 'exists:toppings,id', 'distinct'],
        ];
    }

    public function messages(): array
    {
        return [
            'selectedToppings.max' => 'Custom pizzas can have up to 4 toppings.',
        ];
    }

    protected function isCustomPizza(): bool
    {
        if (! $this->selectedPizzaId) {
            return false;
        }

        return Pizza::whereKey($this->selectedPizzaId)->value('is_custom') ?? false;
    }

    protected function refreshIsCustom(): void
    {
        $this->isCustom = $this->isCustomPizza();
    }
}
