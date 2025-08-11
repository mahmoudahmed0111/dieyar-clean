<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeepCleaningImage extends Model
{
    protected $fillable = ['deep_cleaning_id', 'type', 'image'];

    public function deepCleaning()
    {
        return $this->belongsTo(DeepCleaning::class);
    }
}
