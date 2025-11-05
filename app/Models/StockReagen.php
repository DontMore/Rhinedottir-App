<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class StockReagen extends Model
{
    protected $table = 'stock_reagens'; // Replace 'stock_reagents' with the actual table name as needed.

    protected $primaryKey = 'guid'; // The column used as the primary key.
    public $incrementing = false;
    protected $keyType = 'string';

    // Define fillable columns.
    protected $fillable = [
        'guid',
        'reagen_guid',
        'batch',
        'quantity',
        'expiredDate',
        'note',
        'stockUpdateDate'
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
        return $this->belongsTo(Reagen::class, 'guid', 'guid');
    }

    public function logbookReagen()
    {
        return $this->belongsTo(LogbookReagen::class, 'guid', 'guid');
    }
}
