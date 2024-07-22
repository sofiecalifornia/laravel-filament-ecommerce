<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets;

use App\Filament\Admin\Resources\Shop\OrderResource as MainOrderResourceAlias;
use App\Filament\Admin\Support\TenantHelper;
use App\Filament\Branch\Resources\Shop\OrderResource as BranchOrderResourceAlias;
use Domain\Access\Role\Contracts\HasPermissionWidgets;
use Domain\Access\Role\PermissionWidgets;
use Domain\Shop\Order\Models\Order;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestOrders extends TableWidget implements HasPermissionWidgets
{
    use PermissionWidgets;

    protected static ?int $sort = 7;

    #[\Override]
    public function table(Table $table): Table
    {
        return $table
            ->query(fn () => Order::limit(5)->latest())
            ->columns([
                Tables\Columns\TextColumn::make('customer.full_name')
                    ->translateLabel(),

                Tables\Columns\TextColumn::make('branch.name')
                    ->hidden(fn (): bool => null !== TenantHelper::getBranch())
                    ->translateLabel(),

                Tables\Columns\TextColumn::make('total_price')
                    ->translateLabel()
                    ->money(),

                Tables\Columns\TextColumn::make('created_at')
                    ->translateLabel()
                    ->dateTime(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->translateLabel()
                    ->authorize('view')
                    ->url(fn (Order $record): string => match (null === TenantHelper::getBranch()) {
                        true => MainOrderResourceAlias::getUrl('view', ['record' => $record]),
                        default => BranchOrderResourceAlias::getUrl('view', ['record' => $record]),
                    }),
            ])
            ->paginated(false);
    }
}
