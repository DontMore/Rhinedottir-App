<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class ApiSetting extends Model implements AuditableContract
{
    use Auditable;

    protected $table = 'api_settings';
    protected $primaryKey = 'guid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'guid',
        'gas_web_app_url',
        'is_active',
        'api_token',
        'push_is_active',
        'push_interval',
        'last_push_at',
        'selected_tables',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'push_is_active' => 'boolean',
        'last_push_at' => 'datetime',
        'selected_tables' => 'array', // Penting agar checkbox tersimpan sebagai JSON
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->guid)) {
                $model->guid = (string) Str::uuid();
            }
            if (empty($model->api_token)) {
                $model->api_token = Str::random(60);
            }
        });
    }
}