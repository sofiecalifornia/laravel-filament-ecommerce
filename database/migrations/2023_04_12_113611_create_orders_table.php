<?php

declare(strict_types=1);

use Domain\Access\Admin\Models\Admin;
use Domain\Shop\Branch\Models\Branch;
use Domain\Shop\Customer\Models\Customer;
use Domain\Shop\Order\Enums\PaymentStatus;
use Domain\Shop\Order\Enums\Status;
use Domain\Shop\Order\Models\Order;
use Domain\Shop\Product\Models\Sku;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Branch::class)
                ->constrained();
            $table->foreignIdFor(Customer::class)
                ->constrained();
            $table->foreignIdFor(Admin::class)
                ->nullable()
                ->constrained();

            $table->string('receipt_number')->unique();
            $table->money('total_price')->default(0);

            $table->text('notes')->nullable();

            $table->phpEnum('payment_method')->nullable();
            $table->phpEnum('payment_status')->default(PaymentStatus::PENDING->value);
            $table->phpEnum('status')->default(Status::PENDING->value);

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Order::class)
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignIdFor(Sku::class)
                ->constrained();

            $table->string('sku_code');
            $table->string('name');
            $table->money('price');
            $table->money('total_price');
            $table->unsignedFloat('quantity')
                ->comment('customer actual quantity');
            $table->unsignedFloat('paid_quantity');
            $table->unsignedFloat('minimum')->nullable();
            $table->unsignedFloat('maximum')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
