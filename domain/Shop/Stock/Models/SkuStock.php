<?php

declare(strict_types=1);

namespace Domain\Shop\Stock\Models;

use App\Helpers;
use Domain\Shop\Branch\Models\Branch;
use Domain\Shop\Product\Models\Sku;
use Domain\Shop\Stock\Enums\StockType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Domain\Shop\Stock\Models\SkuStock
 *
 * @property int $id
 * @property int $branch_id
 * @property int $sku_id
 * @property \Domain\Shop\Stock\Enums\StockType $type PHP backed enum
 * @property float|null $count when base on stock
 * @property float|null $warning when base on stock
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Domain\Shop\Branch\Models\Branch $branch
 * @property-read \Domain\Shop\Product\Models\Sku $sku
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Stock\Models\SkuStock newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Stock\Models\SkuStock newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Stock\Models\SkuStock query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Stock\Models\SkuStock whereBranchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Stock\Models\SkuStock whereCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Stock\Models\SkuStock whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Stock\Models\SkuStock whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Stock\Models\SkuStock whereSkuId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Stock\Models\SkuStock whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Stock\Models\SkuStock whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Stock\Models\SkuStock whereWarning($value)
 *
 * @mixin \Eloquent
 */
class SkuStock extends Model
{
    use LogsActivity;

    protected $fillable = [
        'branch_id',
        'sku_id',
        'type',
        'count',
        'warning',
    ];

    protected $casts = [
        'count' => 'float',
        'warning' => 'float',
        'type' => StockType::class,
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(Helpers::getCurrentAuthDriver())
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
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
}
