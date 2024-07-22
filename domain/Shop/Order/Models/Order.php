<?php

declare(strict_types=1);

namespace Domain\Shop\Order\Models;

use App\Casts\MoneyCast;
use Domain\Shop\Branch\Models\Branch;
use Domain\Shop\Customer\Models\Customer;
use Domain\Shop\Order\Enums\ClaimType;
use Domain\Shop\Order\Enums\PaymentMethod;
use Domain\Shop\Order\Enums\PaymentStatus;
use Domain\Shop\Order\Enums\Status;
use Domain\Shop\Order\Observers\OrderObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * Domain\Shop\Order\Models\Order
 *
 * @property string $uuid
 * @property string $branch_uuid
 * @property string $customer_uuid
 * @property string $receipt_number
 * @property \Akaunting\Money\Money $delivery_price for money
 * @property \Akaunting\Money\Money $total_price for money
 * @property string|null $notes
 * @property \Domain\Shop\Order\Enums\PaymentMethod|null $payment_method PHP backed enum
 * @property \Domain\Shop\Order\Enums\PaymentStatus $payment_status PHP backed enum
 * @property \Domain\Shop\Order\Enums\Status $status PHP backed enum
 * @property \Domain\Shop\Order\Enums\ClaimType $claim_type PHP backed enum
 * @property \Illuminate\Support\Carbon|null $claim_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Activitylog\Models\Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read \Domain\Shop\Branch\Models\Branch $branch
 * @property-read \Domain\Shop\Customer\Models\Customer $customer
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection|\Spatie\MediaLibrary\MediaCollections\Models\Media[] $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Domain\Shop\Order\Models\OrderInvoice[] $orderInvoices
 * @property-read int|null $order_invoices_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Domain\Shop\Order\Models\OrderItem[] $orderItems
 * @property-read int|null $order_items_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Order\Models\Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Order\Models\Order newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Order\Models\Order onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Order\Models\Order query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Order\Models\Order withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Order\Models\Order withoutTrashed()
 *
 * @mixin \Eloquent
 */
#[ObservedBy(OrderObserver::class)]
class Order extends Model implements HasMedia
{
    use HasUuids;
    use InteractsWithMedia;
    use LogsActivity;
    use SoftDeletes;

    protected $primaryKey = 'uuid';

    /** @var array<int, non-empty-string> */
    protected $fillable = [
        'branch_uuid',
        'customer_uuid',
        'receipt_number',
        'notes',
        'delivery_price',
        'total_price',
        'payment_method',
        'payment_status',
        'status',
        'claim_type',
        'claim_at',
    ];

    #[\Override]
    protected function casts(): array
    {
        return [
            'payment_method' => PaymentMethod::class,
            'payment_status' => PaymentStatus::class,
            'status' => Status::class,
            'delivery_price' => MoneyCast::class,
            'total_price' => MoneyCast::class,
            'claim_type' => ClaimType::class,
            'claim_at' => 'datetime',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<\Domain\Shop\Order\Models\OrderItem> */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\Domain\Shop\Customer\Models\Customer, \Domain\Shop\Order\Models\Order> */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\Domain\Shop\Branch\Models\Branch, \Domain\Shop\Order\Models\Order> */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<\Domain\Shop\Order\Models\OrderInvoice> */
    public function orderInvoices(): HasMany
    {
        return $this->hasMany(OrderInvoice::class)->latest();
    }
    //    public function registerMediaCollections(): void
    //    {
    //        $this->addMediaCollection('invoice')
    //            ->acceptsFile(fn () => ['application/pdf'])
    //            ->singleFile();
    //    }

}
