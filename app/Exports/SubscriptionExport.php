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

class SubscriptionExport implements FromView, ShouldAutoSize, WithEvents
{
    private $subscription;

    public function __construct($subscription)
    {
        $this->subscription = $subscription;
    }

    /**
     * @return View
     */
    public function view(): View
    {
        return view('admin.reports.export.subscription_report_export', ['subscription' => $this->subscription]);
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
