<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DamageVideo extends Model
{
    protected $fillable = ['damage_id', 'video'];

    public function damage()
    {
        return $this->belongsTo(Damage::class);
    }
}
