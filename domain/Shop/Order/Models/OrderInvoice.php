<?php

declare(strict_types=1);

namespace Domain\Shop\Order\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Domain\Shop\Order\Models\OrderInvoice
 *
 * @property string $uuid
 * @property string $order_uuid
 * @property string $file_name
 * @property string $disk
 * @property string $path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Activitylog\Models\Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read \Domain\Shop\Order\Models\Order $order
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Order\Models\OrderInvoice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Order\Models\OrderInvoice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Order\Models\OrderInvoice query()
 *
 * @mixin \Eloquent
 */
class OrderInvoice extends Model
{
    use HasUuids;
    use LogsActivity;

    protected $primaryKey = 'uuid';

    protected $fillable = [
        'file_name',
        'disk',
        'path',
    ];

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\Domain\Shop\Order\Models\Order, \Domain\Shop\Order\Models\OrderInvoice> */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function download(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        return Storage::disk($this->disk)
            ->download($this->path, $this->file_name);
    }

    public function readStream(): mixed
    {
        return Storage::disk($this->disk)
            ->readStream($this->path);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
