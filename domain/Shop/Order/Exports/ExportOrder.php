<?php

declare(strict_types=1);

namespace Domain\Shop\Order\Exports;

use Domain\Shop\Order\Models\Order;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Excel;

class ExportOrder implements FromView, ShouldAutoSize
{
    use Exportable;

    /** @phpstan-ignore-next-line  */
    private string $writerType;

    /** @phpstan-ignore-next-line  */
    private string $fileName;

    public function __construct(
        private readonly Order $order
    ) {
        $this->writerType = Excel::MPDF;

        //        $title = Str::slug($order->userFullName());
        $this->fileName = $this->order->receipt_number.'.pdf';
    }

    public function view(): View
    {
        return view('exports.order.export', ['order' => $this->order]);
    }
}
