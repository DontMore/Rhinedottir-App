<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use App\Models\ReagenIn;
use Carbon\Carbon;

class ExpiredReagenExport implements FromCollection, WithHeadings, WithMapping
{
    protected $status;

    public function __construct($status = null)
    {
        $this->status = $status;
    }

    public function collection()
    {
        $query = ReagenIn::with(['reagen'])
            ->whereNotNull('expiredDate')
            ->where(function($q) {
                $q->whereDate('expiredDate', '<=', now()->addMonths(3))
                  ->orWhereDate('expiredDate', '<', now());
            });

        if ($this->status === 'expired') {
            $query->whereDate('expiredDate', '<', now());
        } elseif ($this->status === 'near') {
            $query->whereDate('expiredDate', '>', now())
                  ->whereDate('expiredDate', '<=', now()->addMonths(3));
        }

        return $query->orderBy('expiredDate')->get();
    }

    public function headings(): array
    {
        return [
            'No. Catalog',
            'Reagen Name',
            'Batch',
            'Expired Date',
            'Status',
            'Remaining Days'
        ];
    }

    public function map($reagent): array
    {
        $expiredDate = Carbon::parse($reagent->expiredDate);
        $isExpired = $expiredDate->isPast();
        $remainingDays = now()->diffInDays($expiredDate, false);

        return [
            $reagent->noCatalog,
            $reagent->reagen->nameReagen,
            $reagent->batch,
            $expiredDate->format('d/m/Y'),
            $isExpired ? 'Expired' : 'Near Expiry',
            $remainingDays . ' days'
        ];
    }
}
