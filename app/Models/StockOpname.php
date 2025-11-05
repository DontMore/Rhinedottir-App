<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class StockOpname extends Model
{
    use HasFactory;

    protected $table = 'stock_opnames'; // Replace 'stock_reagents' with the actual table name as needed.

    protected $primaryKey = 'guid'; // The column used as the primary key.
    public $incrementing = false;
    protected $keyType = 'string';

    // Define fillable columns.
    protected $fillable = [
        'guid',
        'reagen_guid',
        'month',
        'year',
        'user_id',
        'status',
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

        // Relationship with the NoKatalogReagen model (Foreign Key).
        public function reagen()
        {
            return $this->belongsTo(Reagen::class, 'reagen_guid', 'guid');
        }

        public function stockHistory()
        {
            return $this->belongsTo(StockHistory::class, 'reagen_guid', 'reagen_guid');
        }

        public function user()
        {
            return $this->belongsTo(User::class, 'user_id', 'guid');
        }
}
