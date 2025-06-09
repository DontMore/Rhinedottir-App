<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithProperties;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use App\Models\ReagenIn;
use App\Models\LogbookReagen;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HistoricalExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithProperties
{
    protected $start_date;
    protected $end_date;
    protected $reagen;

    public function __construct($start_date = null, $end_date = null, $reagen = null)
    {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->reagen = $reagen;
    }

    public function collection()
    {
        // Get Reagen In data
        $reagenInQuery = ReagenIn::with(['reagen'])
            ->join('users', 'reagens_in.user_id', '=', 'users.id')
            ->select(
                'reagens_in.created_at',
                'reagens_in.noCatalog',
                'reagens_in.Id',
                DB::raw('CAST(reagens_in.quantity AS SIGNED) as quantity'),
                DB::raw('NULL as description'),
                'users.name as user_name',
                DB::raw("'in' as transaction_type")
            );

        // Get Logbook (Reagen Out) data
        $reagenOutQuery = LogbookReagen::with(['reagen'])
            ->join('users', 'logbook_reagens.user_id', '=', 'users.id')
            ->select(
                'logbook_reagens.created_at',
                'logbook_reagens.noCatalog',
                'logbook_reagens.id as Id',
                DB::raw('CAST(logbook_reagens.quantity_taken AS SIGNED) as quantity'),
                'logbook_reagens.note as description',
                'users.name as user_name',
                DB::raw("'out' as transaction_type")
            );

        if ($this->start_date) {
            $reagenInQuery->whereDate('reagens_in.created_at', '>=', $this->start_date);
            $reagenOutQuery->whereDate('logbook_reagens.created_at', '>=', $this->start_date);
        }
        if ($this->end_date) {
            $reagenInQuery->whereDate('reagens_in.created_at', '<=', $this->end_date);
            $reagenOutQuery->whereDate('logbook_reagens.created_at', '<=', $this->end_date);
        }
        if ($this->reagen) {
            $reagenInQuery->where('reagens_in.noCatalog', $this->reagen);
            $reagenOutQuery->where('logbook_reagens.noCatalog', $this->reagen);
        }

        return $reagenInQuery->union($reagenOutQuery)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Date',
            'Type',
            'Reagen',
            'Quantity',
            'Recorded By',
            'Description'
        ];
    }

    public function map($history): array
    {
        return [
            Carbon::parse($history->created_at)->format('d/m/Y H:i'),
            $history->transaction_type === 'in' ? 'In' : 'Out',
            $history->reagen->nameReagen,
            $history->quantity,
            $history->user_name,
            $history->description ?? '-'
        ];
    }

    public function properties(): array
    {
        return [
            'title' => 'Historical Report',
            'creator' => config('app.name'),
            'company' => config('app.name'),
            'description' => 'Historical Report of Reagen Movement',
            'subject' => 'Reagen Historical Report',
            'keywords' => 'reagen,historical,report',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        $data = $this->collection();
        
        // Set page setup
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(0);
        
        // Set print area
        $sheet->getPageSetup()->setPrintArea("A1:F$lastRow");
        
        // Set smaller font size and compact layout
        $sheet->getStyle('A1:F'.$lastRow)->applyFromArray([
            'font' => [
                'size' => 8
            ],
            'alignment' => [
                'vertical' => 'center',
                'indent' => 1
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC']
                ]
            ]
        ]);

        // Header styling
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 8
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F3F4F6']
            ]
        ]);
        
        // Compact dimensions
        $sheet->getDefaultRowDimension()->setRowHeight(13);
        $sheet->getRowDimension(1)->setRowHeight(15);
        
        $sheet->getColumnDimension('A')->setWidth(11); // Date
        $sheet->getColumnDimension('B')->setWidth(5);  // Type
        $sheet->getColumnDimension('C')->setWidth(18); // Reagen
        $sheet->getColumnDimension('D')->setWidth(7);  // Quantity
        $sheet->getColumnDimension('E')->setWidth(13); // Recorded By
        $sheet->getColumnDimension('F')->setWidth(20); // Description

        // Summary section with slim styling
        $summaryRow = $lastRow + 1;
        $sheet->getStyle("A{$summaryRow}:F" . ($summaryRow + 8))->applyFromArray([
            'font' => [
                'size' => 8
            ]
        ]);

        // Calculate totals
        $totalIn = $data->where('transaction_type', 'in')
            ->sum(function($item) { return (int)$item->quantity; });
        $totalOut = $data->where('transaction_type', 'out')
            ->sum(function($item) { return (int)$item->quantity; });

        // Add summary rows
        $sheet->setCellValue("A{$summaryRow}", 'Summary');
        
        $sheet->setCellValue("A" . ($summaryRow + 1), 'Total Overall In:');
        $sheet->setCellValue("B" . ($summaryRow + 1), $totalIn);
        $sheet->setCellValue("A" . ($summaryRow + 2), 'Total Overall Out:');
        $sheet->setCellValue("B" . ($summaryRow + 2), $totalOut);

        // Per reagent calculations
        $perReagenSummary = $data->groupBy('noCatalog')
            ->map(function ($group) {
                return [
                    'name' => $group->first()->reagen->nameReagen,
                    'in' => $group->where('transaction_type', 'in')
                        ->sum(function($item) { return (int)$item->quantity; }),
                    'out' => $group->where('transaction_type', 'out')
                        ->sum(function($item) { return (int)$item->quantity; })
                ];
            });

        $currentRow = $summaryRow + 4;
        $sheet->setCellValue("A{$currentRow}", 'Per Reagen Summary:');
        $currentRow++;

        foreach ($perReagenSummary as $noCatalog => $summary) {
            $sheet->setCellValue("A{$currentRow}", $summary['name']);
            $sheet->setCellValue("B{$currentRow}", "In: {$summary['in']} | Out: {$summary['out']}");
            $currentRow++;
        }

        // Style the per reagen summary
        $perReagenStartRow = $summaryRow + 4;
        $sheet->getStyle("A{$perReagenStartRow}:B{$currentRow}")->getFont()->setSize(9);

        return [
            'A1:F1' => [
                'font' => ['bold' => true, 'size' => 8],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F3F4F6']
                ]
            ]
        ];
    }
}
