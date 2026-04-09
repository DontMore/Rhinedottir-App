<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $primaryKey = 'guid';
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'guid',
        'name',
        'username',
        'email',
        'password',
        'role',
        'organization_guid',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * ✅ OVERRIDE: Tell Laravel to use 'username' for authentication
     */
    public function username()
    {
        return 'username';
    }

    /**
     * Boot method to auto-generate UUID for guid
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->guid)) {
                $model->guid = (string) Str::uuid();
            }
        });
    }

    // ✅ Opsional: Tambahkan relationships jika digunakan di Controller
    // public function reagenIn() { return $this->hasMany(ReagenIn::class, 'user_id', 'id'); }
    // public function logbook() { return $this->hasMany(LogbookReagen::class, 'user_id', 'id'); }
    // public function orders() { return $this->hasMany(Order::class, 'user_id', 'id'); }
}