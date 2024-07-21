<?php

declare(strict_types=1);

namespace Domain\Shop\Product\Models;

use App\Casts\MoneyCast;
use App\Helpers;
use Domain\Shop\Cart\Models\Cart;
use Domain\Shop\Stock\Models\SkuStock;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
 * Domain\Shop\Product\Models\Sku
 *
 * @property int $id
 * @property int $product_id
 * @property string $code
 * @property float $price for money
 * @property float|null $minimum
 * @property float|null $maximum
 * @property string $slug
 * @property int $order_column manage by spatie/eloquent-sortable
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Domain\Shop\Product\Models\AttributeOption> $attributeOptions
 * @property-read int|null $attribute_options_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Domain\Shop\Cart\Models\Cart> $carts
 * @property-read int|null $carts_count
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \Domain\Shop\Product\Models\Product $product
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Domain\Shop\Stock\Models\SkuStock> $skuStocks
 * @property-read int|null $sku_stocks_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\Sku newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\Sku newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\Sku ordered(string $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\Sku query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\Sku whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\Sku whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\Sku whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\Sku whereMaximum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\Sku whereMinimum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\Sku whereOrderColumn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\Sku wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\Sku whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\Sku whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\Sku whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Sku extends Model implements HasMedia, Sortable
{
    use HasSlug;
    use InteractsWithMedia;
    use LogsActivity;
    use SortableTrait;

    protected $fillable = [
        'product_id',
        'code',
        'price',
        'minimum',
        'maximum',
        'order_column',
    ];

    protected $casts = [
        'price' => MoneyCast::class,
    ];

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\Domain\Shop\Product\Models\Product, \Domain\Shop\Product\Models\Sku> */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\Domain\Shop\Product\Models\AttributeOption> */
    public function attributeOptions(): BelongsToMany
    {
        return $this->belongsToMany(AttributeOption::class);
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

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(Helpers::getCurrentAuthDriver())
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(['product.name', 'code'])
            ->saveSlugsTo($this->getRouteKeyName());
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<\Domain\Shop\Stock\Models\SkuStock> */
    public function skuStocks(): HasMany
    {
        return $this->hasMany(SkuStock::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<\Domain\Shop\Cart\Models\Cart> */
    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }
}
