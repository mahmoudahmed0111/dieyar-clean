<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PestControlVideo extends Model
{
    protected $fillable = ['pest_control_id', 'type', 'video'];

    public function pestControl()
    {
        return $this->belongsTo(PestControl::class);
    }
}
