<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_year_id', 'exam_type_id', 'school_class_id', 'subject_id',
        'created_by', 'exam_name', 'exam_date', 'start_time',
        'duration_minutes', 'max_marks', 'pass_marks',
        'hall_no', 'status', 'published_at', 'instructions',
    ];

    protected function casts(): array
    {
        return [
            'exam_date'    => 'date',
            'published_at' => 'datetime',
        ];
    }

    // ── Relationships ──────────────────────────────────

    public function academicYear() { return $this->belongsTo(AcademicYear::class); }
    public function examType()     { return $this->belongsTo(ExamType::class); }
    public function schoolClass()  { return $this->belongsTo(SchoolClass::class); }
    public function subject()      { return $this->belongsTo(Subject::class); }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function results()
    {
        return $this->hasMany(ExamResult::class);
    }

    // ── Scopes ─────────────────────────────────────────

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeForClass($query, int $classId)
    {
        return $query->where('school_class_id', $classId);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('exam_date', '>=', now())
                     ->where('status', 'scheduled');
    }

    // ── Helpers ────────────────────────────────────────

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function getClassAverageAttribute(): float
    {
        return $this->results()->whereNotNull('marks_obtained')->avg('marks_obtained') ?? 0;
    }

    public function getPassCountAttribute(): int
    {
        return $this->results()
            ->where('marks_obtained', '>=', $this->pass_marks)
            ->count();
    }

    public function scopeForToday($query)
{
    return $query->whereDate('exam_date', today())
                 ->whereIn('status', ['scheduled', 'published']);
}
}
