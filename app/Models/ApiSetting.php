<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class ApiSetting extends Model implements AuditableContract
{
    use Auditable;

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
