<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\Student;
use Illuminate\Http\Request;

class ExamApiController extends Controller
{
    // GET /api/exams  — list exams for student's class
    public function index(Request $request)
    {
        $user   = $request->user();
        $role   = $user->getRoleNames()->first();
        $year   = AcademicYear::current();

        $student = $this->resolveStudent($user, $role, $request->student_id);

        if (!$student) {
            return response()->json(['success' => false, 'message' => 'No student found.'], 404);
        }

        $exams = Exam::with(['examType', 'subject'])
            ->where('academic_year_id', $year->id)
            ->where('school_class_id', $student->school_class_id)
            ->where('status', 'published')
            ->orderByDesc('exam_date')
            ->get();

        return response()->json([
            'success' => true,
            'student' => ['name' => $student->name, 'class' => $student->schoolClass?->name],
            'data'    => $exams->map(fn($e) => [
                'id'          => $e->id,
                'exam_name'   => $e->exam_name,
                'exam_type'   => $e->examType->name,
                'subject'     => $e->subject->name,
                'exam_date'   => $e->exam_date->format('d M Y'),
                'max_marks'   => $e->max_marks,
                'pass_marks'  => $e->pass_marks,
                'hall_no'     => $e->hall_no,
                'start_time'  => $e->start_time,
            ]),
        ]);
    }

    // GET /api/results  — exam results for student
    public function results(Request $request)
    {
        $user   = $request->user();
        $role   = $user->getRoleNames()->first();
        $year   = AcademicYear::current();

        $student = $this->resolveStudent($user, $role, $request->student_id);

        if (!$student) {
            return response()->json(['success' => false, 'message' => 'No student found.'], 404);
        }

        $results = ExamResult::with(['exam.examType', 'exam.subject'])
            ->where('student_id', $student->id)
            ->whereHas('exam', fn($q) =>
                $q->where('academic_year_id', $year->id)->where('status', 'published')
            )
            ->orderByDesc('created_at')
            ->get();

        // Group by exam type
        $grouped = $results->groupBy(fn($r) => $r->exam->examType->name);

        $formatted = $grouped->map(function ($group, $examType) {
            $totalMarks    = $group->sum('marks_obtained');
            $totalMax      = $group->sum(fn($r) => $r->exam->max_marks);
            $percentage    = $totalMax > 0 ? round(($totalMarks / $totalMax) * 100, 2) : 0;
            $passAll       = $group->every(fn($r) => $r->marks_obtained >= $r->exam->pass_marks);

            return [
                'exam_type'  => $examType,
                'percentage' => $percentage,
                'passed'     => $passAll,
                'subjects'   => $group->map(fn($r) => [
                    'subject'        => $r->exam->subject->name,
                    'exam_date'      => $r->exam->exam_date->format('d M Y'),
                    'marks_obtained' => $r->marks_obtained,
                    'max_marks'      => $r->exam->max_marks,
                    'pass_marks'     => $r->exam->pass_marks,
                    'grade'          => $r->grade,
                    'passed'         => $r->marks_obtained >= $r->exam->pass_marks,
                    'absent'         => $r->is_absent,
                ])->values(),
            ];
        })->values();

        return response()->json([
            'success' => true,
            'student' => [
                'name'         => $student->name,
                'class'        => $student->schoolClass?->name,
                'section'      => $student->section?->name,
                'roll_number'  => $student->roll_number,
            ],
            'data' => $formatted,
        ]);
    }

    // GET /api/results/report-card?student_id=&exam_type_id=
    public function reportCard(Request $request)
    {
        $user    = $request->user();
        $role    = $user->getRoleNames()->first();
        $student = $this->resolveStudent($user, $role, $request->student_id);

        if (!$student) {
            return response()->json(['success' => false, 'message' => 'No student found.'], 404);
        }

        $query = ExamResult::with(['exam.examType', 'exam.subject'])
            ->where('student_id', $student->id)
            ->whereHas('exam', fn($q) => $q->where('status', 'published'));

        if ($request->filled('exam_type_id')) {
            $query->whereHas('exam', fn($q) => $q->where('exam_type_id', $request->exam_type_id));
        }

        $results    = $query->get();
        $total      = $results->sum('marks_obtained');
        $maxTotal   = $results->sum(fn($r) => $r->exam->max_marks);
        $percentage = $maxTotal > 0 ? round(($total / $maxTotal) * 100, 2) : 0;

        return response()->json([
            'success'    => true,
            'student'    => [
                'name'        => $student->name,
                'admission_no'=> $student->admission_no,
                'class'       => $student->schoolClass?->name,
                'section'     => $student->section?->name,
                'roll_number' => $student->roll_number,
                'photo_url'   => $student->photo_url,
            ],
            'summary'    => [
                'total_marks'   => $total,
                'max_marks'     => $maxTotal,
                'percentage'    => $percentage,
                'result'        => $percentage >= 35 ? 'PASS' : 'FAIL',
            ],
            'subjects'   => $results->map(fn($r) => [
                'subject'        => $r->exam->subject->name,
                'exam_type'      => $r->exam->examType->name,
                'marks_obtained' => $r->marks_obtained,
                'max_marks'      => $r->exam->max_marks,
                'grade'          => $r->grade,
                'passed'         => $r->marks_obtained >= $r->exam->pass_marks,
            ]),
        ]);
    }

    private function resolveStudent($user, string $role, ?int $studentId): ?Student
    {
        return match ($role) {
            'student' => Student::where('user_id', $user->id)->first(),
            'parent'  => Student::where('parent_user_id', $user->id)
                            ->when($studentId, fn($q, $v) => $q->where('id', $v))
                            ->first(),
            default   => $studentId ? Student::find($studentId) : null,
        };
    }
}
