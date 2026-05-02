<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

// ✅ 1. Import Interface & Trait Audit
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class EmailSetting extends Model implements AuditableContract
{
    // ✅ 2. Tambahkan Trait Auditing
    use Auditable;

    protected $primaryKey = 'guid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'guid',
        'mail_host',
        'mail_port',
        'mail_encryption',
        'mail_username',
        'mail_password',
        'mail_from_address',
    ];

    // ✅ 3. Exclude field sensitif agar tidak terekam di audit trail
    protected $auditExclude = ['mail_password', 'guid'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->guid)) {
                $model->guid = (string) Str::uuid();
            }
        });
    }

    public static function updateConfig()
    {
        $settings = self::first();
        if ($settings) {
            config([
                'mail.mailers.smtp.host' => $settings->mail_host,
                'mail.mailers.smtp.port' => $settings->mail_port,
                'mail.mailers.smtp.encryption' => $settings->mail_encryption,
                'mail.mailers.smtp.username' => $settings->mail_username,
                'mail.mailers.smtp.password' => $settings->mail_password,
                'mail.from.address' => $settings->mail_from_address,
            ]);
        }
    }
}