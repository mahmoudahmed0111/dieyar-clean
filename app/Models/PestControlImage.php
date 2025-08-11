<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PestControlImage extends Model
{
    protected $fillable = ['pest_control_id', 'type', 'image'];

    public function pestControl()
    {
        return $this->belongsTo(PestControl::class);
    }
}
