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
            $table->uuid()->primary()->unique();

            $table->foreignIdFor(Category::class)
                ->nullable()
                ->constrained(column: 'uuid');

            $table->foreignIdFor(Brand::class)
                ->nullable()
                ->constrained(column: 'uuid');

            $table->string('parent_sku')->unique();
            $table->string('name')->unique();
            $table->longText('description')->nullable();
            $table->phpEnum('status');
            $table->eloquentSortable();

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('skus', function (Blueprint $table) {
            $table->uuid()->primary()->unique();

            $table->foreignIdFor(Product::class)->constrained(column: 'uuid');

            $table->string('code')->unique();
            $table->money('price');

            $table->float('minimum')
                ->unsigned()
                ->nullable();
            $table->float('maximum')
                ->unsigned()
                ->nullable();

            $table->eloquentSortable();
            $table->timestamps();
        });

        Schema::create('attributes', function (Blueprint $table) {
            $table->uuid()->primary()->unique();

            $table->foreignIdFor(Product::class)->constrained(column: 'uuid');

            $table->string('name');
            $table->string('prefix')->nullable();
            $table->string('suffix')->nullable();
            $table->phpEnum('type');

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('attribute_options', function (Blueprint $table) {
            $table->uuid()->primary()->unique();

            $table->foreignIdFor(Attribute::class)->constrained(column: 'uuid');

            $table->string('value');

            $table->eloquentSortable();
            $table->timestamps();
        });

        Schema::create('attribute_option_sku', function (Blueprint $table) {
            $table->foreignIdFor(Sku::class)->constrained(column: 'uuid', indexName: 'attr_opt_sku_sku_frgn');
            $table->foreignIdFor(AttributeOption::class)->constrained(column: 'uuid', indexName: 'attr_opt_sku_attr_frgn');

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
