<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Models\Pizza;
use App\Models\Topping;
use PHPUnit\Framework\TestCase;

class MoneyFormattingTest extends TestCase
{
    /** @test */
    public function it_formats_order_totals_with_currency_symbol(): void
    {
        $gbpOrder = new Order(['currency' => 'GBP', 'total_price' => 12.5]);
        $usdOrder = new Order(['currency' => 'USD', 'total_price' => 18.4]);

        $this->assertSame('£12.50', $gbpOrder->formattedTotal());
        $this->assertSame('$18.40', $usdOrder->formattedTotal());
    }

    /** @test */
    public function it_formats_pizza_prices(): void
    {
        $gbpPizza = new Pizza(['currency' => 'GBP', 'price' => 9]);
        $usdPizza = new Pizza(['currency' => 'USD', 'price' => 11.75]);

        $this->assertSame('£9.00', $gbpPizza->formattedPrice());
        $this->assertSame('$11.75', $usdPizza->formattedPrice());
    }

    /** @test */
    public function it_formats_topping_prices(): void
    {
        $gbpTopping = new Topping(['currency' => 'GBP', 'price' => 1.5]);
        $usdTopping = new Topping(['currency' => 'USD', 'price' => 2.25]);

        $this->assertSame('£1.50', $gbpTopping->formattedPrice());
        $this->assertSame('$2.25', $usdTopping->formattedPrice());
    }
}
