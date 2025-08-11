<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DamageImage extends Model
{
    protected $fillable = ['damage_id', 'image'];

    public function damage()
    {
        return $this->belongsTo(Damage::class);
    }
}
