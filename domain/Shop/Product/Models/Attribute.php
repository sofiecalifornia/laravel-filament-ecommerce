<?php

declare(strict_types=1);

namespace Domain\Shop\Product\Models;

use Domain\Shop\Product\Enums\AttributeFieldType;
use Domain\Shop\Product\Observers\AttributeObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Domain\Shop\Product\Models\Attribute
 *
 * @property string $uuid
 * @property string $product_uuid
 * @property string $name
 * @property string|null $prefix
 * @property string|null $suffix
 * @property \Domain\Shop\Product\Enums\AttributeFieldType $type PHP backed enum
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Activitylog\Models\Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Domain\Shop\Product\Models\AttributeOption[] $attributeOptions
 * @property-read int|null $attribute_options_count
 * @property-read \Domain\Shop\Product\Models\Product $product
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\Attribute newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\Attribute newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\Attribute onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\Attribute query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\Attribute withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\Attribute withoutTrashed()
 *
 * @mixin \Eloquent
 */
#[ObservedBy(AttributeObserver::class)]
class Attribute extends Model
{
    use HasUuids;
    use LogsActivity;
    use SoftDeletes;

    protected $primaryKey = 'uuid';

    protected $fillable = [
        'name',
        'type',
        'prefix',
        'suffix',
    ];

    #[\Override]
    protected function casts(): array
    {
        return [
            'type' => AttributeFieldType::class,
        ];
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<\Domain\Shop\Product\Models\AttributeOption> */
    public function attributeOptions(): HasMany
    {
        return $this->hasMany(AttributeOption::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\Domain\Shop\Product\Models\Product, \Domain\Shop\Product\Models\Attribute> */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
