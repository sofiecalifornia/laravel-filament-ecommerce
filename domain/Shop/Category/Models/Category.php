<?php

declare(strict_types=1);

namespace Domain\Shop\Category\Models;

use App\Helpers;
use Domain\Shop\Category\Models\Query\CategoryQueryBuilder;
use Domain\Shop\Product\Models\Product;
use Illuminate\Database\Eloquent\Casts\Attribute;
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
 * Domain\Shop\Category\Models\Category
 *
 * @property int $id
 * @property int|null $parent_id
 * @property string $name
 * @property string|null $description
 * @property bool $is_visible
 * @property string $slug
 * @property int $order_column manage by spatie/eloquent-sortable
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Domain\Shop\Category\Models\Category> $children
 * @property-read int|null $children_count
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \Domain\Shop\Category\Models\Category|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Domain\Shop\Product\Models\Product> $products
 * @property-read int|null $products_count
 *
 * @method static \Domain\Shop\Category\Models\Query\CategoryQueryBuilder|\Domain\Shop\Category\Models\Category newModelQuery()
 * @method static \Domain\Shop\Category\Models\Query\CategoryQueryBuilder|\Domain\Shop\Category\Models\Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Category\Models\Category onlyTrashed()
 * @method static \Domain\Shop\Category\Models\Query\CategoryQueryBuilder|\Domain\Shop\Category\Models\Category ordered(string $direction = 'asc')
 * @method static \Domain\Shop\Category\Models\Query\CategoryQueryBuilder|\Domain\Shop\Category\Models\Category query()
 * @method static \Domain\Shop\Category\Models\Query\CategoryQueryBuilder|\Domain\Shop\Category\Models\Category whereChild()
 * @method static \Domain\Shop\Category\Models\Query\CategoryQueryBuilder|\Domain\Shop\Category\Models\Category whereCreatedAt($value)
 * @method static \Domain\Shop\Category\Models\Query\CategoryQueryBuilder|\Domain\Shop\Category\Models\Category whereDeletedAt($value)
 * @method static \Domain\Shop\Category\Models\Query\CategoryQueryBuilder|\Domain\Shop\Category\Models\Category whereDescription($value)
 * @method static \Domain\Shop\Category\Models\Query\CategoryQueryBuilder|\Domain\Shop\Category\Models\Category whereId($value)
 * @method static \Domain\Shop\Category\Models\Query\CategoryQueryBuilder|\Domain\Shop\Category\Models\Category whereIsVisible($value)
 * @method static \Domain\Shop\Category\Models\Query\CategoryQueryBuilder|\Domain\Shop\Category\Models\Category whereName($value)
 * @method static \Domain\Shop\Category\Models\Query\CategoryQueryBuilder|\Domain\Shop\Category\Models\Category whereOrderColumn($value)
 * @method static \Domain\Shop\Category\Models\Query\CategoryQueryBuilder|\Domain\Shop\Category\Models\Category whereParent()
 * @method static \Domain\Shop\Category\Models\Query\CategoryQueryBuilder|\Domain\Shop\Category\Models\Category whereParentId($value)
 * @method static \Domain\Shop\Category\Models\Query\CategoryQueryBuilder|\Domain\Shop\Category\Models\Category whereSlug($value)
 * @method static \Domain\Shop\Category\Models\Query\CategoryQueryBuilder|\Domain\Shop\Category\Models\Category whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Category\Models\Category withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Category\Models\Category withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Category extends Model implements HasMedia, Sortable
{
    use HasSlug;
    use InteractsWithMedia;
    use LogsActivity;
    use SoftDeletes;
    use SortableTrait;

    protected $fillable = [
        'parent_id',
        'name',
        'description',
        'is_visible',
    ];

    protected $casts = [
        'is_visible' => 'bool',
    ];

    /** @return Attribute<string, never> */
    protected function nameWithParent(): Attribute
    {
        return Attribute::get(
            fn (): string => ($this->parent?->name ?? trans('unknown')).' > '.$this->name
        );
    }

    public function newEloquentBuilder($query): CategoryQueryBuilder
    {
        return new CategoryQueryBuilder($query);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<\Domain\Shop\Category\Models\Category> */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\Domain\Shop\Category\Models\Category, \Domain\Shop\Category\Models\Category> */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
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
            ->singleFile()
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
