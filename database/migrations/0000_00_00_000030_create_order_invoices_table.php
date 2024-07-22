<?php

declare(strict_types=1);

use Domain\Shop\Order\Models\Order;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_invoices', function (Blueprint $table) {
            $table->uuid()->primary()->unique();

            $table->foreignIdFor(Order::class)
                ->constrained(column: 'uuid')
                ->cascadeOnDelete();

            $table->string('file_name');
            $table->string('disk');
            $table->string('path');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_invoices');
    }
};
