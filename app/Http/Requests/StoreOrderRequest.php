<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pizza_id' => ['required', 'exists:pizzas,id'],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'payment_method' => ['required', 'in:card,paypal'],
            'toppings' => ['sometimes', 'array', 'max:4'],
            'toppings.*' => ['integer', 'exists:toppings,id', 'distinct'],
        ];
    }

    public function messages(): array
    {
        return [
            'toppings.max' => 'Custom pizzas can have up to 4 toppings.',
        ];
    }
}
