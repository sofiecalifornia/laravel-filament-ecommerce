<?php

declare(strict_types=1);

use Domain\Shop\Branch\Models\Branch;
use Domain\Shop\Product\Models\Sku;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sku_stocks', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Branch::class)
                ->constrained();
            $table->foreignIdFor(Sku::class)
                ->constrained();

            $table->phpEnum('type');

            $table->unsignedFloat('count')->nullable()
                ->comment('when base on stock');

            $table->unsignedFloat('warning')->nullable()
                ->comment('when base on stock');

            $table->timestamps();

            $table->unique(['branch_id', 'sku_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sku_stocks');
    }
};
