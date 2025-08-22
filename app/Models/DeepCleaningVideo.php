<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeepCleaningVideo extends Model
{
    protected $fillable = [
        'deep_cleaning_id',
        'video',
        'type'
    ];

    public function deepCleaning()
    {
        return $this->belongsTo(DeepCleaning::class);
    }
}
