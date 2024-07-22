<?php

declare(strict_types=1);

namespace Domain\Shop\Product\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

/**
 * Domain\Shop\Product\Models\AttributeOption
 *
 * @property string $uuid
 * @property string $attribute_uuid
 * @property string $value
 * @property int $order_column manage by spatie/eloquent-sortable
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Domain\Shop\Product\Models\Attribute $attribute
 * @property-read string $label
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\AttributeOption newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\AttributeOption newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\AttributeOption ordered(string $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Product\Models\AttributeOption query()
 *
 * @mixin \Eloquent
 */
class AttributeOption extends Model implements Sortable
{
    use HasUuids;
    use SortableTrait;

    protected $primaryKey = 'uuid';

    protected $fillable = [
        'attribute_uuid',
        'value',
        'order_column',
    ];

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\Domain\Shop\Product\Models\Attribute, \Domain\Shop\Product\Models\AttributeOption> */
    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class);
    }

    /** @return \Illuminate\Database\Eloquent\Casts\Attribute<string, never> */
    protected function label(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::get(
            fn (): string => trans(':name: :prefix:value:suffix', [
                'name' => $this->attribute->name,
                'prefix' => $this->attribute->prefix ?? '',
                'value' => $this->value,
                'suffix' => $this->attribute->suffix ?? '',
            ])
        );
    }
}
