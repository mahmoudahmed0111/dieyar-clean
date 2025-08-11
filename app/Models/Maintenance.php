<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\MaintenanceImage;
use App\Models\MaintenanceVideo;

class Maintenance extends Model
{
    protected $table = 'maintenance';
    protected $fillable = [
        'cleaner_id', 'chalet_id', 'description', 'status', 'requested_at', 'completed_at'
    ];

    public function images()
    {
        return $this->hasMany(MaintenanceImage::class);
    }

    public function videos()
    {
        return $this->hasMany(MaintenanceVideo::class);
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
