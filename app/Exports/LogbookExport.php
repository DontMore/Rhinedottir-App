<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use App\Models\LogbookReagen;

class LogbookExport implements FromCollection, WithHeadings, WithMapping
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
        $query = LogbookReagen::with(['reagen', 'user']);

        if ($this->start_date) {
            $query->whereDate('created_at', '>=', $this->start_date);
        }
        if ($this->end_date) {
            $query->whereDate('created_at', '<=', $this->end_date);
        }
        if ($this->reagen) {
            $query->where('noCatalog', $this->reagen);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Date',
            'Reagen Name',
            'Quantity Taken',
            'User',
            'Notes'
        ];
    }

    public function map($logbook): array
    {
        return [
            $logbook->created_at->format('d/m/Y H:i'),
            $logbook->reagen->nameReagen,
            $logbook->quantity_taken,
            $logbook->user->name,
            $logbook->notes
        ];
    }
}
