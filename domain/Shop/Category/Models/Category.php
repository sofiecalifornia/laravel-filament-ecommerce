<?php

declare(strict_types=1);

namespace Domain\Shop\Category\Models;

use Domain\Shop\Category\Models\EloquentBuilder\CategoryEloquentBuilder;
use Domain\Shop\Category\Observers\CategoryObserver;
use Domain\Shop\Product\Models\Product;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
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

/**
 * Domain\Shop\Category\Models\Category
 *
 * @property string $uuid
 * @property string|null $parent_uuid
 * @property string $name
 * @property string|null $description
 * @property bool $is_visible
 * @property int $order_column manage by spatie/eloquent-sortable
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Activitylog\Models\Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Domain\Shop\Category\Models\Category[] $children
 * @property-read int|null $children_count
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection|\Spatie\MediaLibrary\MediaCollections\Models\Media[] $media
 * @property-read int|null $media_count
 * @property-read string $name_with_parent
 * @property-read \Domain\Shop\Category\Models\Category|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection|\Domain\Shop\Product\Models\Product[] $products
 * @property-read int|null $products_count
 *
 * @method static \Domain\Shop\Category\Models\EloquentBuilder\CategoryEloquentBuilder|\Domain\Shop\Category\Models\Category newModelQuery()
 * @method static \Domain\Shop\Category\Models\EloquentBuilder\CategoryEloquentBuilder|\Domain\Shop\Category\Models\Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Category\Models\Category onlyTrashed()
 * @method static \Domain\Shop\Category\Models\EloquentBuilder\CategoryEloquentBuilder|\Domain\Shop\Category\Models\Category ordered(string $direction = 'asc')
 * @method static \Domain\Shop\Category\Models\EloquentBuilder\CategoryEloquentBuilder|\Domain\Shop\Category\Models\Category query()
 * @method static \Domain\Shop\Category\Models\EloquentBuilder\CategoryEloquentBuilder|\Domain\Shop\Category\Models\Category whereChild()
 * @method static \Domain\Shop\Category\Models\EloquentBuilder\CategoryEloquentBuilder|\Domain\Shop\Category\Models\Category whereParent()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Category\Models\Category withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Category\Models\Category withoutTrashed()
 *
 * @mixin \Eloquent
 */
#[ObservedBy(CategoryObserver::class)]
class Category extends Model implements HasMedia, Sortable
{
    use HasUuids;
    use InteractsWithMedia;
    use LogsActivity;
    use SoftDeletes;
    use SortableTrait;

    protected $primaryKey = 'uuid';

    protected $fillable = [
        'parent_uuid',
        'name',
        'description',
        'is_visible',
    ];

    #[\Override]
    protected function casts(): array
    {
        return [
            'is_visible' => 'bool',
        ];
    }

    /** @return Attribute<string, never> */
    protected function nameWithParent(): Attribute
    {
        return Attribute::get(
            fn (): string => ($this->parent?->name ?? trans('unknown')).' > '.$this->name
        );
    }

    #[\Override]
    public function newEloquentBuilder($query): CategoryEloquentBuilder
    {
        return new CategoryEloquentBuilder($query);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<\Domain\Shop\Category\Models\Category> */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_uuid');
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\Domain\Shop\Category\Models\Category, \Domain\Shop\Category\Models\Category> */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_uuid');
    }

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
            ->singleFile()
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

    public function loadProductCountWithTrashed(): self
    {
        return $this->loadCount([
            'products' => function (Builder $builder) {
                /** @var \Domain\Shop\Product\Models\Product|\Illuminate\Database\Eloquent\Builder $builder */
                $builder->withTrashed();
            },
        ]);
    }
}
