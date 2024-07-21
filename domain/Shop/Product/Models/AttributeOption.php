<?php

declare(strict_types=1);

namespace Domain\Shop\Product\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

/**
 * Domain\Shop\Product\Models\AttributeOption
 *
 * @property int $id
 * @property int $attribute_id
 * @property string $value
 * @property string $slug
 * @property int $order_column manage by spatie/eloquent-sortable
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Domain\Shop\Product\Models\Attribute $attribute
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\AttributeOption newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\AttributeOption newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\AttributeOption ordered(string $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\AttributeOption query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\AttributeOption whereAttributeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\AttributeOption whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\AttributeOption whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\AttributeOption whereOrderColumn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\AttributeOption whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\AttributeOption whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\AttributeOption whereValue($value)
 *
 * @mixin \Eloquent
 */
class AttributeOption extends Model implements Sortable
{
    use HasSlug;
    use SortableTrait;

    protected $fillable = [
        'attribute_id',
        'value',
        'order_column',
    ];

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\Domain\Shop\Product\Models\Attribute, \Domain\Shop\Product\Models\AttributeOption> */
    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class);
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(['attribute.name', 'value'])
            ->saveSlugsTo($this->getRouteKeyName());
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
