<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ReagenIn extends Model
{
    protected $table = 'reagens_in'; // Replace 'stock_reagents' with the actual table name as needed.

    protected $primaryKey = 'guid'; // The column used as the primary key.
    public $incrementing = false;
    protected $keyType = 'string';

    // Define fillable columns.
    protected $fillable = [
        'guid',
        'batch',
        'quantity',
        'expiredDate',
        'note',
        'stockUpdateDate',
        'user_id',
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

    public function stockReagen()
    {
        return $this->belongsTo(StockReagen::class, 'guid', 'guid');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'guid');
    }
}
