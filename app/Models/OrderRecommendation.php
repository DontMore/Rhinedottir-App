<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class OrderRecommendation extends Model
{
    protected $fillable = ['organization_guid', 'recommendations', 'generated_at'];
    protected $casts = [
        'recommendations' => 'array',
        'generated_at' => 'datetime'
    ];
}