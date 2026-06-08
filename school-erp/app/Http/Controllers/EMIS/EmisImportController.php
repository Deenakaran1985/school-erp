<?php

namespace App\Http\Controllers\EMIS;

use App\Http\Controllers\Controller;
use App\Imports\EmisStudentImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class EmisImportController extends Controller
{
    public function showForm()
    {
        $this->authorize('student.import');
        return view('students.emis-import');
    }

    public function import(Request $request)
    {
        $this->authorize('student.import');

        $request->validate([
            'excel_file'       => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'school_class_id'  => ['nullable', 'exists:school_classes,id'],
            'section_id'       => ['nullable', 'exists:sections,id'],
            'duplicate_mode'   => ['required', 'in:skip,update'],
        ]);

        $import = new EmisStudentImport(
            yearId:    (int) $request->academic_year_id,
            classId:   (int) $request->school_class_id,
            sectionId: (int) $request->section_id,
            dupMode:   $request->duplicate_mode,
        );

        Excel::import($import, $request->file('excel_file'));

        $summary = [
            'imported' => $import->imported,
            'skipped'  => $import->skipped,
            'updated'  => $import->updated,
            'failed'   => count($import->errors),
            'errors'   => $import->errors,
        ];

        return redirect()
            ->route('admin.students.emis.form')
            ->with('import_summary', $summary);
    }
}