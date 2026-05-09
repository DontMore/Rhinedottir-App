<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class Order extends Model implements AuditableContract
{
    use Auditable;

    protected $primaryKey = 'guid'; // ✅ Gunakan GUID sebagai primary key
    public $incrementing = false;   // ✅ Non-aktifkan auto-increment
    protected $keyType = 'string';  // ✅ Tipe key adalah string

    protected $fillable = [
        'guid',
        'noCatalog',
        'nameReagen',
        'merk',
        'packSize',
        'quantity',
        'status',
        'userId',           // Legacy compatibility
        'user_guid',        // ✅ Baru
        'organization_guid',// ✅ Baru
    ];

    protected $casts = [
        'quantity' => 'integer',
        'status' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->guid)) {
                $model->guid = (string) Str::uuid(); // ✅ Auto-generate GUID
            }
        });
    }

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_guid', 'guid');
    }
}