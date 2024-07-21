<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Filament\Support\TenantHelper;
use Closure;
use Domain\Shop\Order\Models\Order;
use Domain\Shop\Stock\Models\SkuStock;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApplyBranchTenantScopes
{
    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var \Domain\Shop\Branch\Models\Branch $branch */
        $branch = TenantHelper::getBranch();

        Order::addGlobalScope(
            fn (Builder $query) => $query->whereBelongsTo($branch),
        );

        SkuStock::addGlobalScope(
            fn (Builder $query) => $query->whereBelongsTo($branch),
        );

        return $next($request);
    }
}
