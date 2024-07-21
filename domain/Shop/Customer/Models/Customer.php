<?php

declare(strict_types=1);

namespace Domain\Shop\Customer\Models;

use App\Helpers;
use Domain\Access\Admin\Models\Admin;
use Domain\Shop\Cart\Models\Cart;
use Domain\Shop\Customer\Enums\Gender;
use Domain\Shop\Customer\Enums\Status;
use Domain\Shop\Order\Models\Order;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * Domain\Shop\Customer\Models\Customer
 *
 * @property int $id
 * @property int|null $admin_id
 * @property string $reference_number
 * @property string|null $email
 * @property mixed|null $password
 * @property string $first_name
 * @property string|null $last_name
 * @property string|null $mobile
 * @property \Domain\Shop\Customer\Enums\Gender|null $gender PHP backed enum
 * @property \Domain\Shop\Customer\Enums\Status $status PHP backed enum
 * @property string $timezone
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Domain\Shop\Customer\Models\Address> $addresses
 * @property-read int|null $addresses_count
 * @property-read \Domain\Access\Admin\Models\Admin|null $admin
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Domain\Shop\Cart\Models\Cart> $carts
 * @property-read int|null $carts_count
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Domain\Shop\Order\Models\Order> $orders
 * @property-read int|null $orders_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Customer\Models\Customer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Customer\Models\Customer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Customer\Models\Customer onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Customer\Models\Customer query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Customer\Models\Customer whereAdminId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Customer\Models\Customer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Customer\Models\Customer whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Customer\Models\Customer whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Customer\Models\Customer whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Customer\Models\Customer whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Customer\Models\Customer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Customer\Models\Customer whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Customer\Models\Customer whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Customer\Models\Customer wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Customer\Models\Customer whereReferenceNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Customer\Models\Customer whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Customer\Models\Customer whereTimezone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Customer\Models\Customer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Customer\Models\Customer withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Shop\Customer\Models\Customer withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Customer extends Authenticatable implements HasMedia
{
    use HasApiTokens;
    use InteractsWithMedia;
    use LogsActivity;
    use SoftDeletes;

    protected $fillable = [
        'admin_id',
        'email',
        'first_name',
        'last_name',
        'mobile',
        'password',
        'gender',
        'status',
        'timezone',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'password' => 'hashed',
        'gender' => Gender::class,
        'status' => Status::class,
    ];

    public function getRouteKeyName(): string
    {
        return 'reference_number';
    }

    /** @return Attribute<string, never> */
    protected function fullName(): Attribute
    {
        return Attribute::get(
            function (): string {
                if ($this->last_name === null) {
                    return $this->first_name;
                }

                return "{$this->first_name} {$this->last_name}";
            }
        );
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(Helpers::getCurrentAuthDriver())
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\Domain\Access\Admin\Models\Admin, \Domain\Shop\Customer\Models\Customer> */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
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

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')
            ->useFallbackUrl(asset('images/no-image.webp'))
            ->singleFile()
            ->registerMediaConversions(function () {
                $this->addMediaConversion('thumb')
                    ->fit(Manipulations::FIT_FILL, 40, 40);
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
