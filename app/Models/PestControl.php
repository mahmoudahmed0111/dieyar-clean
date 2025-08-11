<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\PestControlImage;
use App\Models\PestControlVideo;

class PestControl extends Model
{
    protected $fillable = [
        'chalet_id', 'cleaner_id', 'date', 'description', 'status', 'notes'
    ];

    public function images()
    {
        return $this->hasMany(PestControlImage::class);
    }

    public function videos()
    {
        return $this->hasMany(PestControlVideo::class);
    }

    public function cleaner()
    {
        return $this->belongsTo(Cleaner::class);
    }

    public function chalet()
    {
        return $this->belongsTo(Chalet::class);
    }
}
