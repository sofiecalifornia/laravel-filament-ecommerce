<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('google_two_factor_recovery_codes', function (Blueprint $table) {
            $table->id();

            $table->morphs('model');

            $table->string('code');
            $table->timestamp('used_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('google_two_factor_recovery_codes');
    }
};
