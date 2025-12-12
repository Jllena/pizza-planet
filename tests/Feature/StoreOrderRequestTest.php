<?php

namespace Tests\Feature;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Pizza;
use App\Models\Topping;
use Database\Seeders\PizzaSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class StoreOrderRequestTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_accepts_a_valid_custom_order_payload(): void
    {
        $this->seed(PizzaSeeder::class);

        $custom = Pizza::where('is_custom', true)->firstOrFail();
        $toppings = Topping::take(4)->pluck('id')->toArray();

        $validator = $this->makeValidator([
            'pizza_id' => $custom->id,
            'customer_name' => 'Jane Customer',
            'payment_method' => 'card',
            'toppings' => $toppings,
        ]);

        $this->assertTrue($validator->passes(), 'Expected valid custom order payload to pass validation.');
    }

    /** @test */
    public function it_rejects_invalid_payment_methods_and_missing_pizzas(): void
    {
        $this->seed(PizzaSeeder::class);

        $validator = $this->makeValidator([
            'pizza_id' => 9999,
            'payment_method' => 'cash',
            'toppings' => [],
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('pizza_id', $validator->errors()->toArray());
        $this->assertArrayHasKey('payment_method', $validator->errors()->toArray());
    }

    /** @test */
    public function it_limits_toppings_to_four_distinct_existing_entries(): void
    {
        $this->seed(PizzaSeeder::class);

        $custom = Pizza::where('is_custom', true)->firstOrFail();
        $toppingIds = Topping::take(3)->pluck('id')->toArray();
        $payloadToppings = [$toppingIds[0], $toppingIds[0], 9999, $toppingIds[1], $toppingIds[2]];

        $validator = $this->makeValidator([
            'pizza_id' => $custom->id,
            'payment_method' => 'paypal',
            'toppings' => $payloadToppings,
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('toppings', $validator->errors()->toArray());
        $this->assertArrayHasKey('toppings.1', $validator->errors()->toArray()); // duplicate
        $this->assertArrayHasKey('toppings.2', $validator->errors()->toArray()); // non-existent id
    }

    private function makeValidator(array $data)
    {
        $request = new StoreOrderRequest();

        return Validator::make($data, $request->rules(), $request->messages());
    }
}
