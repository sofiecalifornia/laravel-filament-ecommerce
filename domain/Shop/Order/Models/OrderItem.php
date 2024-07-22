<?php

declare(strict_types=1);

namespace Domain\Shop\Order\Models;

use App\Casts\MoneyCast;
use Domain\Shop\Order\Observers\OrderItemObserver;
use Domain\Shop\Product\Models\Sku;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Domain\Shop\Order\Models\OrderItem
 *
 * @property string $uuid
 * @property string $order_uuid
 * @property string $sku_uuid
 * @property string $sku_code
 * @property string $name
 * @property string|null $description
 * @property \Akaunting\Money\Money $price for money
 * @property \Akaunting\Money\Money $discount_price for money
 * @property \Akaunting\Money\Money $total_price for money
 * @property float $quantity customer actual quantity
 * @property float $paid_quantity
 * @property float|null $minimum
 * @property float|null $maximum
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Activitylog\Models\Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read \Domain\Shop\Order\Models\Order $order
 * @property-read \Domain\Shop\Product\Models\Sku $sku
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Order\Models\OrderItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Order\Models\OrderItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Order\Models\OrderItem onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Order\Models\OrderItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Order\Models\OrderItem withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Order\Models\OrderItem withoutTrashed()
 *
 * @mixin \Eloquent
 */
#[ObservedBy(OrderItemObserver::class)]
class OrderItem extends Model
{
    use HasUuids;
    use LogsActivity;
    use SoftDeletes;

    protected $primaryKey = 'uuid';

    /** @var array<int, non-empty-string> */
    protected $fillable = [
        'sku_uuid',
        'quantity',
        'description',
    ];

    #[\Override]
    protected function casts(): array
    {
        return [
            'price' => MoneyCast::class,
            'discount_price' => MoneyCast::class,
            'total_price' => MoneyCast::class,
            'quantity' => 'float',
            'paid_quantity' => 'float',
            'minimum' => 'float',
            'maximum' => 'float',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

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
