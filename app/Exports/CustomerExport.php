<?php
namespace App\Exports;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Illuminate\Contracts\View\View;



class CustomerExport implements FromView, ShouldAutoSize, WithEvents
{
    private $customers;

    public function __construct($customers)
    {
        $this->customers = $customers;
    }

    /**
     * @return View
     */
    public function view(): View
    {
        return view('admin.reports.export.customer_report_export', ['customers' => $this->customers]);
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getDelegate()->setRightToLeft(false);
            },
        ];
    }
}
