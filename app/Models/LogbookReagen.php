<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogbookReagen extends Model
{
    use HasFactory;

    protected $table = 'logbook_reagens';

    protected $fillable = [
        'noCatalog',
        'user_id',
        'batch',
        'quantity_taken',
        'note',
    ];

    // Relasi ke tabel 'reagens'
    public function reagen()
    {
        return $this->belongsTo(Reagen::class, 'catalog_number', 'noCatalog');
    }

    // Relasi ke tabel 'users'
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function stockReagen()
    {
        return $this->hasOne(StockReagen::class, 'noCatalog');
    }
}
