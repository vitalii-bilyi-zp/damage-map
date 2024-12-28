<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DamageNoteImage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['damage_note_id', 'file_name', 'hash_file_name'];

    public function damageNote()
    {
        return $this->belongsTo(DamageNote::class);
    }
}
