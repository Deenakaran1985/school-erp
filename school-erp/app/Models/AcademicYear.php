<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class AcademicYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'start_date', 'end_date', 'is_current',
    ];

    protected function casts(): array
    {
        return [
            'start_date'  => 'date',
            'end_date'    => 'date',
            'is_current'  => 'boolean',
        ];
    }

    // ── Relationships ──────────────────────────────────

    public function schoolClasses()
    {
        return $this->hasMany(SchoolClass::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function exams()
    {
        return $this->hasMany(Exam::class);
    }

    public function gradeConfigs()
    {
        return $this->hasMany(GradeConfig::class);
    }

    // ── Scopes ─────────────────────────────────────────

    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    // ── Static Helper ──────────────────────────────────

    public static function current(): self
    {
        return static::where('is_current', true)->firstOrFail();
    }
}