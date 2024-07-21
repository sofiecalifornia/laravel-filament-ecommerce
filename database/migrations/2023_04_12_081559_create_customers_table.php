<?php

declare(strict_types=1);

use Domain\Access\Admin\Models\Admin;
use Domain\Shop\Customer\Enums\Status;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Admin::class)
                ->nullable()
                ->constrained();

            $table->string('reference_number')->unique();
            $table->string('email')->unique()->nullable();
            $table->string('password')->nullable();

            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('mobile')->nullable();
            $table->phpEnum('gender')->nullable();
            $table->phpEnum('status')->default(Status::ACTIVE->value);

            $table->string('timezone')->default('Asia/Manila');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
