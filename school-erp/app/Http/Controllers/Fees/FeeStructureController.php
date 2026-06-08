<?php
namespace App\Http\Controllers\Fees;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\FeeStructure;
use App\Models\SchoolClass;
use Illuminate\Http\Request;

class FeeStructureController extends Controller
{
    public function index()
    {
        $this->authorize('fee.structure.manage');

        $year       = AcademicYear::current();
        $structures = FeeStructure::with(['schoolClass', 'academicYear'])
            ->where('academic_year_id', $year->id)
            ->orderBy('school_class_id')
            ->orderBy('term')
            ->get()
            ->groupBy('school_class_id');

        $classes = SchoolClass::active()->get();

        return view('fees.structures.index', compact(
            'structures', 'classes', 'year'
        ));
    }

    public function store(Request $request)
    {
        $this->authorize('fee.structure.manage');

        $validated = $request->validate([
            'school_class_id'  => 'required|exists:school_classes,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'fee_head'         => 'required|string|max:100',
            'amount'           => 'required|numeric|min:0',
            'term'             => 'required|in:term1,term2,term3,annual,monthly',
            'due_date'         => 'nullable|date',
            'is_optional'      => 'boolean',
        ]);

        FeeStructure::create($validated);

        return back()->with('success', 'Fee structure added.');
    }

    public function destroy(FeeStructure $structure)
    {
        $this->authorize('fee.structure.manage');

        if ($structure->payments()->exists()) {
            return back()->with('error', 'Cannot delete — payments exist for this structure.');
        }

        $structure->delete();
        return back()->with('success', 'Fee structure deleted.');
    }

    // AJAX — get fee structures for a student's class
    public function forClass(Request $request)
    {
        $year    = AcademicYear::current();
        $structs = FeeStructure::where('academic_year_id', $year->id)
            ->where('school_class_id', $request->class_id)
            ->get(['id', 'fee_head', 'amount', 'term']);

        return response()->json($structs);
    }
}