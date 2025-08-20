<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Cleaner extends Authenticatable
{
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'password',
        'national_id',
        'address',
        'hire_date',
        'status',
        'image',
        'fcm_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'password' => 'hashed',
    ];

    // العلاقات
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}
