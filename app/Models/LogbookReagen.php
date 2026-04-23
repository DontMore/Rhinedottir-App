<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LogbookReagen extends Model
{
    use HasFactory;

    protected $table = 'logbook_reagens';
    protected $primaryKey = 'guid';
    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = true;

    // ✅ PERBAIKAN: Tambahkan 'noCatalog' ke fillable
    protected $fillable = [
        'guid',
        'noCatalog',        // 👈 WAJIB: agar bisa di-mass-assign
        'reagen_guid',
        'user_id',
        'organization_guid', // 👈 Pastikan ini juga ada
        'batch',
        'quantity_taken',
        'note',
        'created_at'
    ];

    protected $dates = ['created_at', 'updated_at'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->guid)) {
                $model->guid = (string) Str::uuid();
            }
        });
    }

    // Relasi ke tabel 'reagens'
    public function reagen()
    {
        return $this->belongsTo(Reagen::class, 'reagen_guid', 'guid');
    }

    // Relasi ke tabel 'users'
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
