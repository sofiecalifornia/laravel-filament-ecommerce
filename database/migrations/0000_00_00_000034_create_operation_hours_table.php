<?php

declare(strict_types=1);

use Domain\Shop\Branch\Models\Branch;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('operation_hours', function (Blueprint $table) {
            $table->uuid()->primary()->unique();

            $table->foreignIdFor(Branch::class)
                ->constrained(column: 'uuid')
                ->cascadeOnDelete();

            $table->phpEnum('day');
            $table->phpEnum('type');
            $table->boolean('is_all_day');
            $table->boolean('is_open');

            $table->eloquentSortable();

            $table->time('from');
            $table->time('to');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operation_hours');
    }
};
