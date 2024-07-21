<?php

declare(strict_types=1);

namespace App\Providers\Macros;

use Closure;
use Illuminate\Database\Schema\ColumnDefinition;

/**
 * @mixin \Illuminate\Database\Schema\Blueprint
 *
 * @codeCoverageIgnore
 */
class BluePrintMixin
{
    public function phpEnum(): Closure
    {
        return function ($column, string $comment = null): ColumnDefinition {
            $comment = is_null($comment)
                ? 'PHP backed enum'
                : $comment.' (PHP backed enum)';

            return $this->string($column, 100)->comment($comment)->index();
        };
    }

    public function eloquentSortable(): Closure
    {
        return function ($column = null, string $comment = null): ColumnDefinition {
            $comment = is_null($comment)
                ? 'manage by spatie/eloquent-sortable'
                : $comment.' (manage by spatie/eloquent-sortable)';

            return $this->unsignedBigInteger(
                $column ?? config('eloquent-sortable.order_column_name')
            )->comment($comment);
        };
    }

    public function money(): Closure
    {
        return function ($column, string $comment = null): ColumnDefinition {
            $comment = is_null($comment)
                ? 'for money'
                : $comment.' (for money)';

            return $this->unsignedInteger($column)->comment($comment);
        };
    }
}
