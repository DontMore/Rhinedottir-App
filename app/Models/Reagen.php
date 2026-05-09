<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

// ✅ 1. Import Interface & Trait Audit
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class Reagen extends Model implements AuditableContract
{
    // ✅ 2. Tambahkan Trait Auditing
    use HasFactory, Auditable;

    protected $primaryKey = 'guid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'guid',
        'noCatalog',
        'nameReagen',
        'merk',
        'packSize',
        'hazardOptions',
        'msds',
        'price',
        'buffer_stock',
        'organization_guid',
        'group_guid',
        'category_guid',
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

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_guid');
    }

    // ✅ PERBAIKAN PENTING:
    // Semua hasMany harus menunjuk ke foreign_key di tabel anak yaitu 'reagen_guid'
    public function reagenIn()
    {
        return $this->hasMany(ReagenIn::class, 'reagen_guid', 'guid');
    }

    public function logbookReagens()
    {
        return $this->hasMany(LogbookReagen::class, 'reagen_guid', 'guid');
    }

    public function stockReagen()
    {
        return $this->hasOne(StockReagen::class, 'reagen_guid', 'guid');
    }

    public function stockHistories()
    {
        return $this->hasMany(StockHistory::class, 'reagen_guid', 'guid');
    }

    public function stocks()
    {
        return $this->hasMany(StockReagen::class, 'reagen_guid', 'guid');
    }

    public function group()
    {
        return $this->belongsTo(ReagenGroup::class, 'group_guid', 'guid');
    }

    public function category()
    {
        return $this->belongsTo(ReagenCategory::class, 'category_guid', 'guid');
    }
}
