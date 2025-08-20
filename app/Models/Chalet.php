<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ChaletImage;
use App\Models\ChaletVideo;

class Chalet extends Model
{
    protected $fillable = [
        'name',
        'code',
        'pass_code',
        'floor',
        'building',
        'location',
        'description',
        'status',
        'type',
        'is_cleaned',
        'is_booked'
    ];

    public function images()
    {
        return $this->hasMany(ChaletImage::class);
    }

    public function videos()
    {
        return $this->hasMany(ChaletVideo::class);
    }

    
}
