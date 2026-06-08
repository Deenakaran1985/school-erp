<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Homework;
use App\Models\Payroll;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Staff;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StaffApiController extends Controller
{
    // GET /api/staff/profile
    public function profile(Request $request)
    {
        $staff = Staff::with(['user', 'department'])
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data'    => [
                'id'              => $staff->id,
                'employee_id'     => $staff->employee_id,
                'name'            => $staff->name,
                'designation'     => $staff->designation,
                'department'      => $staff->department?->name,
                'staff_type'      => $staff->staff_type,
                'qualification'   => $staff->qualification,
                'joining_date'    => $staff->joining_date?->format('d M Y'),
                'phone'           => $staff->user->phone,
                'email'           => $staff->user->email,
                'photo_url'       => $staff->photo
                    ? asset('storage/' . $staff->photo)
                    : asset('images/default-staff.png'),
                'bank_name'       => $staff->bank_name,
                'bank_account'    => $staff->bank_account,
                'bank_ifsc'       => $staff->bank_ifsc,
                'gross_salary'    => (float) $staff->gross_salary,
            ],
        ]);
    }

    // GET /api/staff/payslips
    public function payslips(Request $request)
    {
        $staff = Staff::where('user_id', $request->user()->id)->firstOrFail();

        $payrolls = Payroll::where('staff_id', $staff->id)
            ->whereIn('status', ['approved', 'paid'])
            ->orderByDesc('month_year')
            ->paginate(12);

        return response()->json([
            'success' => true,
            'data'    => $payrolls->map(fn($p) => [
                'id'              => $p->id,
                'month'           => \Carbon\Carbon::createFromFormat('Y-m', $p->month_year)->format('F Y'),
                'gross_salary'    => (float) $p->gross_salary,
                'total_deduction' => (float) $p->total_deduction,
                'net_salary'      => (float) $p->net_salary,
                'present_days'    => $p->present_days,
                'working_days'    => $p->working_days,
                'status'          => $p->status,
                'paid_on'         => $p->paid_on?->format('d M Y'),
                'payslip_url'     => route('admin.payroll.payslip', $p->id),
            ]),
            'meta'    => [
                'current_page' => $payrolls->currentPage(),
                'last_page'    => $payrolls->lastPage(),
            ],
        ]);
    }

    // GET /api/staff/attendance?month=2025-06
    public function attendance(Request $request)
    {
        $user  = $request->user();
        $month = $request->input('month', now()->format('Y-m'));
        [$year, $mon] = explode('-', $month);

        $records = Attendance::where('user_id', $user->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $mon)
            ->orderBy('date')
            ->get(['date', 'status', 'remarks']);

        $summary = [
            'present' => $records->where('status', 'present')->count(),
            'absent'  => $records->where('status', 'absent')->count(),
            'late'    => $records->where('status', 'late')->count(),
            'leave'   => $records->where('status', 'leave')->count(),
        ];

        return response()->json([
            'success' => true,
            'month'   => $month,
            'summary' => $summary,
            'records' => $records->map(fn($r) => [
                'date'    => $r->date->format('Y-m-d'),
                'status'  => $r->status,
                'remarks' => $r->remarks,
            ]),
        ]);
    }

    // GET /api/staff/my-classes  — teacher's assigned classes
    public function myClasses(Request $request)
    {
        $user = $request->user();

        // Find sections where this teacher is class teacher
        $sections = Section::with(['schoolClass', 'students'])
            ->where('class_teacher_id', $user->id)
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $sections->map(fn($s) => [
                'section_id'      => $s->id,
                'class'           => $s->schoolClass->name,
                'section'         => $s->name,
                'student_count'   => $s->students->count(),
            ]),
        ]);
    }

    // GET /api/staff/students?class_id=&section_id=
    public function students(Request $request)
    {
        $request->validate([
            'class_id'   => 'required|exists:school_classes,id',
            'section_id' => 'nullable|exists:sections,id',
        ]);

        $students = Student::with(['section'])
            ->where('school_class_id', $request->class_id)
            ->when($request->section_id, fn($q, $v) => $q->where('section_id', $v))
            ->active()
            ->orderBy('roll_number')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $students->map(fn($s) => [
                'id'          => $s->id,
                'name'        => $s->name,
                'roll_number' => $s->roll_number,
                'section'     => $s->section?->name,
                'photo_url'   => $s->photo_url,
            ]),
        ]);
    }

    // POST /api/staff/attendance/mark  — mark student attendance
    public function markStudentAttendance(Request $request)
    {
        $this->authorize('attendance.mark');

        $request->validate([
            'date'      => 'required|date|before_or_equal:today',
            'records'   => 'required|array|min:1',
            'records.*.student_id' => 'required|exists:students,id',
            'records.*.status'     => 'required|in:present,absent,late,holiday',
        ]);

        foreach ($request->records as $rec) {
            Attendance::updateOrCreate(
                ['student_id' => $rec['student_id'], 'date' => $request->date],
                ['status' => $rec['status'], 'remarks' => $rec['remarks'] ?? null]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Attendance saved for ' . count($request->records) . ' students.',
        ]);
    }

    // POST /api/staff/homework
    public function createHomework(Request $request)
    {
        $request->validate([
            'subject_id'      => 'required|exists:subjects,id',
            'school_class_id' => 'required|exists:school_classes,id',
            'section_id'      => 'nullable|exists:sections,id',
            'title'           => 'required|string|max:150',
            'description'     => 'nullable|string',
            'due_date'        => 'required|date|after:today',
        ]);

        $hw = Homework::create(array_merge(
            $request->only('subject_id', 'school_class_id', 'section_id', 'title', 'description', 'due_date'),
            ['created_by' => $request->user()->id]
        ));

        return response()->json([
            'success' => true,
            'message' => 'Homework assigned.',
            'id'      => $hw->id,
        ], 201);
    }
}
