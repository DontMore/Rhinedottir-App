<?php

namespace App\Exports;

use App\Models\Reagen;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReagenListExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithTitle
{
    protected $organizationGuid;

    public function __construct(string $organizationGuid)
    {
        $this->organizationGuid = $organizationGuid;
    }

    public function collection()
    {
        return Reagen::select('guid as reagen_guid', 'noCatalog', 'nameReagen', 'merk', 'packSize')
            ->where('organization_guid', $this->organizationGuid)
            ->withSum('stocks as total_quantity', 'quantity')
            ->orderBy('nameReagen')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Catalog Number',
            'Reagent Name',
            'Brand',
            'Pack Size',
            'Total Stock',
        ];
    }

    public function map($reagen): array
    {
        static $no = 1;

        return [
            $no++,
            $reagen->noCatalog,
            $reagen->nameReagen,
            $reagen->merk,
            $reagen->packSize,
            $reagen->total_quantity ?? 0,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Bold pada baris heading
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'E2E8F0']]],
        ];
    }

    public function title(): string
    {
        return 'Reagen List';
    }
}