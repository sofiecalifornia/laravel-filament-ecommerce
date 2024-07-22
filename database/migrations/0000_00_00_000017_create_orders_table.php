<?php

declare(strict_types=1);

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
            $table->uuid()->primary()->unique();

            $table->foreignIdFor(Branch::class)
                ->constrained(column: 'uuid');
            $table->foreignIdFor(Customer::class)
                ->constrained(column: 'uuid');

            $table->string('receipt_number')->unique();
            $table->money('delivery_price');
            $table->money('total_price');

            $table->text('notes')->nullable();

            $table->phpEnum('payment_method')->nullable();
            $table->phpEnum('payment_status')->default(PaymentStatus::pending->value);
            $table->phpEnum('status')->default(Status::pending->value);
            $table->phpEnum('claim_type');

            $table->dateTime('claim_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->uuid()->primary()->unique();

            $table->foreignIdFor(Order::class)
                ->constrained(column: 'uuid')
                ->cascadeOnDelete();
            $table->foreignIdFor(Sku::class)
                ->constrained(column: 'uuid');

            $table->string('sku_code');
            $table->string('name');
            $table->text('description')->nullable();
            $table->money('price');
            $table->money('discount_price');
            $table->money('total_price');
            $table->float('quantity')
                ->unsigned()
                ->comment('customer actual quantity');
            $table->float('paid_quantity')
                ->unsigned();
            $table->float('minimum')
                ->unsigned()
                ->nullable();
            $table->float('maximum')
                ->unsigned()
                ->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->unique([
                'order_uuid',
                'sku_uuid',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
