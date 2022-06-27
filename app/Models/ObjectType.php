<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ObjectCategory;

class ObjectType extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'object_category_id'
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

    public function objectCategory()
    {
        return $this->belongsTo(ObjectCategory::class);
    }
}
