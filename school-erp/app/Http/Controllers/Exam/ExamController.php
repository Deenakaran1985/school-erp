<?php
namespace App\Http\Controllers\Exam;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Exam;
use App\Models\ExamType;
use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('exam.view');

        $year    = AcademicYear::current();
        $classes = SchoolClass::active()->get();

        $exams = Exam::with(['examType', 'schoolClass', 'subject', 'createdBy'])
            ->where('academic_year_id', $year->id)
            ->when($request->class_id,   fn($q, $v) => $q->where('school_class_id', $v))
            ->when($request->exam_type, fn($q, $v) => $q->where('exam_type_id', $v))
            ->when($request->status,    fn($q, $v) => $q->where('status', $v))
            ->orderByDesc('exam_date')
            ->paginate(20)
            ->withQueryString();

        $examTypes = ExamType::orderBy('sort_order')->get();

        return view('exams.index', compact('exams', 'classes', 'examTypes', 'year'));
    }

    public function create()
    {
        $this->authorize('exam.create');

        $classes   = SchoolClass::active()->get();
        $examTypes = ExamType::orderBy('sort_order')->get();

        return view('exams.create', compact('classes', 'examTypes'));
    }

    public function store(Request $request)
    {
        $this->authorize('exam.create');

        $validated = $request->validate([
            'exam_type_id'     => 'required|exists:exam_types,id',
            'school_class_id'  => 'required|exists:school_classes,id',
            'subject_id'       => 'required|exists:subjects,id',
            'exam_name'        => 'required|string|max:150',
            'exam_date'        => 'required|date',
            'start_time'       => 'nullable|date_format:H:i',
            'duration_minutes' => 'nullable|integer|min:10',
            'max_marks'        => 'required|integer|min:1',
            'pass_marks'       => 'required|integer|min:1|lt:max_marks',
            'hall_no'          => 'nullable|string|max:30',
            'instructions'     => 'nullable|string',
        ]);

        $year = AcademicYear::current();

        Exam::create(array_merge($validated, [
            'academic_year_id' => $year->id,
            'created_by'       => auth()->id(),
            'status'           => 'scheduled',
        ]));

        return redirect()
            ->route('admin.exams.index')
            ->with('success', 'Exam scheduled successfully.');
    }

    public function show(Exam $exam)
    {
        $this->authorize('exam.view');

        $exam->load(['examType', 'schoolClass', 'subject', 'results.student']);

        return view('exams.show', compact('exam'));
    }

    // AJAX: subjects for selected class
    public function getSubjects(Request $request)
    {
        $subjects = Subject::where('school_class_id', $request->class_id)
            ->active()
            ->orderBy('sort_order')
            ->get(['id', 'name', 'code', 'max_marks', 'pass_marks']);

        return response()->json($subjects);
    }
}