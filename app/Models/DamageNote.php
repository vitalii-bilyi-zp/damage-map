<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DamageNote extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'object_type_id', 'community_id', 'city', 'street', 'building_number', 'damage_type', 'restoration_cost'
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

    ];
}
