<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

// ✅ 1. Import Interface & Trait Audit
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class LogbookReagen extends Model implements AuditableContract
{
    // ✅ 2. Tambahkan Trait Auditing
    use HasFactory, Auditable;

    protected $table = 'logbook_reagens';
    protected $primaryKey = 'guid';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;

    protected $fillable = [
        'guid',
        'noCatalog',
        'reagen_guid',
        'user_id',
        'organization_guid',
        'batch',
        'quantity_taken',
        'note',
        'created_at'
    ];

    protected $dates = ['created_at', 'updated_at'];

    // ✅ 3. Exclude field auto-generate/immutable agar tidak membanjiri audit trail
    protected $auditExclude = ['guid', 'created_at', 'organization_guid'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->guid)) {
                $model->guid = (string) Str::uuid();
            }
        });
    }

    // Relasi ke tabel 'reagens'
    public function reagen()
    {
        return $this->belongsTo(Reagen::class, 'reagen_guid', 'guid');
    }

    // Relasi ke tabel 'users'
    public function user()
    {
        // ⚠️ Perhatikan: Karena User model pakai primary key 'guid', 
        // pastikan kolom user_id di tabel ini menyimpan GUID user, bukan integer ID.
        // Jika iya, ubah parameter terakhir menjadi 'guid':
        // return $this->belongsTo(User::class, 'user_id', 'guid');
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}