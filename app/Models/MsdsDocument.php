<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MsdsDocument extends Model
{
    protected $fillable = [
        'reagen_guid',
        'file_path',
        'file_name',
        'file_size',
        'file_type',
        'title',
        'trainers', // ✅ Tambahkan ini
        'uploaded_by',
    ];

    protected $casts = [
        'trainers' => 'array', // ✅ Auto-convert JSON to array
    ];

    public function reagen()
    {
        return $this->belongsTo(Reagen::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // Helper: Format ukuran file
    public function getFormattedSizeAttribute()
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) return number_format($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024) return number_format($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }

    // Helper: Icon berdasarkan tipe file
    public function getFileIconAttribute()
    {
        return match($this->file_type) {
            'pdf'  => 'bi-file-earmark-pdf-fill text-red-500',
            'ppt'  => 'bi-file-earmark-ppt-fill text-orange-500',
            'pptx' => 'bi-file-earmark-ppt-fill text-orange-500',
            default => 'bi-file-earmark-fill text-slate-400',
        };
    }

}