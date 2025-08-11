<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\DamageImage;
use App\Models\DamageVideo;

class Damage extends Model
{
    protected $fillable = [
        'cleaner_id', 'chalet_id', 'description', 'price', 'reported_at', 'status'
    ];

    public function images()
    {
        return $this->hasMany(DamageImage::class);
    }

    public function videos()
    {
        return $this->hasMany(DamageVideo::class);
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
