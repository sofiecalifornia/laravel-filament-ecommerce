<?php

declare(strict_types=1);

namespace Domain\Shop\Cart\Models;

use App\Casts\MoneyCast;
use Domain\Shop\Branch\Models\Branch;
use Domain\Shop\Customer\Models\Customer;
use Domain\Shop\Product\Models\Product;
use Domain\Shop\Product\Models\Sku;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Domain\Shop\Cart\Models\Cart
 *
 * @property string $uuid
 * @property string $customer_uuid
 * @property string $branch_uuid
 * @property string $product_uuid
 * @property string $sku_uuid
 * @property string $sku_code
 * @property string $product_name
 * @property \Akaunting\Money\Money $price for money
 * @property float $quantity
 * @property float|null $minimum
 * @property float|null $maximum
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Activitylog\Models\Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read \Domain\Shop\Branch\Models\Branch $branch
 * @property-read \Domain\Shop\Customer\Models\Customer $customer
 * @property-read \Domain\Shop\Product\Models\Product $product
 * @property-read \Domain\Shop\Product\Models\Sku $sku
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Cart\Models\Cart newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Cart\Models\Cart newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Cart\Models\Cart query()
 *
 * @mixin \Eloquent
 */
class Cart extends Model
{
    use HasUuids;
    use LogsActivity;

    protected $primaryKey = 'uuid';

    protected $fillable = [
        'customer_uuid',
        'branch_uuid',
        'product_uuid',
        'sku_uuid',
        'product_name',
        'sku_code',
        'price',
        'quantity',
        'minimum',
        'maximum',
    ];

    #[\Override]
    protected function casts(): array
    {
        return [
            'quantity' => 'float',
            'price' => MoneyCast::class,
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\Domain\Shop\Customer\Models\Customer, \Domain\Shop\Cart\Models\Cart> */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\Domain\Shop\Product\Models\Sku, \Domain\Shop\Cart\Models\Cart> */
    public function sku(): BelongsTo
    {
        return $this->belongsTo(Sku::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\Domain\Shop\Branch\Models\Branch, \Domain\Shop\Cart\Models\Cart> */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\Domain\Shop\Product\Models\Product, \Domain\Shop\Cart\Models\Cart> */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
