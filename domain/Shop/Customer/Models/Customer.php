<?php

declare(strict_types=1);

namespace Domain\Shop\Customer\Models;

use Domain\Shop\Cart\Models\Cart;
use Domain\Shop\Customer\Enums\Gender;
use Domain\Shop\Customer\Enums\Status;
use Domain\Shop\Customer\Observers\CustomerObserver;
use Domain\Shop\Order\Models\Order;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * Domain\Shop\Customer\Models\Customer
 *
 * @property string $uuid
 * @property string|null $email
 * @property mixed|null $password
 * @property string $first_name
 * @property string|null $last_name
 * @property string|null $mobile
 * @property string|null $landline
 * @property \Domain\Shop\Customer\Enums\Gender|null $gender PHP backed enum
 * @property \Domain\Shop\Customer\Enums\Status $status PHP backed enum
 * @property string $timezone
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Activitylog\Models\Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Domain\Shop\Customer\Models\Address[] $addresses
 * @property-read int|null $addresses_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Domain\Shop\Cart\Models\Cart[] $carts
 * @property-read int|null $carts_count
 * @property-read string $full_name
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection|\Spatie\MediaLibrary\MediaCollections\Models\Media[] $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Domain\Shop\Order\Models\Order[] $orders
 * @property-read int|null $orders_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Sanctum\PersonalAccessToken[] $tokens
 * @property-read int|null $tokens_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Customer\Models\Customer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Customer\Models\Customer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Customer\Models\Customer onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Customer\Models\Customer query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Customer\Models\Customer withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Customer\Models\Customer withoutTrashed()
 *
 * @mixin \Eloquent
 */
#[ObservedBy(CustomerObserver::class)]
class Customer extends Authenticatable implements HasMedia
{
    use HasApiTokens;
    use HasUuids;
    use InteractsWithMedia;
    use LogsActivity;
    use Notifiable;
    use SoftDeletes;

    protected $primaryKey = 'uuid';

    protected $fillable = [
        'email',
        'first_name',
        'last_name',
        'mobile',
        'landline',
        'password',
        'gender',
        'status',
        'timezone',
    ];

    protected $hidden = [
        'password',
    ];

    #[\Override]
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'gender' => Gender::class,
            'status' => Status::class,
        ];
    }

    /** @return Attribute<string, never> */
    protected function fullName(): Attribute
    {
        return Attribute::get(
            function (): string {
                if (null === $this->last_name) {
                    return $this->first_name;
                }

                return "{$this->first_name} {$this->last_name}";
            }
        );
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<\Domain\Shop\Order\Models\Order> */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\MorphMany<\Domain\Shop\Customer\Models\Address> */
    public function addresses(): MorphMany
    {
        return $this->morphMany(Address::class, 'model');
    }

    #[\Override]
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')
            ->useFallbackUrl(asset('images/no-image.webp'))
            ->singleFile()
            ->registerMediaConversions(function () {
                $this->addMediaConversion('thumb')
                    ->fit(Fit::Fill, 40, 40);
            });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\Domain\Shop\Cart\Models\Cart>
     */
    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }
}
