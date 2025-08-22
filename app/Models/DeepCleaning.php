<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\DeepCleaningImage;
use App\Models\DeepCleaningVideo;
use App\Models\Inventory;

class DeepCleaning extends Model
{
    protected $fillable = [
        'cleaner_id',
        'chalet_id',
        'date',
        'cleaning_cost',
        'cleaning_type',
        'status'
    ];

    protected $casts = [
        'date' => 'date',
        'cleaning_cost' => 'decimal:2',
    ];

    public function images()
    {
        return $this->hasMany(DeepCleaningImage::class);
    }

    public function videos()
    {
        return $this->hasMany(DeepCleaningVideo::class);
    }

    public function inventory()
    {
        return $this->belongsToMany(Inventory::class, 'deep_cleaning_inventory')
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
