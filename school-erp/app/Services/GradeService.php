<?php
namespace App\Services;

use App\Models\AcademicYear;
use App\Models\ExamResult;
use App\Models\GradeConfig;
use App\Models\Student;
use App\Models\StudentPromotion;
use App\Models\SubjectFinalGrade;
use Illuminate\Support\Collection;

class GradeService
{
    // ── Resolve grade from percentage ──────────────────────
    public function resolveGrade(float $percentage, int $yearId): array
    {
        $config = GradeConfig::resolve($percentage, $yearId);

        if (!$config) {
            return ['grade' => 'F', 'grade_point' => 0.00, 'description' => 'Fail'];
        }

        return [
            'grade'       => $config->grade,
            'grade_point' => (float) $config->grade_point,
            'description' => $config->description,
        ];
    }

    // ── Calculate CGPA for a student in an academic year ───
    public function calculateCGPA(Student $student, AcademicYear $year): float
    {
        $results = ExamResult::with(['exam.examType', 'exam.subject'])
            ->whereHas('exam', fn($q) =>
                $q->where('academic_year_id', $year->id)
                  ->where('school_class_id', $student->school_class_id)
            )
            ->where('student_id', $student->id)
            ->where('is_absent', false)
            ->whereNotNull('marks_obtained')
            ->get();

        if ($results->isEmpty()) return 0.00;

        // Group by subject, then compute weighted final % per subject
        $bySubject = $results->groupBy('exam.subject_id');
        $totalGP   = 0;
        $subCount  = 0;

        foreach ($bySubject as $subjectId => $subjectResults) {
            $weightedTotal = 0;

            foreach ($subjectResults as $result) {
                $weight        = (float) $result->exam->examType->weightage_percent / 100;
                $pct           = (float) $result->percentage;
                $weightedTotal += $pct * $weight;
            }

            $gradeData  = $this->resolveGrade($weightedTotal, $year->id);
            $totalGP   += $gradeData['grade_point'];
            $subCount++;
        }

        return $subCount > 0 ? round($totalGP / $subCount, 2) : 0.00;
    }

    // ── Check if student passes for promotion ──────────────
    public function checkPromotion(Student $student, AcademicYear $year): array
    {
        $cgpa  = $this->calculateCGPA($student, $year);
        $grade = $this->resolveGrade($cgpa * 10, $year->id);

        // Count subject failures
        $failCount = ExamResult::whereHas('exam', fn($q) =>
            $q->where('academic_year_id', $year->id)
              ->where('school_class_id', $student->school_class_id)
              ->whereHas('examType', fn($q) =>
                $q->where('counts_for_promotion', true))
        )
        ->where('student_id', $student->id)
        ->where('grade', 'F')
        ->count();

        // Fail in core subject (Language or Maths) → detained
        $coreFailure = ExamResult::whereHas('exam.subject', fn($q) =>
            $q->where('is_core', true)
        )->where('student_id', $student->id)
         ->where('grade', 'F')
         ->whereHas('exam', fn($q) =>
            $q->where('academic_year_id', $year->id))
         ->exists();

        $passed = $cgpa >= 4.0 && $failCount <= 2 && !$coreFailure;

        return [
            'cgpa'         => $cgpa,
            'grade'        => $grade['grade'],
            'fail_count'   => $failCount,
            'core_failure' => $coreFailure,
            'passed'       => $passed,
            'status'       => $passed ? 'promoted' : 'detained',
        ];
    }

    // ── Compute class rank for an exam ─────────────────────
    public function computeRanks(int $examId): void
    {
        $results = ExamResult::where('exam_id', $examId)
            ->where('is_absent', false)
            ->whereNotNull('marks_obtained')
            ->orderByDesc('marks_obtained')
            ->get();

        $rank = 1;
        foreach ($results as $result) {
            $result->update(['rank' => $rank++]);
        }
    }
}