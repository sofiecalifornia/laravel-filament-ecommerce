<?php

declare(strict_types=1);

namespace App\Http\Controllers\API\Shop\Cart;

use App\Http\Requests\API\Shop\Cart\CartEditRequest;
use App\Http\Requests\API\Shop\Cart\CartStoreRequest;
use App\Http\Resources\Shop\CartResource;
use Domain\Shop\Branch\Models\Branch;
use Domain\Shop\Cart\Actions\CreateCartAction;
use Domain\Shop\Cart\Actions\DeleteCartAction;
use Domain\Shop\Cart\Actions\EditCartAction;
use Domain\Shop\Cart\DataTransferObjects\CreateCartData;
use Domain\Shop\Cart\DataTransferObjects\EditCartData;
use Domain\Shop\Cart\Models\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\RouteAttributes\Attributes\Delete;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Middleware;
use Spatie\RouteAttributes\Attributes\Post;
use Spatie\RouteAttributes\Attributes\Prefix;
use Spatie\RouteAttributes\Attributes\Put;
use Spatie\RouteAttributes\Attributes\ScopeBindings;

#[Prefix('branches/{enabledBranch}/carts'), Middleware('auth:sanctum')]
class CartController
{
    #[Get('/', name: 'carts.index')]
    public function index(Branch $enabledBranch): mixed
    {
        /** @var \Domain\Shop\Customer\Models\Customer $customer */
        $customer = Auth::user();

        return CartResource::collection(
            QueryBuilder::for(
                Cart::query()
                    ->whereBelongsTo($customer)
                    ->whereBelongsTo($enabledBranch)
            )
                ->allowedFilters(['sku_uuid'])
                ->allowedSorts(['sku_uuid', 'quantity'])
                ->defaultSort('updated_at')
                ->allowedIncludes(['sku.product'])
                ->jsonPaginate()
        );
    }

    /**
     * @throws \Throwable
     */
    #[Post('/', name: 'carts.store')]
    public function store(CartStoreRequest $request, Branch $enabledBranch): mixed
    {
        /** @var \Domain\Shop\Customer\Models\Customer $customer */
        $customer = Auth::user();

        $cart = DB::transaction(fn () => app(CreateCartAction::class)
            ->execute(new CreateCartData(
                branch: $enabledBranch,
                customer: $customer,
                sku_uuid: $request->input('sku_uuid'),
                quantity: (float) $request->input('quantity'),
            )));

        return CartResource::make($cart);
    }

    /**
     * @throws \Throwable
     */
    #[Put('{cart}', name: 'carts.update')]
    #[ScopeBindings]
    public function update(CartEditRequest $request, Branch $enabledBranch, Cart $cart): mixed
    {
        Gate::authorize('update', $cart);

        DB::transaction(fn () => app(EditCartAction::class)
            ->execute($cart, new EditCartData(
                quantity: (float) $request->input('quantity'),
            )));

        return CartResource::make($cart->refresh());
    }

    #[Delete('/empty', name: 'carts.empty')]
    public function empty(Branch $enabledBranch): mixed
    {
        /** @var \Domain\Shop\Customer\Models\Customer $customer */
        $customer = Auth::user();

        $customer->carts->each->delete();

        return response()->noContent();
    }

    /**
     * @throws \Throwable
     */
    #[Delete('{cart}', name: 'carts.destroy')]
    #[ScopeBindings]
    public function destroy(Branch $enabledBranch, Cart $cart): mixed
    {
        Gate::authorize('delete', $cart);

        DB::transaction(fn () => app(DeleteCartAction::class)->execute($cart));

        return response()->noContent();
    }
}
