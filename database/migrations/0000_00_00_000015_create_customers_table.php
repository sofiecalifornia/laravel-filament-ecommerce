<?php

declare(strict_types=1);

use Domain\Shop\Customer\Enums\Status;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->uuid()->primary()->unique();

            $table->string('email')->unique()->nullable();
            $table->string('password')->nullable();

            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('mobile')->nullable();
            $table->string('landline')->nullable();
            $table->phpEnum('gender')->nullable();
            $table->phpEnum('status')->default(Status::active->value);

            $table->string('timezone')->default(config('app-default.timezone'));

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
