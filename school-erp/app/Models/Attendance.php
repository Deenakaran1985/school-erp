<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 'school_class_id', 'section_id',
        'marked_by', 'attendance_date', 'status', 'remarks',
    ];

    protected function casts(): array
    {
        return ['attendance_date' => 'date'];
    }

    public function student() { return $this->belongsTo(Student::class); }
    public function markedBy() { return $this->belongsTo(User::class, 'marked_by'); }

    public function scopePresent($query)  { return $query->where('status', 'present'); }
    public function scopeAbsent($query)   { return $query->where('status', 'absent'); }

    public function scopeForDate($query, string $date)
    {
        return $query->where('attendance_date', $date);
    }

    // Static helper: get percentage for a student in date range
    public static function percentageFor(int $studentId, string $from, string $to): float
    {
        $total   = static::where('student_id', $studentId)
            ->whereBetween('attendance_date', [$from, $to])->count();
        $present = static::where('student_id', $studentId)
            ->whereBetween('attendance_date', [$from, $to])
            ->where('status', 'present')->count();
        return $total > 0 ? round(($present / $total) * 100, 2) : 0;
    }
}