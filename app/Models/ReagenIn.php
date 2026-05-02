<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

// ✅ PERBAIKAN: Import trait & interface dengan benar
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable; // ← Trait-nya bernama Auditable

class ReagenIn extends Model implements AuditableContract
{
    // ✅ PERBAIKAN: Gunakan trait Auditable
    use HasFactory, Auditable;

    protected $table = 'reagens_in';
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
        'user_id',
        'organization_guid',
    ];

    // ✅ Opsional: Exclude field yang tidak perlu di-audit
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

    public function stockReagen()
    {
        return $this->belongsTo(StockReagen::class, 'guid', 'guid');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}