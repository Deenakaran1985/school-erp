<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\Homework;
use App\Models\HomeworkSubmission;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StudentApiController extends Controller
{
    // GET /api/profile  (works for student OR parent)
    public function profile(Request $request)
    {
        $user = $request->user();
        $role = $user->getRoleNames()->first();

        if ($role === 'student') {
            $student = Student::with([
                'schoolClass', 'section', 'academicYear', 'transport.route',
            ])->where('user_id', $user->id)->firstOrFail();

            return response()->json([
                'success' => true,
                'data'    => $this->formatStudent($student),
            ]);
        }

        // Parent — return all children
        $children = Student::with(['schoolClass', 'section'])
            ->where('parent_user_id', $user->id)
            ->active()
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $children->map(fn($s) => $this->formatStudent($s)),
        ]);
    }

    // GET /api/student/{student}/attendance
    public function attendance(Request $request, Student $student)
    {
        $this->authorizeStudentAccess($request->user(), $student);

        $month = $request->input('month', now()->format('Y-m'));
        [$year, $mon] = explode('-', $month);

        $records = Attendance::where('student_id', $student->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $mon)
            ->orderBy('date')
            ->get(['date', 'status', 'remarks']);

        $summary = [
            'present' => $records->where('status', 'present')->count(),
            'absent'  => $records->where('status', 'absent')->count(),
            'late'    => $records->where('status', 'late')->count(),
            'holiday' => $records->where('status', 'holiday')->count(),
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

    // GET /api/student/{student}/homework
    public function homework(Request $request, Student $student)
    {
        $this->authorizeStudentAccess($request->user(), $student);

        $homework = Homework::with('subject')
            ->where('school_class_id', $student->school_class_id)
            ->where('section_id', $student->section_id)
            ->where('due_date', '>=', now()->subDays(30))
            ->orderByDesc('due_date')
            ->get();

        $submittedIds = HomeworkSubmission::where('student_id', $student->id)
            ->pluck('homework_id')
            ->toArray();

        return response()->json([
            'success' => true,
            'data'    => $homework->map(fn($hw) => [
                'id'          => $hw->id,
                'subject'     => $hw->subject->name,
                'title'       => $hw->title,
                'description' => $hw->description,
                'due_date'    => $hw->due_date->format('Y-m-d'),
                'submitted'   => in_array($hw->id, $submittedIds),
                'overdue'     => $hw->due_date->isPast(),
            ]),
        ]);
    }

    // POST /api/profile/update-fcm
    public function updateFcmToken(Request $request)
    {
        $request->validate(['fcm_token' => 'required|string']);
        $request->user()->update(['fcm_token' => $request->fcm_token]);
        return response()->json(['success' => true]);
    }

    // POST /api/profile/update-avatar
    public function updateAvatar(Request $request)
    {
        $request->validate(['avatar' => 'required|image|max:2048']);

        $user = $request->user();
        if ($user->avatar) Storage::disk('public')->delete($user->avatar);

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->update(['avatar' => $path]);

        return response()->json([
            'success'    => true,
            'avatar_url' => asset('storage/' . $path),
        ]);
    }

    private function formatStudent(Student $s): array
    {
        return [
            'id'             => $s->id,
            'admission_no'   => $s->admission_no,
            'emis_number'    => $s->emis_number,
            'name'           => $s->name,
            'father_name'    => $s->father_name,
            'mother_name'    => $s->mother_name,
            'date_of_birth'  => $s->date_of_birth?->format('Y-m-d'),
            'gender'         => $s->gender,
            'blood_group'    => $s->blood_group,
            'photo_url'      => $s->photo_url,
            'community'      => $s->community,
            'class'          => $s->schoolClass?->name,
            'section'        => $s->section?->name,
            'class_section'  => $s->class_section,
            'roll_number'    => $s->roll_number,
            'parent_mobile'  => $s->parent_mobile,
            'address'        => $s->address,
            'uses_transport' => $s->uses_transport,
            'transport_route'=> $s->transport?->route?->name,
            'status'         => $s->status,
        ];
    }

    private function authorizeStudentAccess($user, Student $student): void
    {
        $role = $user->getRoleNames()->first();

        if ($role === 'student' && $student->user_id !== $user->id) {
            abort(403, 'Access denied.');
        }

        if ($role === 'parent' && $student->parent_user_id !== $user->id) {
            abort(403, 'Access denied.');
        }
    }
}
