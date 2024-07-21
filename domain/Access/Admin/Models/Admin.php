<?php

declare(strict_types=1);

namespace Domain\Access\Admin\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Helpers;
use Creativeorange\Gravatar\Facades\Gravatar;
use Domain\Shop\Branch\Models\Branch;
use Domain\Shop\Order\Models\Order;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\ActivitylogServiceProvider;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

/**
 * Domain\Access\Admin\Models\Admin
 *
 * @property int $id
 * @property int|null $admin_id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property mixed|null $password
 * @property string $slug
 * @property string $timezone
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Domain\Access\Admin\Models\Admin|null $admin
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Domain\Shop\Branch\Models\Branch> $branches
 * @property-read int|null $branches_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $causerActivities
 * @property-read int|null $causer_activities_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Domain\Shop\Order\Models\Order> $orders
 * @property-read int|null $orders_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Domain\Access\Role\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Domain\Access\Role\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Access\Admin\Models\Admin newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Access\Admin\Models\Admin newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Access\Admin\Models\Admin onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Access\Admin\Models\Admin permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Access\Admin\Models\Admin query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Access\Admin\Models\Admin role($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Access\Admin\Models\Admin whereAdminId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Access\Admin\Models\Admin whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Access\Admin\Models\Admin whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Access\Admin\Models\Admin whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Access\Admin\Models\Admin whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Access\Admin\Models\Admin whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Access\Admin\Models\Admin whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Access\Admin\Models\Admin wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Access\Admin\Models\Admin whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Access\Admin\Models\Admin whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Access\Admin\Models\Admin whereTimezone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Access\Admin\Models\Admin whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Access\Admin\Models\Admin withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Access\Admin\Models\Admin withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Admin extends Authenticatable implements FilamentUser, HasAvatar, HasTenants
{
    use HasApiTokens;
    use HasRoles;
    use HasSlug;
    use LogsActivity;
    use Notifiable;
    use SoftDeletes;

    /** @var array<int, string> */
    protected $fillable = [
        'admin_id',
        'name',
        'email',
        'password',
        'timezone',
        'theme',
        'theme_color',
    ];

    /** @var array<int, string> */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'password' => 'hashed',
        'email_verified_at' => 'datetime',
        'time_in_required' => 'bool',
    ];

    protected function getDefaultGuardName(): string
    {
        // Forcing Use Of A Single Guard
        // https://spatie.be/docs/laravel-permission/basic-usage/multiple-guards#content-forcing-use-of-a-single-guard
        return config('auth.defaults.guard');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo($this->getRouteKeyName());
    }

    public function isZeroDayAdmin(): bool
    {
        return $this->id === 1;
    }

    public function canImpersonate(): bool
    {
        return $this->isAdminOrSuperAdmin();
    }

    public function canBeImpersonated(): bool
    {
        return ! $this->isSuperAdmin();
    }

    public function isAdminOrSuperAdmin(): bool
    {
        return $this->hasAnyRole(config('domain.access.role.super_admin'), config('domain.access.role.admin'));
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole(config('domain.access.role.super_admin'));
    }

    public function isBranch(): bool
    {
        return $this->hasRole(config('domain.access.role.branch'));
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(Helpers::getCurrentAuthDriver())
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<\Domain\Shop\Order\Models\Order> */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\Domain\Access\Admin\Models\Admin, \Domain\Access\Admin\Models\Admin> */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(self::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<\Spatie\Activitylog\Models\Activity>
     *
     * @throws \Spatie\Activitylog\Exceptions\InvalidConfiguration
     */
    public function causerActivities(): MorphMany
    {
        return $this->morphMany(ActivitylogServiceProvider::determineActivityModel(), 'causer');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\Domain\Shop\Branch\Models\Branch>
     */
    public function branches(): BelongsToMany
    {
        return $this->belongsToMany(Branch::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    /**
     * @param  \Domain\Shop\Branch\Models\Branch  $tenant
     */
    public function canAccessTenant(Model $tenant): bool
    {
        return $this->branches->contains($tenant);
    }

    /**
     * @return array<int, \Domain\Shop\Branch\Models\Branch>|\Illuminate\Support\Collection<int, \Domain\Shop\Branch\Models\Branch>
     */
    public function getTenants(Panel $panel): array|Collection
    {
        return $this->branches;
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return Gravatar::get(email: $this->email);
    }
}
