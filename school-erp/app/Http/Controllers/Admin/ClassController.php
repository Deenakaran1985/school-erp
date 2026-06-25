<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Subject;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function index()
    {
        $this->authorize('class.view');
        $year    = AcademicYear::current();
        $classes = SchoolClass::with(['sections', 'subjects'])
            ->where('academic_year_id', $year->id)
            ->orderBy('sort_order')
            ->get();

        return view('classes.index', compact('classes', 'year'));
    }

    public function create()
    {
        $this->authorize('class.manage');
        $year = AcademicYear::current();
        return view('classes.create', compact('year'));
    }

    public function store(Request $request)
    {
        $this->authorize('class.manage');

        $validated = $request->validate([
            'name'         => 'required|string|max:50',
            'display_name' => 'nullable|string|max:100',
            'level'        => 'required|integer|min:1|max:13',
            'sort_order'   => 'nullable|integer|min:0',
            'sections'     => 'nullable|array',
            'sections.*'   => 'string|max:10',
            'subjects'     => 'nullable|array',
            'subjects.*.name'       => 'required|string|max:100',
            'subjects.*.code'       => 'nullable|string|max:10',
            'subjects.*.max_marks'  => 'nullable|integer|min:0',
            'subjects.*.pass_marks' => 'nullable|integer|min:0',
        ]);

        $year  = AcademicYear::current();
        $class = SchoolClass::create([
            'name'             => $validated['name'],
            'display_name'     => $validated['display_name'] ?? $validated['name'],
            'level'            => $validated['level'],
            'sort_order'       => $validated['sort_order'] ?? 0,
            'academic_year_id' => $year->id,
            'is_active'        => true,
        ]);

        // Create sections
        foreach ($request->input('sections', []) as $sectionName) {
            if (trim($sectionName)) {
                Section::create([
                    'school_class_id' => $class->id,
                    'name'            => strtoupper(trim($sectionName)),
                    'max_strength'    => 40,
                ]);
            }
        }

        // Create subjects
        foreach ($request->input('subjects', []) as $i => $sub) {
            if (!empty($sub['name'])) {
                Subject::create([
                    'school_class_id' => $class->id,
                    'name'            => $sub['name'],
                    'code'            => $sub['code'] ?? null,
                    'max_marks'       => $sub['max_marks']  ?? 100,
                    'pass_marks'      => $sub['pass_marks'] ?? 35,
                    'sort_order'      => $i + 1,
                    'is_active'       => true,
                ]);
            }
        }

        return redirect()->route('admin.classes.index')
            ->with('success', 'Class created with sections and subjects.');
    }

    public function edit(SchoolClass $class)
    {
        $this->authorize('class.manage');
        $class->load(['sections', 'subjects']);
        return view('classes.edit', compact('class'));
    }

    public function update(Request $request, SchoolClass $class)
    {
        $this->authorize('class.manage');

        $validated = $request->validate([
            'name'         => 'required|string|max:50',
            'display_name' => 'nullable|string|max:100',
            'level'        => 'required|integer|min:1|max:13',
            'sort_order'   => 'nullable|integer|min:0',
            'is_active'    => 'boolean',
        ]);

        $class->update($validated);

        return redirect()->route('admin.classes.index')
            ->with('success', 'Class updated.');
    }

    public function destroy(SchoolClass $class)
    {
        $this->authorize('class.manage');

        if ($class->students()->exists()) {
            return back()->with('error', 'Cannot delete class with enrolled students.');
        }

        $class->sections()->delete();
        $class->subjects()->delete();
        $class->delete();

        return redirect()->route('admin.classes.index')
            ->with('success', 'Class deleted.');
    }

    // POST admin/classes/{class}/sections
    public function addSection(Request $request, SchoolClass $class)
    {
        $this->authorize('class.manage');

        $request->validate(['name' => 'required|string|max:10']);

        Section::create([
            'school_class_id' => $class->id,
            'name'            => strtoupper($request->name),
            'max_strength'    => $request->input('max_strength', 40),
        ]);

        return back()->with('success', 'Section added.');
    }

    // DELETE admin/classes/{class}/sections/{section}
    public function removeSection(SchoolClass $class, Section $section)
    {
        $this->authorize('class.manage');

        if ($section->students()->exists()) {
            return back()->with('error', 'Cannot remove section with enrolled students.');
        }

        $section->delete();
        return back()->with('success', 'Section removed.');
    }

    // POST admin/classes/{class}/subjects
    public function addSubject(Request $request, SchoolClass $class)
    {
        $this->authorize('class.manage');

        $request->validate([
            'name'       => 'required|string|max:100',
            'code'       => 'nullable|string|max:10',
            'max_marks'  => 'nullable|integer|min:0',
            'pass_marks' => 'nullable|integer|min:0',
        ]);

        $count = $class->subjects()->count();
        Subject::create([
            'school_class_id' => $class->id,
            'name'            => $request->name,
            'code'            => $request->code,
            'max_marks'       => $request->input('max_marks', 100),
            'pass_marks'      => $request->input('pass_marks', 35),
            'sort_order'      => $count + 1,
            'is_active'       => true,
        ]);

        return back()->with('success', 'Subject added.');
    }
}
