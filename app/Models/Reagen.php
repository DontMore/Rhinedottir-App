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
        'signal_word',
        'price',
        'buffer_stock',
        'reagent_form',
        'organization_guid',
        'group_guid',
        'category_guid',
        'storage_location_guid',
        'recommended_storage_location',
        'is_active', // ✅ TAMBAHKAN INI
    ];

    protected $casts = [
        'is_active' => 'boolean', // ✅ TAMBAHKAN INI
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

        // ✅ Hapus semua dokumen MSDS terkait saat reagen dihapus
        static::deleting(function ($model) {
            $model->reagenMsds()->each(function ($msds) {
                if (\Storage::disk('public')->exists($msds->file_path)) {
                    \Storage::disk('public')->delete($msds->file_path);
                }
                $msds->delete();
            });
        });
    }

    // ============================================
    // RELASI
    // ============================================

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_guid', 'guid');
    }

    public function group()
    {
        return $this->belongsTo(ReagenGroup::class, 'group_guid', 'guid');
    }

    public function category()
    {
        return $this->belongsTo(ReagenCategory::class, 'category_guid', 'guid');
    }

    public function storageLocation()
    {
        return $this->belongsTo(StorageLocation::class, 'storage_location_guid', 'guid');
    }

    public function reagenIn()
    {
        return $this->hasMany(ReagenIn::class, 'reagen_guid', 'guid');
    }

    public function logbookReagens()
    {
        return $this->hasMany(LogbookReagen::class, 'reagen_guid', 'guid');
    }

    public function stockReagen()
    {
        return $this->hasOne(StockReagen::class, 'reagen_guid', 'guid');
    }

    public function stockHistories()
    {
        return $this->hasMany(StockHistory::class, 'reagen_guid', 'guid');
    }

    public function stocks()
    {
        return $this->hasMany(StockReagen::class, 'reagen_guid', 'guid');
    }

    // ============================================
    // ✅ RELASI BARU: Multiple MSDS (tabel reagen_msds)
    // ============================================

    /**
     * Relasi ke ReagenMsds (One-to-Many)
     * Satu reagen bisa memiliki banyak dokumen MSDS dengan versi & tanggal revisi berbeda
     */
    public function reagenMsds()
    {
        return $this->hasMany(ReagenMsds::class, 'reagen_guid', 'guid');
    }

    /**
     * Ambil dokumen MSDS terbaru (yang ditandai is_latest = true)
     */
    public function latestReagenMsds()
    {
        return $this->hasOne(ReagenMsds::class, 'reagen_guid', 'guid')
                    ->where('is_latest', true);
    }

    /**
     * Helper: Cek apakah reagen sudah memiliki MSDS
     */
    public function getHasReagenMsdsAttribute(): bool
    {
        return $this->reagenMsds()->exists();
    }

    /**
     * Helper: URL untuk download MSDS terbaru
     */
    public function getLatestReagenMsdsUrlAttribute(): ?string
    {
        $latest = $this->latestReagenMsds;
        return $latest ? $latest->view_url : null;
    }

    // ============================================
    // LOGIC: Recommended Storage
    // ============================================

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