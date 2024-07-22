<?php

declare(strict_types=1);

//namespace Domain\Shop\Product\Actions;
//
//use Domain\Shop\Product\Models\Product;
//use Domain\Shop\Stock\Enums\StockType;
//use Illuminate\Database\Eloquent\Builder;
//
//class GetProductHasStockCountAction
//{
//    private function execute(): int
//    {
//        return Product::whereRelation(
//            'skus.skuStocks',
//            function (Builder $query) {
//                $query->where('type', StockType::unlimited)
//                    ->orWhere(function (Builder $query) {
//                        $query->whereBaseOnStocksNotZero();
//                    });
//            })
//            ->count();
//    }
//}
