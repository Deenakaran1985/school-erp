<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Department;
use App\Models\ExamType;
use App\Models\GradeConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index()
    {
        $this->authorize('settings.manage');

        $academicYears = AcademicYear::orderByDesc('start_date')->get();
        $currentYear   = AcademicYear::current();
        $departments   = Department::withCount('staff')->get();
        $examTypes     = ExamType::orderBy('sort_order')->get();
        $gradeConfig   = GradeConfig::orderByDesc('min_percent')->get();

        $schoolInfo = [
            'name'    => config('app.school_name', 'School Name'),
            'address' => config('app.school_address', ''),
            'phone'   => config('app.school_phone', ''),
            'email'   => config('app.school_email', ''),
            'logo'    => config('app.school_logo', ''),
        ];

        return view('settings.index', compact(
            'academicYears', 'currentYear', 'departments',
            'examTypes', 'gradeConfig', 'schoolInfo'
        ));
    }

    // POST admin/settings/school-info
    public function updateSchoolInfo(Request $request)
    {
        $this->authorize('settings.manage');

        $validated = $request->validate([
            'school_name'    => 'required|string|max:150',
            'school_address' => 'nullable|string',
            'school_phone'   => 'nullable|string|max:20',
            'school_email'   => 'nullable|email',
            'school_logo'    => 'nullable|image|max:2048',
        ]);

        $env = base_path('.env');
        $lines = file($env);

        $map = [
            'SCHOOL_NAME'    => $validated['school_name'],
            'SCHOOL_ADDRESS' => $validated['school_address'] ?? '',
            'SCHOOL_PHONE'   => $validated['school_phone']   ?? '',
            'SCHOOL_EMAIL'   => $validated['school_email']   ?? '',
        ];

        foreach ($map as $key => $value) {
            $found = false;
            foreach ($lines as &$line) {
                if (str_starts_with($line, "{$key}=")) {
                    $line  = "{$key}=\"{$value}\"\n";
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $lines[] = "{$key}=\"{$value}\"\n";
            }
        }

        if ($request->hasFile('school_logo')) {
            $path    = $request->file('school_logo')->store('school', 'public');
            $found   = false;
            foreach ($lines as &$line) {
                if (str_starts_with($line, 'SCHOOL_LOGO=')) {
                    $line  = "SCHOOL_LOGO=\"{$path}\"\n";
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $lines[] = "SCHOOL_LOGO=\"{$path}\"\n";
            }
        }

        file_put_contents($env, implode('', $lines));

        return back()->with('success', 'School information updated.');
    }

    // POST admin/settings/academic-year
    public function storeAcademicYear(Request $request)
    {
        $this->authorize('settings.manage');

        $validated = $request->validate([
            'label'      => 'required|string|max:20',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after:start_date',
        ]);

        AcademicYear::create(array_merge($validated, ['is_current' => false]));

        return back()->with('success', 'Academic year added.');
    }

    // PUT admin/settings/academic-year/{year}/set-current
    public function setCurrentYear(AcademicYear $year)
    {
        $this->authorize('settings.manage');

        AcademicYear::where('is_current', true)->update(['is_current' => false]);
        $year->update(['is_current' => true]);

        return back()->with('success', "{$year->label} set as current academic year.");
    }

    // POST admin/settings/departments
    public function storeDepartment(Request $request)
    {
        $this->authorize('settings.manage');

        $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:10',
        ]);

        Department::create([
            'name'      => $request->name,
            'code'      => $request->code,
            'is_active' => true,
        ]);

        return back()->with('success', 'Department added.');
    }

    // POST admin/settings/grade-config
    public function storeGradeConfig(Request $request)
    {
        $this->authorize('settings.manage');

        $request->validate([
            'grade'       => 'required|string|max:5',
            'label'       => 'required|string|max:30',
            'min_percent' => 'required|numeric|min:0|max:100',
            'max_percent' => 'required|numeric|min:0|max:100|gt:min_percent',
        ]);

        GradeConfig::create($request->only('grade', 'label', 'min_percent', 'max_percent'));

        return back()->with('success', 'Grade configuration added.');
    }

    // POST admin/settings/exam-types
    public function storeExamType(Request $request)
    {
        $this->authorize('settings.manage');

        $request->validate([
            'name'        => 'required|string|max:50',
            'short_name'  => 'nullable|string|max:10',
            'weightage'   => 'nullable|numeric|min:0|max:100',
            'sort_order'  => 'nullable|integer|min:0',
        ]);

        ExamType::create([
            'name'       => $request->name,
            'short_name' => $request->short_name,
            'weightage'  => $request->input('weightage', 0),
            'sort_order' => $request->input('sort_order', 0),
        ]);

        return back()->with('success', 'Exam type added.');
    }
}
