<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;

// ✅ PERBAIKAN: Import trait dengan nama yang benar
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable; // ← Trait-nya bernama Auditable, bukan Auditing

class User extends Authenticatable implements AuditableContract
{
    // ✅ PERBAIKAN: Gunakan trait Auditable
    use HasApiTokens, HasFactory, Notifiable, Auditable;

    protected $primaryKey = 'guid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'guid',
        'name',
        'username',
        'email',
        'password',
        'role',
        'organization_guid',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    // ✅ Opsional: Exclude field sensitif dari audit
    protected $auditExclude = ['password', 'remember_token'];

    public function username()
    {
        return 'username';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    public function isActive(): bool
    {
        return $this->is_active === true;
    }

    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }

    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->guid)) {
                $model->guid = (string) Str::uuid();
            }
            if (!isset($model->is_active)) {
                $model->is_active = true;
            }
        });
    }
}