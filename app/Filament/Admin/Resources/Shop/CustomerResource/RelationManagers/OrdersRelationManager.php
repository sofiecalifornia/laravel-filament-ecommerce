<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Shop\CustomerResource\RelationManagers;

use App\Filament\Admin\Resources\Shop\OrderResource;
use App\Filament\Admin\Resources\Shop\OrderResource\Pages\ViewOrder;
use Domain\Shop\Customer\Models\Customer;
use Domain\Shop\Order\Enums\Status;
use Domain\Shop\Order\Models\Order;
use Exception;
use Filament\Infolists\Infolist;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';

    protected static ?string $recordTitleAttribute = 'receipt_number';

    #[\Override]
    public static function getBadgeTooltip(Model $ownerRecord, string $pageClass): ?string
    {
        return trans('There are new pending orders.');
    }

    #[\Override]
    public static function getBadgeColor(Model $ownerRecord, string $pageClass): ?string
    {
        return 'warning';
    }

    #[\Override]
    public static function getBadge(Model $ownerRecord, string $pageClass): ?string
    {
        /** @var Customer $ownerRecord */
        $ordersCount = $ownerRecord->loadCount([
            'orders' => fn (Builder $query) =>
                /** @var Order $query */
                $query->where('status', Status::pending),
        ])->orders_count;

        if (0 === $ordersCount) {
            return null;
        }

        return (string) $ordersCount;
    }

    #[\Override]
    public function infolist(Infolist $infolist): Infolist
    {
        return ViewOrder::staticInfolist($infolist);
    }

    /** @throws Exception */
    #[\Override]
    public function table(Table $table): Table
    {
        return OrderResource::table($table)
            ->headerActions([
                Action::make('new_order')
                    ->translateLabel()
                    ->url(
                        OrderResource::can('create')
                         ? OrderResource::getUrl('create', ['customer' => $this->ownerRecord->getRouteKey()])
                            : null
                    ),
            ]);
    }
}
