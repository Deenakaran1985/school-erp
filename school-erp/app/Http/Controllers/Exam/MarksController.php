<?php

namespace App\Http\Controllers\Exam;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\Student;
use App\Services\GradeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MarksController extends Controller
{
    public function __construct(private GradeService $gradeService) {}

    // ── Show marks entry form ──────────────────────────────
    public function index(Exam $exam)
    {
        $this->authorize('exam.marks.entry');

        if ($exam->status === 'published') {
            return redirect()
                ->route('admin.exams.results', $exam)
                ->with('error', 'Results already published. Cannot edit marks.');
        }

        $exam->load(['examType', 'subject', 'schoolClass']);

        // Get students for this class, ordered by roll number
        $students = Student::with(['section'])
            ->where('school_class_id', $exam->school_class_id)
            ->active()
            ->orderBy('roll_number')
            ->get();

        // Pre-load existing results (if partial save done before)
        $existingResults = ExamResult::where('exam_id', $exam->id)
            ->pluck('marks_obtained', 'student_id')
            ->toArray();

        $absentStudents = ExamResult::where('exam_id', $exam->id)
            ->where('is_absent', true)
            ->pluck('student_id')
            ->toArray();

        return view('exams.marks', compact(
            'exam', 'students', 'existingResults', 'absentStudents'
        ));
    }

    // ── Save bulk marks ────────────────────────────────────
    public function store(Request $request, Exam $exam)
    {
        $this->authorize('exam.marks.entry');

        if ($exam->status === 'published') {
            return back()->with('error', 'Cannot modify published results.');
        }

        $request->validate([
            'marks'           => 'required|array',
            'marks.*'         => 'nullable|numeric|min:0|max:' . $exam->max_marks,
            'absent'          => 'nullable|array',
        ]);

        $year        = AcademicYear::current();
        $marksInput  = $request->input('marks', []);
        $absentInput = $request->input('absent', []);

        DB::transaction(function () use ($exam, $marksInput, $absentInput, $year) {

            foreach ($marksInput as $studentId => $marks) {
                $isAbsent = isset($absentInput[$studentId]);
                $marks    = $isAbsent ? null : (float) $marks;

                // Auto-calculate percentage and grade
                $percentage = (!$isAbsent && $marks !== null)
                    ? round(($marks / $exam->max_marks) * 100, 2)
                    : null;

                $gradeData = (!$isAbsent && $percentage !== null)
                    ? $this->gradeService->resolveGrade($percentage, $year->id)
                    : ['grade' => null, 'grade_point' => null];

                ExamResult::updateOrCreate(
                    ['exam_id' => $exam->id, 'student_id' => $studentId],
                    [
                        'marks_obtained' => $marks,
                        'percentage'     => $percentage,
                        'grade'          => $gradeData['grade'],
                        'grade_point'    => $gradeData['grade_point'],
                        'is_absent'      => $isAbsent,
                        'entered_by'     => auth()->id(),
                        'notified_at'    => null, // reset notification flag
                    ]
                );
            }

            // Update exam status to marks_entry
            $exam->update(['status' => 'marks_entry']);

            // Compute ranks after all marks saved
            $this->gradeService->computeRanks($exam->id);
        });

        return redirect()
            ->route('admin.exams.marks.index', $exam)
            ->with('success', 'Marks saved successfully. Review and publish when ready.');
    }
}