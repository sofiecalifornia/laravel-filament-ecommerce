<?php

declare(strict_types=1);

namespace Domain\Shop\Product\Models;

use App\Casts\MoneyCast;
use Domain\Shop\Cart\Models\Cart;
use Domain\Shop\Product\Observers\SkuObserver;
use Domain\Shop\Stock\Models\SkuStock;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * Domain\Shop\Product\Models\Sku
 *
 * @property string $uuid
 * @property string $product_uuid
 * @property string $code
 * @property \Akaunting\Money\Money $price for money
 * @property float|null $minimum
 * @property float|null $maximum
 * @property int $order_column manage by spatie/eloquent-sortable
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Activitylog\Models\Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Domain\Shop\Product\Models\AttributeOption[] $attributeOptions
 * @property-read int|null $attribute_options_count
 * @property-read array $attribute_options_list
 * @property-read \Illuminate\Database\Eloquent\Collection|\Domain\Shop\Cart\Models\Cart[] $carts
 * @property-read int|null $carts_count
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection|\Spatie\MediaLibrary\MediaCollections\Models\Media[] $media
 * @property-read int|null $media_count
 * @property-read \Domain\Shop\Product\Models\Product $product
 * @property-read \Illuminate\Database\Eloquent\Collection|\Domain\Shop\Stock\Models\SkuStock[] $skuStocks
 * @property-read int|null $sku_stocks_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\Sku newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\Sku newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\Sku ordered(string $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\Sku query()
 *
 * @mixin \Eloquent
 */
#[ObservedBy(SkuObserver::class)]
class Sku extends Model implements HasMedia, Sortable
{
    use HasUuids;
    use InteractsWithMedia;
    use LogsActivity;
    use SortableTrait;

    protected $primaryKey = 'uuid';

    protected $fillable = [
        'code',
        'price',
        'minimum',
        'maximum',
        'order_column',
    ];

    #[\Override]
    protected function casts(): array
    {
        return [
            'price' => MoneyCast::class,
        ];
    }

    /** @return Attribute<array, never> */
    protected function attributeOptionsList(): Attribute
    {
        return Attribute::get(
            fn (): array => $this->attributeOptions
                ->map(
                    fn (AttributeOption $attributeOption) => $attributeOption->label
                )
                ->toArray()
        );
    }

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

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
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
