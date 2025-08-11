<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChaletVideo extends Model
{
    protected $fillable = ['chalet_id', 'video'];

    public function chalet()
    {
        return $this->belongsTo(Chalet::class);
    }
}
