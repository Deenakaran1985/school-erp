<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStudentRequest;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    // ── LIST ───────────────────────────────────────────────
    public function index(Request $request)
    {
        $this->authorize('student.view');

        $year    = AcademicYear::current();
        $classes = SchoolClass::active()->get();

        $students = Student::with(['schoolClass', 'section', 'parentUser'])
            ->currentYear()
            ->active()
            ->when($request->search, fn($q, $v) =>
                $q->where(function($q) use ($v) {
                    $q->where('name', 'like', "%{$v}%")
                      ->orWhere('admission_no', 'like', "%{$v}%")
                      ->orWhere('emis_number', 'like', "%{$v}%")
                      ->orWhere('parent_mobile', 'like', "%{$v}%");
                })
            )
            ->when($request->class_id, fn($q, $v) =>
                $q->where('school_class_id', $v))
            ->when($request->section_id, fn($q, $v) =>
                $q->where('section_id', $v))
            ->when($request->gender, fn($q, $v) =>
                $q->where('gender', $v))
            ->when($request->community, fn($q, $v) =>
                $q->where('community', $v))
            ->orderBy('school_class_id')
            ->orderBy('roll_number')
            ->paginate(25)
            ->withQueryString();

        return view('students.index', compact(
            'students', 'classes', 'year'
        ));
    }

    // ── CREATE FORM ────────────────────────────────────────
    public function create()
    {
        $this->authorize('student.create');

        $year    = AcademicYear::current();
        $classes = SchoolClass::active()->get();
        $years   = AcademicYear::orderByDesc('start_date')->get();

        return view('students.create', compact('classes', 'year', 'years'));
    }

    // ── STORE ──────────────────────────────────────────────
    public function store(StoreStudentRequest $request)
    {
        DB::transaction(function () use ($request) {

            // 1. Create parent login user
            $parentUser = User::firstOrCreate(
                ['phone' => $request->parent_mobile],
                [
                    'name'      => $request->father_name . ' (Parent)',
                    'email'     => 'parent_' . $request->parent_mobile . '@school.local',
                    'password'  => Hash::make($request->parent_mobile),
                    'user_type' => 'parent',
                    'status'    => 'active',
                ]
            );
            if (!$parentUser->hasRole('parent')) {
                $parentUser->assignRole('parent');
            }

            // 2. Create student login user
            $studentUser = User::create([
                'name'      => $request->name,
                'email'     => 'student_' . strtolower(str_replace(' ', '.', $request->name))
                              . '_' . time() . '@school.local',
                'password'  => Hash::make($request->parent_mobile),
                'user_type' => 'student',
                'status'    => 'active',
            ]);
            $studentUser->assignRole('student');

            // 3. Handle photo upload
            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')
                    ->store('student-photos', 'public');
            }

            // 4. Generate admission number
            $lastId  = Student::max('id') + 1;
            $admNo   = 'ADM-' . now()->year . '-' . str_pad($lastId, 4, '0', STR_PAD_LEFT);

            // 5. Create student record
            Student::create(array_merge(
                $request->validated(),
                [
                    'user_id'        => $studentUser->id,
                    'parent_user_id' => $parentUser->id,
                    'admission_no'   => $admNo,
                    'photo'          => $photoPath,
                    'admission_date' => now(),
                    'status'         => 'active',
                ]
            ));
        });

        return redirect()
            ->route('admin.students.index')
            ->with('success', 'Student added successfully.');
    }

    // ── SHOW / PROFILE ─────────────────────────────────────
    public function show(Student $student)
    {
        $this->authorize('student.view');

        $student->load([
            'schoolClass', 'section', 'academicYear',
            'parentUser', 'feePayments.feeStructure',
            'examResults.exam.subject', 'examResults.exam.examType',
            'attendance', 'transport.route',
        ]);

        // Attendance summary (current month)
        $attSummary = [
            'present' => $student->attendance->where('status', 'present')->count(),
            'absent'  => $student->attendance->where('status', 'absent')->count(),
            'late'    => $student->attendance->where('status', 'late')->count(),
        ];

        return view('students.show', compact('student', 'attSummary'));
    }

    // ── EDIT FORM ──────────────────────────────────────────
    public function edit(Student $student)
    {
        $this->authorize('student.edit');

        $classes  = SchoolClass::active()->get();
        $sections = Section::where('school_class_id', $student->school_class_id)->get();
        $years    = AcademicYear::orderByDesc('start_date')->get();

        return view('students.edit', compact('student', 'classes', 'sections', 'years'));
    }

    // ── UPDATE ─────────────────────────────────────────────
    public function update(Request $request, Student $student)
    {
        $this->authorize('student.edit');

        $validated = $request->validate([
            'name'             => 'required|string|max:100',
            'father_name'      => 'required|string|max:100',
            'mother_name'      => 'nullable|string|max:100',
            'date_of_birth'    => 'required|date',
            'gender'           => 'required|in:M,F,O',
            'school_class_id'  => 'required|exists:school_classes,id',
            'section_id'       => 'nullable|exists:sections,id',
            'parent_mobile'    => 'required|digits:10',
            'emis_number'      => 'nullable|string|max:20|unique:students,emis_number,' . $student->id,
            'community'        => 'nullable|in:OC,BC,MBC,SC,ST',
            'address'          => 'nullable|string',
            'roll_number'      => 'nullable|integer',
            'status'           => 'required|in:active,inactive,transferred,passed_out',
            'photo'            => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            if ($student->photo) Storage::disk('public')->delete($student->photo);
            $validated['photo'] = $request->file('photo')->store('student-photos', 'public');
        }

        $student->update($validated);

        return redirect()
            ->route('admin.students.show', $student)
            ->with('success', 'Student updated successfully.');
    }

    // ── SOFT DELETE ────────────────────────────────────────
    public function destroy(Student $student)
    {
        $this->authorize('student.delete');

        $student->update(['status' => 'inactive']);
        $student->delete(); // soft delete

        return redirect()
            ->route('admin.students.index')
            ->with('success', 'Student removed.');
    }

    // ── AJAX: get sections by class ─────────────────────────
    public function getSections(Request $request)
    {
        $sections = Section::where('school_class_id', $request->class_id)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($sections);
    }
}
