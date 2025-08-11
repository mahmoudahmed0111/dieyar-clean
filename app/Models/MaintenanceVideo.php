<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceVideo extends Model
{
    protected $fillable = ['maintenance_id', 'type', 'video'];

    public function maintenance()
    {
        return $this->belongsTo(Maintenance::class);
    }
}
