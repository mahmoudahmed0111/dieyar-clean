<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'cleaner_id',
        'title',
        'body',
        'type',
        'data',
        'read_at',
        'fcm_token',
        'sent_at'
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    // العلاقات
    public function cleaner()
    {
        return $this->belongsTo(Cleaner::class);
    }

    // Scope للإشعارات غير المقروءة
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    // Scope للإشعارات المقروءة
    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    // تحديد الإشعار كمقروء
    public function markAsRead()
    {
        $this->update(['read_at' => now()]);
    }

    // تحديد الإشعار كمرسل
    public function markAsSent()
    {
        $this->update(['sent_at' => now()]);
    }
}

