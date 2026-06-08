<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class SchoolNotification extends Model
{
    use HasFactory;

    protected $table = 'school_notifications';

    protected $fillable = [
        'sent_by', 'title', 'body', 'type',
        'target_role', 'target_class_id', 'target_user_id',
        'data', 'sent_count', 'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'data'    => 'array',
            'sent_at' => 'datetime',
        ];
    }

    public function sentBy()      { return $this->belongsTo(User::class, 'sent_by'); }
    public function targetClass() { return $this->belongsTo(SchoolClass::class, 'target_class_id'); }
    public function targetUser()  { return $this->belongsTo(User::class, 'target_user_id'); }
    public function reads()        { return $this->hasMany(NotificationRead::class, 'notification_id'); }

    public function scopeForUser($query, int $userId, string $role)
    {
        return $query->where(function($q) use ($userId, $role) {
            $q->where('target_user_id', $userId)
              ->orWhere('target_role', $role)
              ->orWhere('target_role', 'all');
        });
    }
}
