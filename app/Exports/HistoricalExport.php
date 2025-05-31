<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use App\Models\ReagenIn;
use Carbon\Carbon;

class HistoricalExport implements FromCollection, WithHeadings, WithMapping
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
        $query = ReagenIn::with(['reagen'])
            ->join('users', 'reagens_in.user_id', '=', 'users.id')
            ->select('reagens_in.*', 'users.name as user_name');

        if ($this->start_date) {
            $query->whereDate('reagens_in.created_at', '>=', $this->start_date);
        }
        if ($this->end_date) {
            $query->whereDate('reagens_in.created_at', '<=', $this->end_date);
        }
        if ($this->reagen) {
            $query->where('reagens_in.noCatalog', $this->reagen);
        }

        return $query->orderBy('reagens_in.created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Date',
            'Reagen',
            'Quantity In',
            'Recorded By',
            'Description'
        ];
    }

    public function map($history): array
    {
        return [
            Carbon::parse($history->created_at)->format('d/m/Y H:i'),
            $history->reagen->nameReagen,
            $history->quantity,
            $history->user_name,
            $history->description
        ];
    }
}
