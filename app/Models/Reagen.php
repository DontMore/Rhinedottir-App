<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Reagen extends Model
{
    use HasFactory;

    protected $primaryKey = 'guid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'guid',
        'noCatalog',
        'nameReagen',
        'merk',
        'packSize',
        'hazardOptions',
        'msds',
        'price'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->guid)) {
                $model->guid = (string) Str::uuid();
            }
        });
    }

    // Relasi dengan model StockReagen
    public function reagenIn()
    {
        return $this->hasMany(ReagenIn::class, 'guid', 'guid');
    }

    public function logbookReagens()
    {
        return $this->hasMany(LogbookReagen::class, 'guid', 'guid');
    }

    public function stockReagen()
    {
        return $this->hasOne(StockReagen::class, 'guid', 'guid');
    }

    public function stockHistories()
    {
        return $this->hasMany(StockHistory::class, 'guid', 'guid');
    }

    public function stocks()
    {
        return $this->hasMany(StockReagen::class, 'guid', 'guid');
    }
}
