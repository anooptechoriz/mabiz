<?php
namespace App\Exports;
use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Illuminate\Contracts\View\View;

class BulkExport implements FromView, ShouldAutoSize, WithEvents
{
    private $users;

    public function __construct($users)
    {
        $this->users = $users;
    }

    /**
     * @return View
     */
    public function view(): View
    {
        return view('admin.reports.order_data_export', ['ordersList' => $this->users]);
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
