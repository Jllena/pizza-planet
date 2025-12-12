<?php

namespace Database\Seeders;

use App\Models\Pizza;
use App\Models\Topping;
use Illuminate\Database\Seeder;

class PizzaSeeder extends Seeder
{
    public function run(): void
    {
        $toppings = collect([
            'ham',
            'olives',
            'mushrooms',
            'bacon',
            'mince',
            'pepperoni',
            'spicy mince',
            'onion',
            'green pepper',
            'jalapenos',
        ])->mapWithKeys(function (string $name) {
            $topping = Topping::firstOrCreate(
                ['name' => $name],
                ['price' => 1, 'currency' => 'GBP']
            );

            return [$name => $topping->id];
        });

        $pizzas = [
            [
                'name' => 'Margherita',
                'price' => 10,
                'currency' => 'GBP',
                'is_custom' => false,
                'toppings' => [],
            ],
            [
                'name' => 'Romana',
                'price' => 13,
                'currency' => 'GBP',
                'is_custom' => false,
                'toppings' => ['ham', 'olives', 'mushrooms'],
            ],
            [
                'name' => 'Americana',
                'price' => 13,
                'currency' => 'USD',
                'is_custom' => false,
                'toppings' => ['bacon', 'mince', 'pepperoni'],
            ],
            [
                'name' => 'Mexicana',
                'price' => 15,
                'currency' => 'GBP',
                'is_custom' => false,
                'toppings' => ['spicy mince', 'onion', 'green pepper', 'jalapenos'],
            ],
            [
                'name' => 'Custom Pizza',
                'price' => 10,
                'currency' => 'GBP',
                'is_custom' => true,
                'toppings' => [],
            ],
        ];

        foreach ($pizzas as $pizzaData) {
            $toppingIds = collect($pizzaData['toppings'] ?? [])
                ->map(fn ($name) => $toppings[$name])
                ->values();

            $pizza = Pizza::updateOrCreate(
                ['name' => $pizzaData['name']],
                [
                    'price' => $pizzaData['price'],
                    'currency' => $pizzaData['currency'],
                    'is_custom' => $pizzaData['is_custom'],
                ]
            );

            if ($toppingIds->isNotEmpty()) {
                $pizza->toppings()->sync($toppingIds);
            }
        }
    }
}
