<?php

declare(strict_types=1);

namespace Domain\Shop\Product\Models;

use App\Helpers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

/**
 * Domain\Shop\Product\Models\Attribute
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Domain\Shop\Product\Models\AttributeOption> $attributeOptions
 * @property-read int|null $attribute_options_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\Attribute newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\Attribute newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\Attribute onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\Attribute query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\Attribute whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\Attribute whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\Attribute whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\Attribute whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\Attribute whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\Attribute whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\Attribute withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\Attribute withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Attribute extends Model
{
    use HasSlug;
    use LogsActivity;
    use SoftDeletes;

    protected $fillable = [
        'name',
    ];

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<\Domain\Shop\Product\Models\AttributeOption> */
    public function attributeOptions(): HasMany
    {
        return $this->hasMany(AttributeOption::class);
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

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
