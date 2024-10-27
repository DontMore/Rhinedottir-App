<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogbookReagen extends Model
{
    use HasFactory;

    protected $table = 'logbook_reagens';

    // Jika Anda ingin mengisi timestamp secara manual
    public $timestamps = true; // Atau bisa dibiarkan default

    // Specify the fields that are mass assignable
    protected $fillable = [
        'noCatalog', 'user_id', 'batch', 'quantity_taken', 'note', 'created_at'
    ];

    // Specify the fields that should be cast to dates
    protected $dates = ['created_at', 'updated_at'];

    // Relasi ke tabel 'reagens'
    public function reagen()
    {
        return $this->belongsTo(Reagen::class, 'noCatalog', 'noCatalog');
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
