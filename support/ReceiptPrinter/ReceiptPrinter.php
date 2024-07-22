<?php

declare(strict_types=1);

namespace Support\ReceiptPrinter;

use Domain\Access\Admin\Models\Admin;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\CupsPrintConnector;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Support\ReceiptPrinter\Data\ReceiptPrinterData;

// https://github.com/charlieuki/receipt-printer
final readonly class ReceiptPrinter
{
    private Printer $printer;

    /**
     * @throws \Exception
     */
    public function __construct(private ReceiptPrinterData $receiptPrinterData)
    {
        /** @var string $connectorDescriptor */
        $connectorDescriptor = config('support.receipt-printer.connector_descriptor');

        $connector = match (config('support.receipt-printer.connector_type')) {
            'cups' => new CupsPrintConnector($connectorDescriptor),
            'windows' => new WindowsPrintConnector($connectorDescriptor),
            'network' => new NetworkPrintConnector($connectorDescriptor),
            'file' => new FilePrintConnector($connectorDescriptor),
            default => throw new \Exception('Invalid printer connector type.'),
        };

        $this->printer = new Printer($connector);

    }

    private function printImage(): void
    {
        if (null === $this->receiptPrinterData->logo) {
            return;
        }

        $image = EscposImage::load($this->receiptPrinterData->logo, false);

        $this->printer->feed();

        //        switch ($mode) {
        //            case 0:
        $this->printer->graphics($image);
        //                break;
        //            case 1:
        //                $this->printer->bitImage($image);
        //                break;
        //            case 2:
        //                $this->printer->bitImageColumnFormat($image);
        //                break;
        //        }

        $this->printer->feed();

    }

    public function send(): void
    {
        $subtotal = money(0);
        foreach ($this->receiptPrinterData->items as $item) {
            $subtotal = $item->subTotal->add($subtotal);
        }
        $tax = $subtotal->multiply($this->receiptPrinterData->taxPercentage / 100);
        $grandTotal = $subtotal->add($tax);

        // Init printer settings
        $this->printer->initialize();
        $this->printer->selectPrintMode();
        // Set margins
        $this->printer->setPrintLeftMargin(1);
        // Print receipt headers
        $this->printer->setJustification(Printer::JUSTIFY_CENTER);

        $this->printImage();

        $this->printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
        $this->printer->feed(2);
        $this->printer->text($this->receiptPrinterData->store->name.PHP_EOL);
        $this->printer->selectPrintMode();
        $this->printer->text($this->receiptPrinterData->store->address.PHP_EOL);
        $this->printer->text(Formatter::header(
            'TID: '.$this->receiptPrinterData->transactionId,
            'MID: '.$this->receiptPrinterData->store->mid
        ).PHP_EOL);
        $this->printer->feed();
        // Print receipt title
        $this->printer->setEmphasis();
        $this->printer->text('RECEIPT'.PHP_EOL);
        $this->printer->setEmphasis(false);
        $this->printer->feed();

        // Print items
        $this->printer->setJustification(Printer::JUSTIFY_LEFT);
        foreach ($this->receiptPrinterData->items as $item) {
            $this->printer->text(Formatter::item($item));
        }
        $this->printer->feed();

        // subtotal
        $this->printer->setEmphasis();
        $this->printer->text(Formatter::summary('Subtotal', $subtotal->getValue()));
        $this->printer->setEmphasis(false);
        $this->printer->feed();

        // tax
        $this->printer->text(Formatter::summary('Tax', $tax->getValue()));
        $this->printer->feed(2);

        // grand total
        $this->printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
        $this->printer->text(Formatter::summary('TOTAL', $grandTotal->getValue(), true));
        $this->printer->feed();
        $this->printer->selectPrintMode();

        if (filled($this->receiptPrinterData->qrCode)) {
            /** @var string $json */
            $json = json_encode($this->receiptPrinterData->qrCode);
            $this->printer->qrCode($json, Printer::QR_ECLEVEL_L, 8);
        }

        // footer
        $this->printer->feed();
        $this->printer->setJustification(Printer::JUSTIFY_CENTER);
        $this->printer->text('Thank you for shopping!'.PHP_EOL);
        $this->printer->feed();

        // date
        $this->printer->text(self::now()->format('j F Y H:i:s'));
        $this->printer->feed(3);

        $this->printer->cut();

        //        $this->printer->openDrawer();
        $this->printer->close();

    }

    private static function now(): Carbon
    {
        $timezone = config('app-default.timezone');

        if (Auth::check()) {
            /** @var Admin $admin */
            $admin = Auth::user();
            $timezone = $admin->timezone;
        }

        return now()->timezone($timezone);
    }
}
