<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $table = 'inventory';
    protected $fillable = ['name', 'price', 'quantity', 'image'];

    
    public function deepCleanings()
    {
        return $this->belongsToMany(DeepCleaning::class, 'deep_cleaning_inventory')
            ->withPivot('quantity')
            ->withTimestamps();
    }
    public function regularCleanings()
    {
        return $this->belongsToMany(RegularCleaning::class, 'regular_cleaning_inventory')
            ->withPivot('quantity')
            ->withTimestamps();
    }
}
