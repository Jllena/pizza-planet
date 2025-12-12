<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pizza_id')->constrained()->cascadeOnDelete();
            $table->string('customer_name')->nullable();
            $table->string('payment_method');
            $table->decimal('base_price', 8, 2);
            $table->decimal('topping_price', 8, 2)->default(0);
            $table->decimal('total_price', 8, 2);
            $table->string('currency', 3)->default('GBP');
            $table->boolean('is_custom')->default(false);
            $table->unsignedTinyInteger('topping_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
