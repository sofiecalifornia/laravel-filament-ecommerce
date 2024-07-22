<?php

declare(strict_types=1);

namespace Domain\Access\Admin\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Creativeorange\Gravatar\Facades\Gravatar;
use Domain\Access\Admin\Observers\AdminObserver;
use Domain\Access\Role\Support;
use Domain\Shop\Branch\Models\Branch;
use Domain\Shop\Models\Pivot\AdminBranchOrderNotificationsPivot;
use Domain\Shop\Order\Models\Order;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\CausesActivity;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;
use Support\Google2FA\Models\GoogleTwoFactorRecoveryCode;

/**
 * Domain\Access\Admin\Models\Admin
 *
 * @property string $uuid
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property mixed|null $password
 * @property string $timezone
 * @property string|null $theme
 * @property string|null $theme_color
 * @property string|null $remember_token
 * @property mixed|null $google2fa_secret
 * @property int|null $google2fa_timestamp
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Activitylog\Models\Activity[] $actions
 * @property-read int|null $actions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Activitylog\Models\Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Domain\Shop\Branch\Models\Branch[] $branchNotifications
 * @property-read int|null $branch_notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Domain\Shop\Branch\Models\Branch[] $branches
 * @property-read int|null $branches_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Support\Google2FA\Models\GoogleTwoFactorRecoveryCode[] $googleTwoFactorRecoveryCodes
 * @property-read int|null $google_two_factor_recovery_codes_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Domain\Shop\Order\Models\Order[] $orders
 * @property-read int|null $orders_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Domain\Access\Role\Models\Permission[] $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Domain\Access\Role\Models\Role[] $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Sanctum\PersonalAccessToken[] $tokens
 * @property-read int|null $tokens_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Access\Admin\Models\Admin newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Access\Admin\Models\Admin newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Access\Admin\Models\Admin onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Access\Admin\Models\Admin permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Access\Admin\Models\Admin query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Access\Admin\Models\Admin role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Access\Admin\Models\Admin withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Access\Admin\Models\Admin withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Access\Admin\Models\Admin withoutRole($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\Domain\Access\Admin\Models\Admin withoutTrashed()
 *
 * @mixin \Eloquent
 */
#[ObservedBy(AdminObserver::class)]
class Admin extends Authenticatable implements FilamentUser, HasAvatar, HasTenants, MustVerifyEmail
{
    use CausesActivity;
    use HasApiTokens;
    use HasRoles;
    use HasUuids;
    use LogsActivity;
    use Notifiable;
    use SoftDeletes;

    protected $primaryKey = 'uuid';

    /** @var array<int, string> */
    protected $fillable = [
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
        'google2fa_secret',
        'google2fa_timestamp',
    ];

    #[\Override]
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'email_verified_at' => 'datetime',
            'google2fa_secret' => 'encrypted',
        ];
    }

    protected function getDefaultGuardName(): string
    {
        // Forcing Use Of A Single Guard
        // https://spatie.be/docs/laravel-permission/basic-usage/multiple-guards#content-forcing-use-of-a-single-guard
        return config('auth.defaults.guard');
    }

    public function isZeroDayAdmin(): bool
    {
        // TODO: isZeroDayAdmin with uuid
        return 'ecommerce@lloricode.com' === $this->email;
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
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<\Domain\Shop\Order\Models\Order> */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\Domain\Shop\Branch\Models\Branch>
     */
    public function branches(): BelongsToMany
    {
        return $this->belongsToMany(Branch::class);
    }

    #[\Override]
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->can(Support::getPanelPermissionName($panel));
    }

    /**
     * @param  \Domain\Shop\Branch\Models\Branch  $tenant
     */
    #[\Override]
    public function canAccessTenant(Model $tenant): bool
    {
        return $this->can(Support::getPanelPermissionName('branch')) && $this->branches->contains($tenant);
    }

    /**
     * @return array<int, \Domain\Shop\Branch\Models\Branch>|\Illuminate\Support\Collection<int, \Domain\Shop\Branch\Models\Branch>
     */
    #[\Override]
    public function getTenants(Panel $panel): array|Collection
    {
        return $this->branches;
    }

    #[\Override]
    public function getFilamentAvatarUrl(): ?string
    {
        return Gravatar::get(email: $this->email);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\Domain\Shop\Branch\Models\Branch>
     */
    public function branchNotifications(): BelongsToMany
    {
        return $this->belongsToMany(
            Branch::class,
            (new AdminBranchOrderNotificationsPivot())->getTable(),
        )
            ->using(AdminBranchOrderNotificationsPivot::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\MorphMany<\Support\Google2FA\Models\GoogleTwoFactorRecoveryCode> */
    public function googleTwoFactorRecoveryCodes(): MorphMany
    {
        return $this->morphMany(GoogleTwoFactorRecoveryCode::class, 'model');
    }

    public function google2faEnabled(): bool
    {
        return null !== $this->google2fa_secret;
    }
}
