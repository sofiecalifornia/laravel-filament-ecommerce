<?php

declare(strict_types=1);

namespace Domain\Shop\Order\Models;

use App\Casts\MoneyCast;
use Domain\Shop\Product\Models\Sku;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Domain\Shop\Order\Models\OrderItem
 *
 * @property int $id
 * @property int $order_id
 * @property int $sku_id
 * @property string $sku_code
 * @property string $name
 * @property float $price for money
 * @property float $total_price for money
 * @property float $quantity customer actual quantity
 * @property float $paid_quantity
 * @property float|null $minimum
 * @property float|null $maximum
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Domain\Shop\Order\Models\Order $order
 * @property-read \Domain\Shop\Product\Models\Sku $sku
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Order\Models\OrderItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Order\Models\OrderItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Order\Models\OrderItem onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Order\Models\OrderItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Order\Models\OrderItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Order\Models\OrderItem whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Order\Models\OrderItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Order\Models\OrderItem whereMaximum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Order\Models\OrderItem whereMinimum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Order\Models\OrderItem whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Order\Models\OrderItem whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Order\Models\OrderItem wherePaidQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Order\Models\OrderItem wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Order\Models\OrderItem whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Order\Models\OrderItem whereSkuCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Order\Models\OrderItem whereSkuId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Order\Models\OrderItem whereTotalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Order\Models\OrderItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Order\Models\OrderItem withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Order\Models\OrderItem withoutTrashed()
 *
 * @mixin \Eloquent
 */
class OrderItem extends Model
{
    use SoftDeletes;

    /** @var array<int, non-empty-string> */
    protected $fillable = [
        'order_id',
        'sku_id',
        'quantity',
    ];

    protected $casts = [
        'price' => MoneyCast::class,
        'total_price' => MoneyCast::class,
    ];

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\Domain\Shop\Order\Models\Order, \Domain\Shop\Order\Models\OrderItem> */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\Domain\Shop\Product\Models\Sku, \Domain\Shop\Order\Models\OrderItem> */
    public function sku(): BelongsTo
    {
        return $this->belongsTo(Sku::class);
    }
}
