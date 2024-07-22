<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->after('timezone', function (Blueprint $table) {
                $table->string('theme')->nullable()->default('default');
                $table->string('theme_color')->nullable();

            });
        });
    }

    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn(['theme', 'theme_color']);
        });
    }
};
