<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

// ✅ 1. Import Interface & Trait Audit
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class StockReagen extends Model implements AuditableContract
{
    // ✅ 2. Tambahkan Trait Auditing
    use Auditable;

    protected $table = 'stock_reagens';
    protected $primaryKey = 'guid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'guid',
        'noCatalog',
        'reagen_guid',
        'batch',
        'quantity',
        'expiredDate',
        'note',
        'stockUpdateDate',
        'organization_guid',
    ];

    // ✅ 3. Exclude field auto-generated agar tidak membanjiri audit trail
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

    // Relasi ke Reagen menggunakan GUID
    public function reagen()
    {
        return $this->belongsTo(Reagen::class, 'reagen_guid', 'guid');
    }

    // Relasi ke LogbookReagen
    public function logbookReagen()
    {
        return $this->belongsTo(LogbookReagen::class, 'noCatalog', 'noCatalog');
    }
}