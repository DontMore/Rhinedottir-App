<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockHistory extends Model
{
    protected $table = 'stock_histories'; // Replace 'stock_reagents' with the actual table name as needed.

    protected $primaryKey = 'Id'; // The column used as the primary key.

    // Define fillable columns.
    protected $fillable = [
        'month',
        'year',
        'noCatalog',
        'quantity',
        'quantity_in',
        'quantity_out',
        'quantity_actual',
        'status',
        'stock_opname',
        'catatan',
        'user_id'
    ];

    // Relationship with the NoKatalogReagen model (Foreign Key).
    public function reagen()
    {
        return $this->belongsTo(Reagen::class, 'noCatalog', 'noCatalog');
    }
}
