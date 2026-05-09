<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class ReagenGroup extends Model implements AuditableContract
{
    use Auditable;
    protected $primaryKey = 'guid';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['guid', 'name', 'organization_guid'];

    protected static function boot()
    {
        parent::boot();
        static::creating(fn($model) => $model->guid = (string) Str::uuid());
    }

    public function reagens()
    {
        return $this->hasMany(Reagen::class, 'group_guid', 'guid');
    }
}