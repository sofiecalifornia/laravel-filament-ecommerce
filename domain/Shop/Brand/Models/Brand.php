<?php

declare(strict_types=1);

namespace Domain\Shop\Brand\Models;

use App\Helpers;
use Domain\Shop\Product\Models\Product;
use Illuminate\Database\Eloquent\Model;
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
 * Domain\Shop\Brand\Models\Brand
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property int $order_column manage by spatie/eloquent-sortable
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Domain\Shop\Product\Models\Product> $products
 * @property-read int|null $products_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Brand\Models\Brand newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Brand\Models\Brand newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Brand\Models\Brand onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Brand\Models\Brand ordered(string $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Brand\Models\Brand query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Brand\Models\Brand whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Brand\Models\Brand whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Brand\Models\Brand whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Brand\Models\Brand whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Brand\Models\Brand whereOrderColumn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Brand\Models\Brand whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Brand\Models\Brand whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Brand\Models\Brand withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Brand\Models\Brand withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Brand extends Model implements HasMedia, Sortable
{
    use HasSlug;
    use InteractsWithMedia;
    use LogsActivity;
    use SoftDeletes;
    use SortableTrait;

    protected $fillable = [
        'name',
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

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<\Domain\Shop\Product\Models\Product> */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
