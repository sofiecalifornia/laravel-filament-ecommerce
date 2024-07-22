<?php

declare(strict_types=1);

namespace Domain\Shop\Stock\Models;

use Domain\Shop\Branch\Models\Branch;
use Domain\Shop\Product\Models\Sku;
use Domain\Shop\Stock\Enums\StockType;
use Domain\Shop\Stock\Models\EloquentBuilder\SkuStockEloquentBuilder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Domain\Shop\Stock\Models\SkuStock
 *
 * @property string $uuid
 * @property string $branch_uuid
 * @property string $sku_uuid
 * @property \Domain\Shop\Stock\Enums\StockType $type PHP backed enum
 * @property float|null $count when base on stock
 * @property float|null $warning when base on stock
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Activitylog\Models\Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read \Domain\Shop\Branch\Models\Branch $branch
 * @property-read \Domain\Shop\Product\Models\Sku $sku
 *
 * @method static \Domain\Shop\Stock\Models\EloquentBuilder\SkuStockEloquentBuilder|\Domain\Shop\Stock\Models\SkuStock newModelQuery()
 * @method static \Domain\Shop\Stock\Models\EloquentBuilder\SkuStockEloquentBuilder|\Domain\Shop\Stock\Models\SkuStock newQuery()
 * @method static \Domain\Shop\Stock\Models\EloquentBuilder\SkuStockEloquentBuilder|\Domain\Shop\Stock\Models\SkuStock query()
 * @method static \Domain\Shop\Stock\Models\EloquentBuilder\SkuStockEloquentBuilder|\Domain\Shop\Stock\Models\SkuStock whereBaseOnStocksIsNotWarning()
 * @method static \Domain\Shop\Stock\Models\EloquentBuilder\SkuStockEloquentBuilder|\Domain\Shop\Stock\Models\SkuStock whereBaseOnStocksIsWarning()
 * @method static \Domain\Shop\Stock\Models\EloquentBuilder\SkuStockEloquentBuilder|\Domain\Shop\Stock\Models\SkuStock whereBaseOnStocksNotZero()
 *
 * @mixin \Eloquent
 */
class SkuStock extends Model
{
    use HasUuids;
    use LogsActivity;

    protected $primaryKey = 'uuid';

    protected $fillable = [
        'branch_uuid',
        'type',
        'count',
        'warning',
    ];

    #[\Override]
    protected function casts(): array
    {
        return [
            'count' => 'float',
            'warning' => 'float',
            'type' => StockType::class,
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    #[\Override]
    public function newEloquentBuilder($query): SkuStockEloquentBuilder
    {
        return new SkuStockEloquentBuilder($query);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\Domain\Shop\Branch\Models\Branch, \Domain\Shop\Stock\Models\SkuStock> */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\Domain\Shop\Product\Models\Sku, \Domain\Shop\Stock\Models\SkuStock> */
    public function sku(): BelongsTo
    {
        return $this->belongsTo(Sku::class);
    }

    public function isBaseOnStockWarning(): bool
    {
        return $this->count < $this->warning;
    }
}
