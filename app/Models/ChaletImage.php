<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChaletImage extends Model
{
    protected $fillable = ['chalet_id', 'image'];

    public function chalet()
    {
        return $this->belongsTo(Chalet::class);
    }
}
