<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class GradeConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_year_id', 'min_percent', 'max_percent',
        'grade', 'grade_point', 'description',
    ];

    protected function casts(): array
    {
        return [
            'min_percent' => 'decimal:2',
            'max_percent' => 'decimal:2',
            'grade_point' => 'decimal:2',
        ];
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    // ── Key static method used by ExamResult & GradeService ──

    public static function resolve(float $percentage, int $yearId): ?self
    {
        return static::where('academic_year_id', $yearId)
            ->where('min_percent', '<=', $percentage)
            ->where('max_percent', '>=', $percentage)
            ->first();
    }
}