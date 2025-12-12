<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'pizza_id',
        'customer_name',
        'payment_method',
        'base_price',
        'topping_price',
        'total_price',
        'currency',
        'is_custom',
        'topping_count',
    ];

    protected $casts = [
        'is_custom' => 'boolean',
        'base_price' => 'decimal:2',
        'topping_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function pizza()
    {
        return $this->belongsTo(Pizza::class);
    }

    public function toppings()
    {
        return $this->belongsToMany(Topping::class)->withTimestamps();
    }

    public function formattedTotal(): string
    {
        return $this->currencySymbol().number_format((float) $this->total_price, 2);
    }

    public function currencySymbol(): string
    {
        return $this->currency === 'USD' ? '$' : '£';
    }
}
