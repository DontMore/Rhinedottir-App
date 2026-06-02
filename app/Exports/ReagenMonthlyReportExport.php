<?php

namespace App\Exports;

use App\Models\LogbookReagen;
use App\Models\Reagen;
use App\Models\ReagenIn;
use App\Models\StockHistory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReagenMonthlyReportExport implements FromCollection, WithHeadings
{
    private int $year;
    private string $organizationGuid;

    public function __construct(int $year, string $organizationGuid)
    {
        $this->year = $year;
        $this->organizationGuid = $organizationGuid;
    }

    public function headings(): array
    {
        $bulan = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
            9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
        ];

        $headings = ['No', 'No. Catalog', 'Reagen'];

        for ($m = 1; $m <= 12; $m++) {
            $headings[] = "{$bulan[$m]} In";
            $headings[] = "{$bulan[$m]} Out";
            $headings[] = "{$bulan[$m]} Stock";
        }

        return $headings;
    }

    public function collection(): Collection
    {
        $reagens = Reagen::where('organization_guid', $this->organizationGuid)
            ->orderBy('nameReagen')
            ->get(['guid', 'nameReagen', 'noCatalog', 'merk', 'packSize']);

        $reagenGuids = $reagens->pluck('guid');

        $reagenIn = ReagenIn::select(
            'reagen_guid',
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(quantity) as qty_in')
        )
            ->whereYear('created_at', $this->year)
            ->whereIn('reagen_guid', $reagenGuids)
            ->groupBy('reagen_guid', DB::raw('MONTH(created_at)'))
            ->get()
            ->groupBy('reagen_guid');

        $reagenOut = LogbookReagen::select(
            'reagen_guid',
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(quantity_taken) as qty_out')
        )
            ->whereYear('created_at', $this->year)
            ->whereIn('reagen_guid', $reagenGuids)
            ->groupBy('reagen_guid', DB::raw('MONTH(created_at)'))
            ->get()
            ->groupBy('reagen_guid');

        $stockActual = StockHistory::select(
            'reagen_guid',
            'month',
            'year',
            DB::raw('SUM(quantity_actual) as qty_actual')
        )
            ->where('year', $this->year)
            ->whereIn('reagen_guid', $reagenGuids)
            ->groupBy('reagen_guid', 'month', 'year')
            ->get()
            ->groupBy('reagen_guid');

        $rows = [];
        foreach ($reagens as $idx => $reagen) {
            $inMonths = $reagenIn->get($reagen->guid, collect());
            $outMonths = $reagenOut->get($reagen->guid, collect());
            $stockMonths = $stockActual->get($reagen->guid, collect());

            $inByMonth = $inMonths->keyBy('month')->toArray();
            $outByMonth = $outMonths->keyBy('month')->toArray();
            $stockByMonth = $stockMonths->keyBy('month')->toArray();

            $row = [
                $idx + 1,
                $reagen->noCatalog ?? '',
                $reagen->nameReagen,
            ];

            for ($m = 1; $m <= 12; $m++) {
                $row[] = isset($inByMonth[$m]) ? (float) $inByMonth[$m]['qty_in'] : 0;
                $row[] = isset($outByMonth[$m]) ? (float) $outByMonth[$m]['qty_out'] : 0;
                $row[] = isset($stockByMonth[$m]) ? (float) $stockByMonth[$m]['qty_actual'] : 0;
            }

            $rows[] = $row;
        }

        return collect($rows);
    }
}
