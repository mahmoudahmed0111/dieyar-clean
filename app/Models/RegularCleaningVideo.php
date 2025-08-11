<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegularCleaningVideo extends Model
{
    protected $fillable = ['regular_cleaning_id', 'type', 'video'];

    public function regularCleaning()
    {
        return $this->belongsTo(RegularCleaning::class);
    }
}
