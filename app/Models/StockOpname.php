<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

// ✅ 1. Import Interface & Trait Audit
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class StockOpname extends Model implements AuditableContract
{
    // ✅ 2. Tambahkan Trait Auditing
    use HasFactory, Auditable;

    protected $table = 'stock_opnames';
    protected $primaryKey = 'guid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'guid',
        'reagen_guid',
        'month',
        'year',
        'user_id',
        'status',
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

    public function reagen()
    {
        return $this->belongsTo(Reagen::class, 'reagen_guid', 'guid');
    }

    public function stockHistory()
    {
        return $this->belongsTo(StockHistory::class, 'reagen_guid', 'reagen_guid');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'guid');
    }
}