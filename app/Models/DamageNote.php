<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DamageNote extends Model
{
    use HasFactory;

    const LOW_DAMAGE = 'low';
    const MEDIUM_DAMAGE = 'medium';
    const HIGH_DAMAGE = 'high';

    const DAMAGE_TYPES_MAPPING = [
        self::LOW_DAMAGE => 'Слабке руйнування',
        self::MEDIUM_DAMAGE => 'Сильне руйнування',
        self::HIGH_DAMAGE => 'Повне руйнування'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'date', 'object_type_id', 'community_id', 'city', 'street', 'building_number', 'damage_type', 'restoration_cost'
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
    ];
}
