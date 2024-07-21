<?php

declare(strict_types=1);

namespace Domain\Shop\Branch\Models;

use App\Helpers;
use Domain\Access\Admin\Models\Admin;
use Domain\Shop\Branch\Enums\Status;
use Domain\Shop\Order\Models\Order;
use Domain\Shop\Stock\Models\SkuStock;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
 * Domain\Shop\Branch\Models\Branch
 *
 * @property int $id
 * @property string $name
 * @property \Domain\Shop\Branch\Enums\Status $status PHP backed enum
 * @property string $slug
 * @property int $order_column manage by spatie/eloquent-sortable
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Domain\Access\Admin\Models\Admin> $admins
 * @property-read int|null $admins_count
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Domain\Shop\Order\Models\Order> $orders
 * @property-read int|null $orders_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Domain\Shop\Stock\Models\SkuStock> $skuStocks
 * @property-read int|null $sku_stocks_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Branch\Models\Branch newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Branch\Models\Branch newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Branch\Models\Branch onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Branch\Models\Branch ordered(string $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Branch\Models\Branch query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Branch\Models\Branch whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Branch\Models\Branch whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Branch\Models\Branch whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Branch\Models\Branch whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Branch\Models\Branch whereOrderColumn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Branch\Models\Branch whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Branch\Models\Branch whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Branch\Models\Branch whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Branch\Models\Branch withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Branch\Models\Branch withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Branch extends Model implements HasAvatar, HasMedia, Sortable
{
    use HasSlug;
    use InteractsWithMedia;
    use LogsActivity;
    use SoftDeletes;
    use SortableTrait;

    protected $fillable = [
        'name',
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

        $this->addMediaCollection('panel')
            ->singleFile()
            ->useFallbackUrl(asset('images/no-image.webp'))
            ->registerMediaConversions(function () {
                $this->addMediaConversion('thumb')
                    ->fit(Manipulations::FIT_FILL, 40, 40);
            });
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<\Domain\Shop\Order\Models\Order> */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<\Domain\Shop\Stock\Models\SkuStock> */
    public function skuStocks(): HasMany
    {
        return $this->hasMany(SkuStock::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\Domain\Access\Admin\Models\Admin>
     */
    public function admins(): BelongsToMany
    {
        return $this->belongsToMany(Admin::class);
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->getFirstMediaUrl('panel', 'thumb');
    }
}
