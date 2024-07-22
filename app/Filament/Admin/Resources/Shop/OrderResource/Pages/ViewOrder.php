<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Shop\OrderResource\Pages;

use App\Filament\Admin\Resources\Shop\CustomerResource;
use App\Filament\Admin\Resources\Shop\OrderResource;
use App\Filament\Admin\Support\TenantHelper;
use Domain\Shop\Order\Actions\PrintReceiptAction;
use Domain\Shop\Order\Enums\PaymentMethod;
use Domain\Shop\Order\Enums\PaymentStatus;
use Domain\Shop\Order\Enums\Status;
use Domain\Shop\Order\Models\Order;
use Exception;
use Filament\Actions;
use Filament\Forms;
use Filament\Infolists;
use Filament\Infolists\Components\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\HtmlString;

/**
 * @property-read \Domain\Shop\Order\Models\Order $record
 */
class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    #[\Override]
    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('download_latest_invoice')
                ->translateLabel()
                ->icon('heroicon-s-printer')
                ->hidden($this->record->trashed())
                ->url(
                    fn () => route('admin.orders.download.invoice', $this->record),
                    shouldOpenInNewTab: true
                )
                ->authorize('downloadInvoice'),

            Actions\Action::make('print_receipt')
                ->translateLabel()
                ->icon('heroicon-s-printer')
                ->hidden($this->record->trashed())
                ->successNotificationTitle(trans('Receipt sent to printer!'))
                ->failureNotificationTitle(trans('Failed to send receipt to printer!'))
                ->action(function (Actions\Action $action) {

                    try {
                        app(PrintReceiptAction::class)->execute($this->record);
                        $action->success();
                    } catch (Exception $e) {
                        report($e);
                        $action->failure();
                    }

                })
                ->authorize('printReceipt'),

            Actions\DeleteAction::make()
                ->translateLabel(),
            Actions\RestoreAction::make()
                ->translateLabel(),
            Actions\ForceDeleteAction::make()
                ->translateLabel(),
        ];
    }

    #[\Override]
    public function infolist(Infolists\Infolist $infolist): Infolists\Infolist
    {
        return self::staticInfolist($infolist);
    }

    public static function staticInfolist(Infolists\Infolist $infolist): Infolists\Infolist
    {
        return $infolist->schema([

            Infolists\Components\Group::make()

                ->columnSpan(2)
                ->schema([

                    Infolists\Components\Section::make()
                        ->columns()
                        ->schema([
                            Infolists\Components\TextEntry::make('receipt_number')
                                ->translateLabel()
                                ->copyable()
                                ->icon('heroicon-s-receipt-refund'),

                            Infolists\Components\TextEntry::make('customer.full_name')
                                ->translateLabel()
                                ->icon('heroicon-s-user')
                                ->url(
                                    function (Order $record): ?string {

                                        if (null !== TenantHelper::getBranch()) {
                                            return null;
                                        }

                                        return CustomerResource::can('edit', $record->customer)
                                            ? CustomerResource::getUrl('edit', [$record->customer])
                                            : null;
                                    },
                                    shouldOpenInNewTab: true
                                ),

                            Infolists\Components\TextEntry::make('branch.name')
                                ->translateLabel()
                                ->icon('heroicon-o-building-storefront'),

                            Infolists\Components\TextEntry::make('claim_at')
                                ->translateLabel()
                                ->dateTime()
                                ->icon('heroicon-s-truck'),

                            Infolists\Components\TextEntry::make('claim_type')
                                ->translateLabel(),

                            Infolists\Components\TextEntry::make('payment_method')
                                ->translateLabel()
                                ->default(new HtmlString('&mdash;'))
                                ->suffixAction(
                                    Action::make('update_payment_method')
                                        ->translateLabel()
                                        ->icon('heroicon-o-pencil')
                                        ->slideOver()
                                        ->button()
                                        ->authorize(fn (Order $record) => Gate::allows('updatePaymentMethod', $record))
                                        ->form(fn (Order $record) => [
                                            Forms\Components\ToggleButtons::make('payment_method')
                                                ->translateLabel()
                                                ->default($record->payment_method)
                                                ->inline()
                                                ->options(PaymentMethod::class)
                                                ->enum(PaymentMethod::class)
                                                ->required(),
                                        ])
                                        ->successNotificationTitle(
                                            trans('Payment method updated successfully!')
                                        )
                                        ->action(function (Action $action, Order $record, array $data): void {
                                            $record->update(['payment_method' => $data['payment_method']]);

                                            $action->success();
                                        })
                                ),

                            Infolists\Components\TextEntry::make('payment_status')
                                ->translateLabel()
                                ->suffixAction(
                                    Action::make('update_payment_status')
                                        ->translateLabel()
                                        ->icon('heroicon-o-pencil')
                                        ->button()
                                        ->slideOver()
                                        ->authorize(fn (Order $record) => Gate::allows('updatePaymentStatus', $record))
                                        ->form(fn (Order $record) => [
                                            Forms\Components\ToggleButtons::make('payment_status')
                                                ->translateLabel()
                                                ->default($record->payment_status)
                                                ->inline()
                                                ->options(PaymentStatus::class)
                                                ->enum(PaymentStatus::class)
                                                ->required(),
                                        ])
                                        ->successNotificationTitle(
                                            trans('Payment status updated successfully!')
                                        )
                                        ->action(function (Action $action, Order $record, array $data): void {
                                            $record->update(['payment_status' => $data['payment_status']]);

                                            $action->success();

                                        }),
                                ),

                            Infolists\Components\TextEntry::make('status')
                                ->translateLabel()
                                ->suffixAction(
                                    Action::make('update_status')
                                        ->translateLabel()
                                        ->icon('heroicon-o-pencil')
                                        ->button()
                                        ->slideOver()
                                        ->authorize(fn (Order $record) => Gate::allows('updateStatus', $record))
                                        ->form(fn (Order $record) => [
                                            Forms\Components\ToggleButtons::make('status')
                                                ->translateLabel()
                                                ->default($record->status)
                                                ->inline()
                                                ->options(Status::class)
                                                ->enum(Status::class)
                                                ->required(),
                                        ])
                                        ->successNotificationTitle(
                                            trans('Status updated successfully!')
                                        )
                                        ->action(function (Action $action, Order $record, array $data): void {
                                            $record->update(['status' => $data['status']]);

                                            $action->success();

                                        })
                                ),
                        ]),

                    Infolists\Components\Section::make()
                        ->schema([
                            Infolists\Components\TextEntry::make('notes'),
                        ]),

                    Infolists\Components\Section::make()
                        ->schema([
                            Infolists\Components\RepeatableEntry::make('orderItems')
                                ->translateLabel()
                                ->columns(5)
                                ->schema([
                                    Infolists\Components\TextEntry::make('name')
                                        ->label(trans('Product  name'))
                                        ->icon('heroicon-s-shopping-bag'),

                                    Infolists\Components\TextEntry::make('sku.code')
                                        ->translateLabel()
                                        ->icon('heroicon-s-shopping-bag'),

                                    Infolists\Components\TextEntry::make('minimum')
                                        ->translateLabel(),

                                    Infolists\Components\TextEntry::make('price')
                                        ->translateLabel()
                                        ->icon('heroicon-s-currency-dollar')
                                        ->money(),

                                    Infolists\Components\TextEntry::make('quantity')
                                        ->translateLabel(),
                                ]),
                        ]),
                ]),

            Infolists\Components\Group::make()
                ->columnSpan(1)
                ->schema([
                    Infolists\Components\Section::make()
                        ->schema([

                            //                            Infolists\Components\TextEntry::make('delivery_price')
                            //                                ->icon('heroicon-s-truck'),

                            Infolists\Components\TextEntry::make('total_price')
                                ->translateLabel()
                                ->icon('heroicon-s-currency-dollar')
                                ->state(fn (Order $record) => $record->total_price)
                                ->money(),

                        ]),
                    Infolists\Components\Section::make()
                        ->schema([

                            Infolists\Components\TextEntry::make('created_at')
                                ->translateLabel()
                                ->dateTime()
                                ->icon('heroicon-s-calendar'),

                            Infolists\Components\TextEntry::make('updated_at')
                                ->translateLabel()
                                ->dateTime()
                                ->icon('heroicon-s-calendar'),

                            Infolists\Components\TextEntry::make('deleted_at')
                                ->translateLabel()
                                ->dateTime()
                                ->icon('heroicon-s-calendar'),
                        ]),
                ]),
        ])
            ->columns(3);
    }
}
