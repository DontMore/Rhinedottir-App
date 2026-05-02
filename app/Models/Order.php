<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

// ✅ 1. Import Interface & Trait Audit
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class Order extends Model implements AuditableContract
{
    // ✅ 2. Tambahkan Trait Auditing
    use HasFactory, Auditable;

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
        'userId',
        'user_guid',
        'organization_guid',
        'status',
    ];

    // ✅ 3. Exclude field auto-generated/immutable agar tidak membanjiri log audit
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

    // ✅ Relasi ke User menggunakan user_guid (konsisten dengan UUID)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_guid', 'guid');
    }
}