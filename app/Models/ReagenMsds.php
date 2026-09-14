<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ReagenMsds extends Model
{
    protected $table = 'reagen_msds';

    protected $fillable = [
        'reagen_guid',
        'file_name',
        'file_path',
        'version',
        'revision_date',
        'notes',
        'is_latest',
        'organization_guid',
        'uploaded_by',
        'review_status',      // ✅ BARU
        'review_note',         // ✅ BARU
        'reviewed_at',         // ✅ BARU
        'reviewed_by',         // ✅ BARU
    ];

    protected $casts = [
        'revision_date' => 'date',
        'reviewed_at' => 'datetime',
        'is_latest' => 'boolean',
    ];

    // ... (relasi yang sudah ada) ...

    /**
     * ✅ BARU: Cek apakah MSDS perlu review (lebih dari 1 tahun)
     */
    public function needsReview(): bool
    {
        // Jika sudah dikonfirmasi "belum ada update" (no_update) dan reviewed_at kurang dari 1 tahun yang lalu, tidak perlu review
        if ($this->review_status === 'no_update' && $this->reviewed_at) {
            return $this->reviewed_at->lte(Carbon::now()->subYear());
        }

        if (!$this->revision_date) {
            return true; // Jika tidak ada tanggal revisi, anggap perlu review
        }

        return $this->revision_date->lte(Carbon::now()->subYear());
    }

    /**
     * ✅ BARU: Hitung berapa lama sejak revisi terakhir
     */
    public function getYearsSinceRevision(): float
    {
        if (!$this->revision_date) {
            return 0;
        }

        return round($this->revision_date->diffInMonths(Carbon::now()) / 12, 1);
    }

    /**
     * ✅ BARU: Scope untuk MSDS yang perlu review
     */
    public function scopeNeedsReview($query)
    {
        return $query->where(function ($q) {
            $q->where('review_status', 'pending')
              ->orWhereNull('review_status');
        })->where(function ($q) {
            $q->where('revision_date', '<', Carbon::now()->subYear())
              ->orWhereNull('revision_date');
        });
    }

    /**
     * ✅ BARU: Scope untuk MSDS terbaru (latest) yang perlu review
     */
    public function scopeLatestNeedsReview($query)
    {
        return $query->where('is_latest', true)->needsReview();
    }
}