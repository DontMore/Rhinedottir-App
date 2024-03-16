<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockOpname extends Model
{
    use HasFactory;

    protected $table = 'stock_opnames'; // Replace 'stock_reagents' with the actual table name as needed.

    protected $primaryKey = 'id'; // The column used as the primary key.

    // Define fillable columns.
    protected $fillable = [
        'noCatalog',
        'month',
        'year',
        'user_id',
        'status',
    ];

        // Relationship with the NoKatalogReagen model (Foreign Key).
        public function reagen()
        {
            return $this->belongsTo(Reagen::class, 'noCatalog', 'noCatalog');
        }
    
        public function stockHistory()
        {
            return $this->belongsTo(StockHistory::class, 'noCatalog', 'noCatalog');
        }

        public function user()
        {
            return $this->belongsTo(User::class, 'user_id', 'id');
        }
}
