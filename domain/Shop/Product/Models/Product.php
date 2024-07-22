<?php

declare(strict_types=1);

namespace Domain\Shop\Product\Models;

use Domain\Shop\Brand\Models\Brand;
use Domain\Shop\Category\Models\Category;
use Domain\Shop\Product\Enums\Status;
use Domain\Shop\Product\Models\EloquentBuilder\ProductEloquentBuilder;
use Domain\Shop\Product\Observers\ProductObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Tags\HasTags;

/**
 * Domain\Shop\Product\Models\Product
 *
 * @property string $uuid
 * @property string|null $category_uuid
 * @property string|null $brand_uuid
 * @property string $parent_sku
 * @property string $name
 * @property string|null $description
 * @property \Domain\Shop\Product\Enums\Status $status PHP backed enum
 * @property int $order_column manage by spatie/eloquent-sortable
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Activitylog\Models\Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Domain\Shop\Product\Models\Attribute[] $attributes
 * @property-read int|null $attributes_count
 * @property-read \Domain\Shop\Brand\Models\Brand|null $brand
 * @property-read \Domain\Shop\Category\Models\Category|null $category
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection|\Spatie\MediaLibrary\MediaCollections\Models\Media[] $media
 * @property-read int|null $media_count
 * @property \Illuminate\Database\Eloquent\Collection|\Spatie\Tags\Tag[] $tags
 * @property-read \Illuminate\Database\Eloquent\Collection|\Domain\Shop\Product\Models\Sku[] $skus
 * @property-read int|null $skus_count
 * @property-read int|null $tags_count
 *
 * @method static \Domain\Shop\Product\Models\EloquentBuilder\ProductEloquentBuilder|\Domain\Shop\Product\Models\Product newModelQuery()
 * @method static \Domain\Shop\Product\Models\EloquentBuilder\ProductEloquentBuilder|\Domain\Shop\Product\Models\Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\Product onlyTrashed()
 * @method static \Domain\Shop\Product\Models\EloquentBuilder\ProductEloquentBuilder|\Domain\Shop\Product\Models\Product ordered(string $direction = 'asc')
 * @method static \Domain\Shop\Product\Models\EloquentBuilder\ProductEloquentBuilder|\Domain\Shop\Product\Models\Product query()
 * @method static \Domain\Shop\Product\Models\EloquentBuilder\ProductEloquentBuilder|\Domain\Shop\Product\Models\Product whereBaseOnStocksIsWarning()
 * @method static \Domain\Shop\Product\Models\EloquentBuilder\ProductEloquentBuilder|\Domain\Shop\Product\Models\Product whereBaseOnStocksNotZero()
 * @method static \Domain\Shop\Product\Models\EloquentBuilder\ProductEloquentBuilder|\Domain\Shop\Product\Models\Product withAllTags(\ArrayAccess|\Spatie\Tags\Tag|array|string $tags, ?string $type = null)
 * @method static \Domain\Shop\Product\Models\EloquentBuilder\ProductEloquentBuilder|\Domain\Shop\Product\Models\Product withAllTagsOfAnyType($tags)
 * @method static \Domain\Shop\Product\Models\EloquentBuilder\ProductEloquentBuilder|\Domain\Shop\Product\Models\Product withAnyTags(\ArrayAccess|\Spatie\Tags\Tag|array|string $tags, ?string $type = null)
 * @method static \Domain\Shop\Product\Models\EloquentBuilder\ProductEloquentBuilder|\Domain\Shop\Product\Models\Product withAnyTagsOfAnyType($tags)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\Product withTrashed()
 * @method static \Domain\Shop\Product\Models\EloquentBuilder\ProductEloquentBuilder|\Domain\Shop\Product\Models\Product withoutTags(\ArrayAccess|\Spatie\Tags\Tag|array|string $tags, ?string $type = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\Product withoutTrashed()
 *
 * @mixin \Eloquent
 */
#[ObservedBy(ProductObserver::class)]
class Product extends Model implements HasMedia, Sortable
{
    use HasTags;
    use HasUuids;
    use InteractsWithMedia;
    use LogsActivity;
    use SoftDeletes;
    use SortableTrait;

    protected $primaryKey = 'uuid';

    /** @var array<int, non-empty-string> */
    protected $fillable = [
        'category_uuid',
        'brand_uuid',
        'parent_sku',
        'name',
        'description',
        'status',
    ];

    #[\Override]
    protected function casts(): array
    {
        return [
            'status' => Status::class,
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
    public function newEloquentBuilder($query): ProductEloquentBuilder
    {
        return new ProductEloquentBuilder($query);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<\Domain\Shop\Product\Models\Sku> */
    public function skus(): HasMany
    {
        return $this->hasMany(Sku::class);
    }

    #[\Override]
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')
            ->useFallbackUrl(asset('images/no-image.webp'))
            ->registerMediaConversions(function () {
                $this->addMediaConversion('list')
                    ->fit(Fit::Fill, 240, 210);
                $this->addMediaConversion('thumb')
                    ->fit(Fit::Fill, 40, 40);
            });
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\Domain\Shop\Brand\Models\Brand, \Domain\Shop\Product\Models\Product> */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\Domain\Shop\Category\Models\Category, \Domain\Shop\Product\Models\Product> */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<\Domain\Shop\Product\Models\Attribute> */
    public function attributes(): HasMany
    {
        return $this->hasMany(Attribute::class);
    }
}
