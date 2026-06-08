<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Homework extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_class_id', 'section_id', 'subject_id', 'assigned_by',
        'title', 'description', 'attachment',
        'assigned_date', 'due_date', 'status',
    ];

    protected function casts(): array
    {
        return [
            'assigned_date' => 'date',
            'due_date'      => 'date',
        ];
    }

    public function schoolClass() { return $this->belongsTo(SchoolClass::class); }
    public function section()     { return $this->belongsTo(Section::class); }
    public function subject()     { return $this->belongsTo(Subject::class); }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function submissions()
    {
        return $this->hasMany(HomeworkSubmission::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')->where('due_date', '>=', now());
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date->isPast() && $this->status === 'active';
    }
}