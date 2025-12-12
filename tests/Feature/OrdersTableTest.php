<?php

namespace Tests\Feature;

use App\Livewire\OrdersTable;
use App\Models\Order;
use App\Models\Pizza;
use Database\Seeders\PizzaSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OrdersTableTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_lists_recent_orders(): void
    {
        $this->seed(PizzaSeeder::class);

        $pizza = Pizza::first();
        $order = $this->makeOrder($pizza, ['customer_name' => 'Sarah Connor']);

        Livewire::test(OrdersTable::class)
            ->assertSee('#'.$order->id)
            ->assertSee($pizza->name)
            ->assertSee('Sarah Connor')
            ->assertSee($order->formattedTotal());
    }

    /** @test */
    public function it_filters_by_customer_name(): void
    {
        $this->seed(PizzaSeeder::class);

        $pizza = Pizza::first();
        $this->makeOrder($pizza, ['customer_name' => 'Alice Alpha']);
        $this->makeOrder($pizza, ['customer_name' => 'Bob Bravo']);

        Livewire::test(OrdersTable::class)
            ->set('search', 'Bob')
            ->assertSee('Bob Bravo')
            ->assertDontSee('Alice Alpha');
    }

    /** @test */
    public function it_filters_by_order_id_when_search_is_numeric(): void
    {
        $this->seed(PizzaSeeder::class);

        $pizza = Pizza::first();
        $keep = $this->makeOrder($pizza, ['customer_name' => 'Id Match']);
        $hide = $this->makeOrder($pizza, ['customer_name' => 'Other Name']);

        Livewire::test(OrdersTable::class)
            ->set('search', (string) $keep->id)
            ->assertSee('#'.$keep->id)
            ->assertDontSee('#'.$hide->id)
            ->assertDontSee('Other Name');
    }

    /** @test */
    public function it_resets_to_first_page_when_search_changes(): void
    {
        $this->seed(PizzaSeeder::class);

        $pizza = Pizza::first();
        // Create enough orders to move away from the first page.
        foreach (range(1, 12) as $i) {
            $this->makeOrder($pizza, ['customer_name' => "Customer {$i}"]);
        }

        Livewire::test(OrdersTable::class)
            ->call('setPage', 2)
            ->assertSet('paginators.page', 2)
            ->set('search', 'Customer 1')
            ->assertSet('paginators.page', 1);
    }

    private function makeOrder(Pizza $pizza, array $overrides = []): Order
    {
        $pizza->load('toppings');
        $toppingIds = $pizza->toppings->pluck('id');

        $order = Order::create(array_merge([
            'pizza_id' => $pizza->id,
            'customer_name' => null,
            'payment_method' => 'card',
            'base_price' => $pizza->price,
            'topping_price' => 0,
            'total_price' => $pizza->price,
            'currency' => $pizza->currency,
            'is_custom' => $pizza->is_custom,
            'topping_count' => $toppingIds->count(),
        ], $overrides));

        $order->toppings()->sync($toppingIds);

        return $order;
    }
}
