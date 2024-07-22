<?php

declare(strict_types=1);

use Domain\Access\Admin\Models\Admin;
use Domain\Shop\Branch\Models\Branch;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_branch', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Admin::class)
                ->constrained(column: 'uuid')
                ->cascadeOnDelete();
            $table->foreignIdFor(Branch::class)
                ->constrained(column: 'uuid')
                ->cascadeOnDelete();

            $table->unique(['admin_uuid', 'branch_uuid']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_branch');
    }
};
