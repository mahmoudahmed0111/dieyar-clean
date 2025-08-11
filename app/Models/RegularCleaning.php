<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\RegularCleaningImage;
use App\Models\RegularCleaningVideo;
use App\Models\Inventory;

class RegularCleaning extends Model
{
    protected $fillable = [
        'cleaner_id', 'chalet_id', 'date', 'price', 'notes'
    ];

    public function images()
    {
        return $this->hasMany(RegularCleaningImage::class);
    }

    public function videos()
    {
        return $this->hasMany(RegularCleaningVideo::class);
    }

    public function inventory()
    {
        return $this->belongsToMany(Inventory::class, 'regular_cleaning_inventory')
            ->withPivot('quantity')
            ->withTimestamps();
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
