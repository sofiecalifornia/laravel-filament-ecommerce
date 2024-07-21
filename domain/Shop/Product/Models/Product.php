<?php

declare(strict_types=1);

namespace Domain\Shop\Product\Models;

use App\Helpers;
use Domain\Shop\Brand\Models\Brand;
use Domain\Shop\Category\Models\Category;
use Domain\Shop\Product\Enums\Status;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

/**
 * Domain\Shop\Product\Models\Product
 *
 * @property int $id
 * @property int|null $category_id
 * @property int|null $brand_id
 * @property string $parent_sku
 * @property string $name
 * @property string|null $description
 * @property \Domain\Shop\Product\Enums\Status $status PHP backed enum
 * @property string $slug
 * @property int $order_column manage by spatie/eloquent-sortable
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Domain\Shop\Brand\Models\Brand|null $brand
 * @property-read \Domain\Shop\Category\Models\Category|null $category
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Domain\Shop\Product\Models\Sku> $skus
 * @property-read int|null $skus_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\Product onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\Product ordered(string $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\Product query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\Product whereBrandId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\Product whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\Product whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\Product whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\Product whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\Product whereOrderColumn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\Product whereParentSku($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\Product whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\Product whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\Product whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\Product withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\Product withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Product extends Model implements HasMedia, Sortable
{
    use HasSlug;
    use InteractsWithMedia;
    use LogsActivity;
    use SoftDeletes;
    use SortableTrait;

    /** @var array<int, non-empty-string> */
    protected $fillable = [
        'category_id',
        'brand_id',
        'parent_sku',
        'name',
        'description',
        'status',
    ];

    protected $casts = [
        'status' => Status::class,
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(Helpers::getCurrentAuthDriver())
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<\Domain\Shop\Product\Models\Sku> */
    public function skus(): HasMany
    {
        return $this->hasMany(Sku::class);
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo($this->getRouteKeyName());
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')
            ->useFallbackUrl(asset('images/no-image.webp'))
            ->registerMediaConversions(function () {
                $this->addMediaConversion('list')
                    ->fit(Manipulations::FIT_FILL, 240, 210);
                $this->addMediaConversion('thumb')
                    ->fit(Manipulations::FIT_FILL, 40, 40);
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
}
