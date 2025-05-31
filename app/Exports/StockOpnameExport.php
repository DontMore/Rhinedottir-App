<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use App\Models\StockHistory;
use Carbon\Carbon;

class StockOpnameExport implements FromCollection, WithHeadings, WithMapping
{
    protected $month;
    protected $year;

    public function __construct($month, $year)
    {
        $this->month = $month;
        $this->year = $year;
    }

    public function collection()
    {
        return StockHistory::with(['reagen'])
            ->where('month', $this->month)
            ->where('year', $this->year)
            ->get();
    }

    public function headings(): array
    {
        return [
            'No. Catalog',
            'Reagen Name',
            'Previous Stock',
            'In',
            'Out',
            'Current Stock',
            'Actual Stock',
            'Status',
            'Notes'
        ];
    }

    public function map($stock): array
    {
        $expectedStock = ($stock->quantity + $stock->quantity_in) - $stock->quantity_out;
        $difference = $stock->quantity_actual - $expectedStock;
        $status = $difference === 0 ? 'Equal' : ($difference > 0 ? 'Excess' : 'Deficient');

        return [
            $stock->noCatalog,
            $stock->reagen->nameReagen,
            $stock->quantity,
            $stock->quantity_in,
            $stock->quantity_out,
            $expectedStock,
            $stock->quantity_actual,
            $status,
            $stock->catatan ?? '-'
        ];
    }
}
