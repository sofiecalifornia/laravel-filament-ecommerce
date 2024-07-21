<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMainAdminPanel
{
    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, \Closure $next): Response
    {
        /** @var \Domain\Access\Admin\Models\Admin|null $admin */
        $admin = Filament::auth()->user();

        if ($admin !== null && $admin->isBranch()) {
            abort(404, 'You are not allowed to access this page.');
        }

        return $next($request);
    }
}
