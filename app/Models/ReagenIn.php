<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ReagenIn extends Model
{
    protected $table = 'reagens_in'; // Replace 'stock_reagents' with the actual table name as needed.

    protected $primaryKey = 'guid'; // The column used as the primary key.
    public $incrementing = false;
    protected $keyType = 'string';

    // Define fillable columns.
    protected $fillable = [
        'guid',
        'noCatalog',
        'reagen_guid',      // ✅ Tambahkan
        'batch',
        'quantity',
        'expiredDate',
        'note',
        'stockUpdateDate',
        'user_id',
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

    // ✅ Relasi ke Reagen menggunakan GUID
    public function reagen()
    {
        return $this->belongsTo(Reagen::class, 'reagen_guid', 'guid');
    }

    public function stockReagen()
    {
        return $this->belongsTo(StockReagen::class, 'guid', 'guid');
    }

    // ✅ Perbaiki relasi User (sebelumnya salah pakai guid,guid)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
