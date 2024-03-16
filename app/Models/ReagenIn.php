<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReagenIn extends Model
{
    protected $table = 'reagens_in'; // Replace 'stock_reagents' with the actual table name as needed.

    protected $primaryKey = 'Id'; // The column used as the primary key.

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

    public function stockReagen()
    {
        return $this->belongsTo(StockReagen::class, 'noCatalog', 'noCatalog');
    }
}
