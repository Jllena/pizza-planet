<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pizza extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'currency',
        'is_custom',
    ];

    protected $casts = [
        'is_custom' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function toppings()
    {
        return $this->belongsToMany(Topping::class)->withTimestamps();
    }

    public function formattedPrice(): string
    {
        return $this->currencySymbol().number_format((float) $this->price, 2);
    }

    public function currencySymbol(): string
    {
        return $this->currency === 'USD' ? '$' : '£';
    }
}
