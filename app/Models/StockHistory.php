<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

// ✅ 1. Import Interface & Trait Audit
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class StockHistory extends Model implements AuditableContract
{
    // ✅ 2. Tambahkan Trait Auditing
    use HasFactory, Auditable;

    protected $table = 'stock_histories';
    protected $primaryKey = 'guid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'guid',
        'noCatalog',
        'month',
        'year',
        'reagen_guid',
        'quantity',
        'quantity_in',
        'quantity_out',
        'quantity_actual',
        'status',
        'stock_opname',
        'catatan',
        'user_id',
    ];

    // ✅ 3. Exclude field auto-generated/immutable agar tidak membanjiri audit trail
    protected $auditExclude = ['guid'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->guid)) {
                $model->guid = (string) Str::uuid();
            }
        });
    }

    public function reagen()
    {
        return $this->belongsTo(Reagen::class, 'reagen_guid', 'guid');
    }
}