<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reagen extends Model
{
    use HasFactory;

    protected $primaryKey = 'noCatalog';

    public $incrementing = 'noCatalog';

    protected $keyType = 'string';

    protected $fillable = [
        'noCatalog',
        'nameReagen',
        'merk',
        'packSize',
        'hazardOptions',
        'msds',
        'price'
    ];

    // Relasi dengan model StockReagen
    public function reagenIn()
    {
        return $this->hasMany(ReagenIn::class, 'noCatalog', 'noCatalog');
    }

    public function logbookReagens()
    {
        return $this->hasMany(LogbookReagen::class, 'noCatalog');
    }

    public function stockReagen()
    {
        return $this->hasOne(StockReagen::class, 'noCatalog', 'noCatalog');
    }

    public function stockHistories()
    {
        return $this->hasMany(StockHistory::class, 'noCatalog', 'noCatalog');
    }

    public function stocks()
    {
        return $this->hasMany(StockReagen::class, 'noCatalog', 'noCatalog');
    }
}
