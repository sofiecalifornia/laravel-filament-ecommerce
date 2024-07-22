<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->uuid()->primary()->unique();

            $table->string('code')->unique();
            $table->string('name')->unique();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->phpEnum('status');
            $table->unsignedSmallInteger('maximum_advance_booking_days')->nullable();
            $table->eloquentSortable();

            $table->boolean('is_operation_hours_enabled')->default(false);

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};
