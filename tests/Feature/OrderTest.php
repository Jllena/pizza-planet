<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Pizza;
use App\Models\Topping;
use App\Livewire\OrderCreate;
use Database\Seeders\PizzaSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_calculates_custom_pizza_totals_with_toppings(): void
    {
        $this->seed(PizzaSeeder::class);

        $pizza = Pizza::where('is_custom', true)->firstOrFail();
        $toppings = Topping::take(3)->pluck('id')->toArray();

        Livewire::test(OrderCreate::class)
            ->set('selectedPizzaId', $pizza->id)
            ->set('paymentMethod', 'card')
            ->set('selectedToppings', $toppings)
            ->call('placeOrder')
            ->assertRedirect();

        $order = Order::latest()->first();
        $this->assertEquals(10.00, (float) $order->base_price);
        $this->assertEquals(3.00, (float) $order->topping_price);
        $this->assertEquals(13.00, (float) $order->total_price);
        $this->assertCount(3, $order->toppings);
    }

    /** @test */
    public function it_blocks_more_than_four_custom_toppings(): void
    {
        $this->seed(PizzaSeeder::class);

        $pizza = Pizza::where('is_custom', true)->firstOrFail();
        $toppings = Topping::pluck('id')->take(5)->toArray();

        Livewire::test(OrderCreate::class)
            ->set('selectedPizzaId', $pizza->id)
            ->set('paymentMethod', 'paypal')
            ->set('selectedToppings', $toppings)
            ->call('placeOrder')
            ->assertHasErrors(['selectedToppings' => 'max']);

        $this->assertEquals(0, Order::count());
    }

    /** @test */
    public function preset_pizza_ignores_custom_toppings_and_uses_defaults(): void
    {
        $this->seed(PizzaSeeder::class);

        $preset = Pizza::where('is_custom', false)->firstOrFail();
        $extraToppings = Topping::take(2)->pluck('id')->toArray();

        Livewire::test(OrderCreate::class)
            ->set('selectedPizzaId', $preset->id)
            ->set('paymentMethod', 'card')
            ->set('selectedToppings', $extraToppings) // should be ignored
            ->call('placeOrder')
            ->assertRedirect();

        $preset->load('toppings');
        $order = Order::latest()->with('toppings')->first();
        $this->assertEquals($preset->price, (float) $order->total_price);
        $this->assertEquals($preset->toppings->count(), $order->toppings->count());
        $this->assertEquals(
            $preset->toppings->pluck('id')->sort()->values(),
            $order->toppings->pluck('id')->sort()->values()
        );
    }

    /** @test */
    public function usd_pizza_keeps_currency_and_format(): void
    {
        $this->seed(PizzaSeeder::class);

        $usdPizza = Pizza::where('name', 'Americana')->firstOrFail();

        Livewire::test(OrderCreate::class)
            ->set('selectedPizzaId', $usdPizza->id)
            ->set('paymentMethod', 'card')
            ->call('placeOrder')
            ->assertRedirect();

        $order = Order::latest()->first();
        $this->assertEquals('USD', $order->currency);
        $this->assertEquals((float) $usdPizza->price, (float) $order->total_price);
    }

    /** @test */
    public function invalid_payment_method_fails_validation(): void
    {
        $this->seed(PizzaSeeder::class);
        $pizza = Pizza::firstOrFail();

        Livewire::test(OrderCreate::class)
            ->set('selectedPizzaId', $pizza->id)
            ->set('paymentMethod', 'cash')
            ->call('placeOrder')
            ->assertHasErrors(['paymentMethod' => 'in']);
    }

    /** @test */
    public function menu_renders_preset_toppings(): void
    {
        $this->seed(PizzaSeeder::class);

        Livewire::test(OrderCreate::class)
            ->assertSee('Romana')
            ->assertSee('ham')
            ->assertSee('olives')
            ->assertSee('mushrooms');
    }
}
