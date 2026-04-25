<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';
    protected $primaryKey = 'guid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'guid',
        'noCatalog',
        'nameReagen',
        'merk',
        'packSize',
        'quantity',
        'userId',             // Biarkan sementara untuk kompatibilitas view lama
        'user_guid',          // ✅ KOLOM BARU
        'organization_guid',  // ✅ TAMBAHKAN JIKA BELUM ADA
        'status',
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

    // ✅ Update relasi agar menggunakan user_guid (lebih konsisten dengan UUID)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_guid', 'guid');
    }
}
