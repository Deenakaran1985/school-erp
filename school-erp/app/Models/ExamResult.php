<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class ExamResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id', 'student_id', 'entered_by',
        'marks_obtained', 'percentage', 'grade', 'grade_point',
        'is_absent', 'grace_applied', 'grace_marks',
        'rank', 'remarks', 'notified_at',
    ];

    protected function casts(): array
    {
        return [
            'marks_obtained' => 'decimal:2',
            'percentage'     => 'decimal:2',
            'grade_point'    => 'decimal:2',
            'is_absent'      => 'boolean',
            'grace_applied'  => 'boolean',
            'notified_at'    => 'datetime',
        ];
    }

    public function exam()    { return $this->belongsTo(Exam::class); }
    public function student() { return $this->belongsTo(Student::class); }
    public function enteredBy() { return $this->belongsTo(User::class, 'entered_by'); }

    public function scopePending($query)
    {
        return $query->whereNull('notified_at');
    }

    public function scopePassed($query)
    {
        return $query->where('grade', '!=', 'F')->where('is_absent', false);
    }

    public function getIsPassAttribute(): bool
    {
        return !$this->is_absent
            && $this->marks_obtained !== null
            && $this->marks_obtained >= $this->exam->pass_marks;
    }

    public function getStatusLabelAttribute(): string
    {
        if ($this->is_absent) return 'Absent';
        return $this->is_pass ? 'Pass' : 'Fail';
    }
}
