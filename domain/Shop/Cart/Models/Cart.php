<?php

declare(strict_types=1);

namespace Domain\Shop\Cart\Models;

use App\Casts\MoneyCast;
use App\Helpers;
use Domain\Shop\Branch\Models\Branch;
use Domain\Shop\Customer\Models\Customer;
use Domain\Shop\Product\Models\Product;
use Domain\Shop\Product\Models\Sku;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Domain\Shop\Cart\Models\Cart
 *
 * @property int $id
 * @property int $customer_id
 * @property int $branch_id
 * @property int $product_id
 * @property int $sku_id
 * @property string $sku_code
 * @property string $product_name
 * @property float $price for money
 * @property float $quantity
 * @property float|null $minimum
 * @property float|null $maximum
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Domain\Shop\Branch\Models\Branch $branch
 * @property-read \Domain\Shop\Customer\Models\Customer $customer
 * @property-read \Domain\Shop\Product\Models\Product $product
 * @property-read \Domain\Shop\Product\Models\Sku $sku
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Cart\Models\Cart newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Cart\Models\Cart newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Cart\Models\Cart query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Cart\Models\Cart whereBranchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Cart\Models\Cart whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Cart\Models\Cart whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Cart\Models\Cart whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Cart\Models\Cart whereMaximum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Cart\Models\Cart whereMinimum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Cart\Models\Cart wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Cart\Models\Cart whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Cart\Models\Cart whereProductName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Cart\Models\Cart whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Cart\Models\Cart whereSkuCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Cart\Models\Cart whereSkuId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Cart\Models\Cart whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Cart extends Model
{
    use LogsActivity;

    protected $fillable = [
        'customer_id',
        'branch_id',
        'product_id',
        'sku_id',
        'product_name',
        'sku_code',
        'price',
        'quantity',
        'minimum',
        'maximum',
    ];

    protected $casts = [
        'quantity' => 'float',
        'price' => MoneyCast::class,
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(Helpers::getCurrentAuthDriver())
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
