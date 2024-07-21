<?php

declare(strict_types=1);

use Domain\Shop\Brand\Models\Brand;
use Domain\Shop\Category\Models\Category;
use Domain\Shop\Product\Models\Attribute;
use Domain\Shop\Product\Models\AttributeOption;
use Domain\Shop\Product\Models\Product;
use Domain\Shop\Product\Models\Sku;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Category::class)
                ->nullable()
                ->constrained();

            $table->foreignIdFor(Brand::class)
                ->nullable()
                ->constrained();

            $table->string('parent_sku')->unique();
            $table->string('name')->unique();
            $table->longText('description')->nullable();
            $table->phpEnum('status');
            $table->string('slug')->unique();
            $table->eloquentSortable();

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('skus', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Product::class)->constrained();
            $table->string('code')->unique();
            $table->money('price');

            $table->unsignedFloat('minimum')->nullable();
            $table->unsignedFloat('maximum')->nullable();

            $table->string('slug')->unique();

            $table->eloquentSortable();
            $table->timestamps();
        });

        Schema::create('attributes', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('slug')->unique();

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('attribute_options', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Attribute::class)->constrained();
            $table->string('value');

            $table->string('slug')->unique();

            $table->eloquentSortable();
            $table->timestamps();
        });

        Schema::create('attribute_option_sku', function (Blueprint $table) {
            $table->foreignIdFor(Sku::class)->constrained(indexName: 'attr_opt_sku_sku_frgn');
            $table->foreignIdFor(AttributeOption::class)->constrained(indexName: 'attr_opt_sku_attr_frgn');

            $table->timestamps();

            $table->primary([
                (new Sku())->getForeignKey(), (new AttributeOption())->getForeignKey(),
            ], name: 'attr_opt_sku_primary');
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
