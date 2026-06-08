<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class SchoolClass extends Model
{
    use HasFactory;

    protected $table = 'school_classes';

    protected $fillable = [
        'name', 'display_name', 'level',
        'academic_year_id', 'sort_order', 'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    // ── Relationships ──────────────────────────────────

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class)->orderBy('sort_order');
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function exams()
    {
        return $this->hasMany(Exam::class);
    }

    public function feeStructures()
    {
        return $this->hasMany(FeeStructure::class);
    }

    // ── Scopes ─────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    // ── Helpers ────────────────────────────────────────

    public function getStudentCountAttribute(): int
    {
        return $this->students()->where('status', 'active')->count();
    }
}
