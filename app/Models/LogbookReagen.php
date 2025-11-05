<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LogbookReagen extends Model
{
    use HasFactory;

    protected $table = 'logbook_reagens';
    protected $primaryKey = 'guid';
    public $incrementing = false;
    protected $keyType = 'string';

    // Jika Anda ingin mengisi timestamp secara manual
    public $timestamps = true; // Atau bisa dibiarkan default

    // Specify the fields that are mass assignable
    protected $fillable = [
        'guid', 'reagen_guid', 'user_id', 'batch', 'quantity_taken', 'note', 'created_at'
    ];

    // Specify the fields that should be cast to dates
    protected $dates = ['created_at', 'updated_at'];

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
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function stockReagen()
    {
        return $this->hasOne(StockReagen::class, 'reagen_guid', 'reagen_guid');
    }
}
