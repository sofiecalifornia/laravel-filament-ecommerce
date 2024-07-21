<?php

declare(strict_types=1);

use Domain\Shop\Branch\Models\Branch;
use Domain\Shop\Customer\Models\Customer;
use Domain\Shop\Product\Models\Product;
use Domain\Shop\Product\Models\Sku;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Customer::class)
                ->constrained();
            $table->foreignIdFor(Branch::class)
                ->constrained();
            $table->foreignIdFor(Product::class)
                ->constrained();
            $table->foreignIdFor(Sku::class)
                ->constrained();

            $table->string('sku_code');
            $table->string('product_name');
            $table->money('price');
            $table->unsignedFloat('quantity');
            $table->unsignedFloat('minimum')->nullable();
            $table->unsignedFloat('maximum')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
