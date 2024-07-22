<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->uuid()->primary()->unique();

            $table->foreignUuid('parent_uuid')
                ->nullable()
                ->constrained(table: 'categories', column: 'uuid')
                ->cascadeOnDelete();

            $table->string('name');
            $table->longText('description')->nullable();
            $table->boolean('is_visible')->default(false);

            $table->eloquentSortable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
