<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailSetting extends Model
{
    protected $fillable = [
        'mail_username',
        'mail_password',
        'mail_from_address',
    ];

    public static function updateConfig()
    {
        $settings = self::first();
        if ($settings) {
            config([
                'mail.mailers.smtp.username' => $settings->mail_username,
                'mail.mailers.smtp.password' => $settings->mail_password,
                'mail.from.address' => $settings->mail_from_address,
            ]);
        }
    }
}
