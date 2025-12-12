<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topping extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'currency',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function pizzas()
    {
        return $this->belongsToMany(Pizza::class)->withTimestamps();
    }

    public function formattedPrice(): string
    {
        $symbol = $this->currency === 'USD' ? '$' : '£';

        return $symbol.number_format((float) $this->price, 2);
    }
}
