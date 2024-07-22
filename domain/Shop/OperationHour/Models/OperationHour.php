<?php

declare(strict_types=1);

namespace Domain\Shop\OperationHour\Models;

use Domain\Shop\Branch\Models\Branch;
use Domain\Shop\OperationHour\Enums\Day;
use Domain\Shop\OperationHour\Enums\Type;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

/**
 * Domain\Shop\OperationHour\Models\OperationHour
 *
 * @property string $uuid
 * @property string $branch_uuid
 * @property \Domain\Shop\OperationHour\Enums\Day $day PHP backed enum
 * @property \Domain\Shop\OperationHour\Enums\Type $type PHP backed enum
 * @property bool $is_all_day
 * @property bool $is_open
 * @property int $order_column manage by spatie/eloquent-sortable
 * @property \Illuminate\Support\Carbon $from
 * @property \Illuminate\Support\Carbon $to
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Activitylog\Models\Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read \Domain\Shop\Branch\Models\Branch $branch
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\OperationHour\Models\OperationHour newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\OperationHour\Models\OperationHour newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\OperationHour\Models\OperationHour ordered(string $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\OperationHour\Models\OperationHour query()
 *
 * @mixin \Eloquent
 */
class OperationHour extends Model implements Sortable
{
    use HasUuids;
    use LogsActivity;
    use SortableTrait;

    protected $primaryKey = 'uuid';

    protected $fillable = [
        'day',
        'from',
        'to',
        'is_all_day',
        'is_open',
        'type',
        'order_column',
    ];

    #[\Override]
    protected function casts(): array
    {
        return [
            'from' => 'datetime',
            'to' => 'datetime',
            'day' => Day::class,
            'type' => Type::class,
            'is_all_day' => 'bool',
            'is_open' => 'bool',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * @return BelongsTo<\Domain\Shop\Branch\Models\Branch, \Domain\Shop\OperationHour\Models\OperationHour>
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
