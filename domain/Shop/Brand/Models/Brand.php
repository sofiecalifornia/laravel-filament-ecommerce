<?php

declare(strict_types=1);

namespace Domain\Shop\Brand\Models;

use Domain\Shop\Brand\Observers\BrandObserver;
use Domain\Shop\Product\Models\Product;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * Domain\Shop\Brand\Models\Brand
 *
 * @property string $uuid
 * @property string $name
 * @property int $order_column manage by spatie/eloquent-sortable
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Activitylog\Models\Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection|\Spatie\MediaLibrary\MediaCollections\Models\Media[] $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Domain\Shop\Product\Models\Product[] $products
 * @property-read int|null $products_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Brand\Models\Brand newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Brand\Models\Brand newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Brand\Models\Brand onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Brand\Models\Brand ordered(string $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Brand\Models\Brand query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Brand\Models\Brand withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Brand\Models\Brand withoutTrashed()
 *
 * @mixin \Eloquent
 */
#[ObservedBy(BrandObserver::class)]
class Brand extends Model implements HasMedia, Sortable
{
    use HasUuids;
    use InteractsWithMedia;
    use LogsActivity;
    use SoftDeletes;
    use SortableTrait;

    protected $primaryKey = 'uuid';

    protected $fillable = [
        'name',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
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

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<\Domain\Shop\Product\Models\Product> */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
