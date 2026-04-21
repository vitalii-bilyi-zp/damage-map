<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\DamageNoteRequest;
use App\Models\DamageNoteImage;
use App\Models\ObjectType;
use App\Models\Community;
use App\Models\RepairType;

class DamageNote extends Model
{
    use HasFactory;

    const LOW_DAMAGE = 'low';
    const MEDIUM_DAMAGE = 'medium';
    const HIGH_DAMAGE = 'high';

    const DAMAGE_TYPES_MAPPING = [
        self::LOW_DAMAGE => 'Легке',
        self::MEDIUM_DAMAGE => 'Середнє',
        self::HIGH_DAMAGE => 'Тяжке'
    ];

    const HERITAGE_NONE     = 'none';
    const HERITAGE_LOCAL    = 'local';
    const HERITAGE_REGIONAL = 'regional';
    const HERITAGE_NATIONAL = 'national';
    const HERITAGE_WORLD    = 'world';

    const HERITAGE_STATUSES_MAPPING = [
        self::HERITAGE_NONE     => 'Без статусу',
        self::HERITAGE_LOCAL    => 'Місцеве значення',
        self::HERITAGE_REGIONAL => 'Регіональне значення',
        self::HERITAGE_NATIONAL => 'Національне значення',
        self::HERITAGE_WORLD    => 'Світове значення',
    ];

    /**
     * Допустимі коди типів ремонту для кожного рівня спадщини.
     * null означає «всі типи дозволені».
     */
    const HERITAGE_ALLOWED_REPAIR_CODES = [
        self::HERITAGE_NONE     => null,
        self::HERITAGE_LOCAL    => null,
        self::HERITAGE_REGIONAL => ['current_repair', 'capital_repair', 'restoration', 'conservation'],
        self::HERITAGE_NATIONAL => ['restoration', 'conservation'],
        self::HERITAGE_WORLD    => ['restoration'],
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'date',
        'object_type_id',
        'community_id',
        'city',
        'street',
        'building_number',
        'floors',
        'area',
        'damage_type',
        'repair_type_id',
        'restoration_cost',
        'predicted_restoration_cost',
        'comment',
        'heritage_status'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [

    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'date' => 'date',
        'area' => 'decimal:2',
        'restoration_cost' => 'decimal:2',
    ];

    public function damageNoteRequest()
    {
        return $this->hasOne(DamageNoteRequest::class);
    }

    public function damageNoteImages()
    {
        return $this->hasMany(DamageNoteImage::class);
    }

    public function objectType()
    {
        return $this->belongsTo(ObjectType::class);
    }

    public function community()
    {
        return $this->belongsTo(Community::class);
    }

    public function repairType()
    {
        return $this->belongsTo(RepairType::class);
    }
}
