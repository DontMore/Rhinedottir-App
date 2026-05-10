<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class StorageLocation extends Model implements AuditableContract
{
    use Auditable;
    protected $primaryKey = 'guid';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'guid', 'code', 'name', 'state_type', 'allowed_hazards',
        'capacity', 'description', 'organization_guid'
    ];
    protected $casts = [
        'allowed_hazards' => 'array',
        'capacity' => 'integer'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(fn($model) => $model->guid = (string) Str::uuid());
    }

    public function reagenIn()
    {
        return $this->hasMany(ReagenIn::class, 'storage_location_guid', 'guid');
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_guid');
    }
}