<?php

declare(strict_types=1);

namespace Support\Google2FA\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $model_type
 * @property string $model_id
 * @property mixed $code
 * @property \Illuminate\Support\Carbon|null $used_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\Support\Google2FA\Models\GoogleTwoFactorRecoveryCode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Support\Google2FA\Models\GoogleTwoFactorRecoveryCode newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Support\Google2FA\Models\GoogleTwoFactorRecoveryCode query()
 *
 * @mixin \Eloquent
 */
class GoogleTwoFactorRecoveryCode extends Model
{
    protected $fillable = [
        'code',
        'used_at',
    ];

    #[\Override]
    protected function casts(): array
    {
        return [
            'code' => 'encrypted',
            'used_at' => 'datetime',
        ];
    }

    public function isUsed(): bool
    {
        return null !== $this->used_at;
    }
}
