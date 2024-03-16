<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockReagen extends Model
{
    protected $table = 'stock_reagens'; // Replace 'stock_reagents' with the actual table name as needed.

    protected $primaryKey = 'stockId'; // The column used as the primary key.

    // Define fillable columns.
    protected $fillable = [
        'noCatalog',
        'batch',
        'quantity',
        'expiredDate',
        'note',
        'stockUpdateDate'
    ];

    // Relationship with the NoKatalogReagen model (Foreign Key).
    public function reagen()
    {
        return $this->belongsTo(Reagen::class, 'noCatalog', 'noCatalog');
    }

    public function logbookReagen()
    {
        return $this->belongsTo(LogbookReagen::class, 'noCatalog', 'noCatalog');
    }
}
