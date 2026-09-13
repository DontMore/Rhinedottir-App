<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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
    ];

    protected $casts = [
        'revision_date' => 'date',
        'is_latest' => 'boolean',
    ];

    // Relasi ke Reagen
    public function reagen()
    {
        return $this->belongsTo(Reagen::class, 'reagen_guid', 'guid');
    }

    // Relasi ke User (uploader)
    public function uploader()
    {
        return $this->belongsTo(\App\Models\User::class, 'uploaded_by', 'id');
    }

    // Helper: URL download/view
    public function getViewUrlAttribute(): string
    {
        return route('reagen.msds.document.view', $this->id);
    }

    // Helper: Ukuran file human-readable
    public function getFileSizeAttribute(): string
    {
        try {
            $bytes = \Storage::disk('public')->size($this->file_path);
            if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
            return round($bytes / 1024, 2) . ' KB';
        } catch (\Throwable $e) {
            return 'N/A';
        }
    }

    // Helper: Format tanggal revisi
    public function getRevisionDateFormattedAttribute(): string
    {
        return $this->revision_date ? $this->revision_date->format('d M Y') : '-';
    }
}