<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegularCleaningImage extends Model
{
    protected $fillable = ['regular_cleaning_id', 'type', 'image'];

    public function regularCleaning()
    {
        return $this->belongsTo(RegularCleaning::class);
    }
}
