<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class Reagen extends Model implements AuditableContract
{
    use HasFactory, Auditable;

    protected $primaryKey = 'guid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'guid',
        'noCatalog',
        'nameReagen',
        'merk',
        'packSize',
        'hazardOptions',
        'msds',
        'price',
        'buffer_stock',
        'reagent_form', // ✅ Tambahkan ini
        'organization_guid',
        'group_guid',
        'category_guid',
        'storage_location_guid',
        'recommended_storage_location',
    ];

    protected $auditExclude = ['guid'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->guid)) {
                $model->guid = (string) Str::uuid();
            }
        });
    }

    // ✅ Relasi ke Organization
    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_guid', 'guid');
    }

    // ✅ Relasi ke ReagenGroup (NEW)
    public function group()
    {
        return $this->belongsTo(ReagenGroup::class, 'group_guid', 'guid');
    }

    // ✅ Relasi ke ReagenCategory (NEW)
    public function category()
    {
        return $this->belongsTo(ReagenCategory::class, 'category_guid', 'guid');
    }

    // ✅ Relasi ke StorageLocation (NEW)
    public function storageLocation()
    {
        return $this->belongsTo(StorageLocation::class, 'storage_location_guid', 'guid');
    }

    // ✅ Relasi ke ReagenIn (batch stok masuk)
    public function reagenIn()
    {
        return $this->hasMany(ReagenIn::class, 'reagen_guid', 'guid');
    }

    // ✅ Relasi ke LogbookReagen
    public function logbookReagens()
    {
        return $this->hasMany(LogbookReagen::class, 'reagen_guid', 'guid');
    }

    // ✅ Relasi ke StockReagen (total stok saat ini)
    public function stockReagen()
    {
        return $this->hasOne(StockReagen::class, 'reagen_guid', 'guid');
    }

    // ✅ Relasi ke StockHistory
    public function stockHistories()
    {
        return $this->hasMany(StockHistory::class, 'reagen_guid', 'guid');
    }

    // ✅ Alias untuk stockReagen
    public function stocks()
    {
        return $this->hasMany(StockReagen::class, 'reagen_guid', 'guid');
    }

    /**
     * Generate recommended storage location based on hazards
     */
    public static function getRecommendedStorage($hazards, $organization_guid)
    {
        $recommendations = [];

        $hazardMap = [
            'Flammable' => 'flammable',
            'Corrosive' => 'corrosive',
            'Toxic' => 'toxic',
            'Explosive' => 'explosive',
            'Oxidising' => 'oxidizer',
            'Carcinogen' => 'carcinogen',
            'Environment' => 'hazardous',
            'Irritant' => 'general',
        ];

        foreach ($hazards as $hazard) {
            if (isset($hazardMap[$hazard])) {
                $recommendations[] = $hazardMap[$hazard];
            }
        }

        $priority = [
            'explosive' => 1,
            'flammable' => 2,
            'toxic' => 3,
            'corrosive' => 4,
            'oxidizer' => 5,
            'carcinogen' => 6,
            'hazardous' => 7,
            'general' => 8,
        ];

        if (empty($recommendations)) {
            return 'General Storage - Rak A (Non-Hazardous)';
        }

        usort($recommendations, function ($a, $b) use ($priority) {
            return ($priority[$a] ?? 99) <=> ($priority[$b] ?? 99);
        });

        $primaryStorage = $recommendations[0];

        $storageNames = [
            'explosive' => 'Explosive Storage - Ruang Khusus (Class I)',
            'flammable' => 'Flammable Cabinet - Lemari Tahan Api (Class II)',
            'toxic' => 'Toxic Storage - Lemari Terkunci (Class III)',
            'corrosive' => 'Corrosive Cabinet - Rak Tahan Korosi (Class IV)',
            'oxidizer' => 'Oxidizer Storage - Rak Terpisah (Class V)',
            'carcinogen' => 'Carcinogen Storage - Area Terkontrol (Class VI)',
            'hazardous' => 'Hazardous Waste Area - Area Limbah B3',
            'general' => 'General Storage - Rak A (Non-Hazardous)',
        ];

        return $storageNames[$primaryStorage] ?? 'General Storage';
    }
}
