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
            $table->uuid()->primary()->unique();

            $table->foreignIdFor(Branch::class)
                ->constrained(column: 'uuid');
            $table->foreignIdFor(Sku::class)
                ->constrained(column: 'uuid');

            $table->phpEnum('type');

            $table->float('count')
                ->unsigned()
                ->nullable()
                ->comment('when base on stock');

            $table->float('warning')
                ->unsigned()
                ->nullable()
                ->comment('when base on stock');

            $table->timestamps();

            $table->unique(['branch_uuid', 'sku_uuid']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sku_stocks');
    }
};
