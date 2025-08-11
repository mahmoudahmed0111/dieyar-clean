<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ChaletImage;
use App\Models\ChaletVideo;

class Chalet extends Model
{
    protected $fillable = ['name', 'location', 'description', 'status', 'type'];

    public function images()
    {
        return $this->hasMany(ChaletImage::class);
    }

    public function videos()
    {
        return $this->hasMany(ChaletVideo::class);
    }
}
