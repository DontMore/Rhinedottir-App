<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class StockReagen extends Model
{
    protected $table = 'stock_reagens';
    protected $primaryKey = 'guid';
    public $incrementing = false;
    protected $keyType = 'string';

    // ✅ PERBAIKAN: Tambahkan 'noCatalog' ke fillable
    protected $fillable = [
        'guid',
        'noCatalog',        // 👈 WAJIB: agar bisa di-mass-assign
        'reagen_guid',
        'batch',
        'quantity',
        'expiredDate',
        'note',
        'stockUpdateDate',
        'organization_guid',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->guid)) {
                $model->guid = (string) Str::uuid();
            }
        });
    }

    // Relasi ke Reagen menggunakan GUID
    public function reagen()
    {
        return $this->belongsTo(Reagen::class, 'reagen_guid', 'guid');
    }

    // Perbaiki juga relasi ini (sebelumnya salah pakai guid,guid)
    public function logbookReagen()
    {
        return $this->belongsTo(LogbookReagen::class, 'noCatalog', 'noCatalog');
    }
}