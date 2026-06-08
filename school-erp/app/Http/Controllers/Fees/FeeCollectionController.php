<?php
namespace App\Http\Controllers\Fees;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\FeePayment;
use App\Models\FeeStructure;
use App\Models\SchoolClass;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FeeCollectionController extends Controller
{
    // ── Search student to collect fee ──────────────────────
    public function index(Request $request)
    {
        $this->authorize('fee.collect');

        $classes = SchoolClass::active()->get();
        $student = null;

        if ($request->filled('search')) {
            $student = Student::with([
                'schoolClass', 'section',
                'feePayments.feeStructure'
            ])
            ->active()
            ->where(function($q) use ($request) {
                $q->where('admission_no', $request->search)
                  ->orWhere('emis_number', $request->search)
                  ->orWhere('parent_mobile', $request->search)
                  ->orWhere('name', 'like', '%' . $request->search . '%');
            })
            ->first();
        }

        return view('fees.collect', compact('classes', 'student'));
    }

    // ── Record offline payment ─────────────────────────────
    public function store(Request $request)
    {
        $this->authorize('fee.collect');

        $validated = $request->validate([
            'student_id'       => 'required|exists:students,id',
            'fee_structure_id' => 'required|exists:fee_structures,id',
            'amount_paid'      => 'required|numeric|min:1',
            'payment_mode'     => 'required|in:cash,cheque,dd,bank_transfer',
            'payment_date'     => 'required|date',
            'cheque_no'        => 'nullable|string|max:30',
            'bank_name'        => 'nullable|string|max:100',
            'discount'         => 'nullable|numeric|min:0',
            'fine'             => 'nullable|numeric|min:0',
            'notes'            => 'nullable|string',
        ]);

        $structure = FeeStructure::findOrFail($validated['fee_structure_id']);

        $payment = FeePayment::create(array_merge($validated, [
            'amount_due'   => $structure->amount,
            'discount'     => $validated['discount'] ?? 0,
            'fine'         => $validated['fine']     ?? 0,
            'status'       => 'paid',
            'collected_by' => auth()->id(),
        ]));

        return redirect()
            ->route('admin.fees.receipt', $payment)
            ->with('success', 'Payment recorded. Receipt ready.');
    }

    // ── Student fee summary page ───────────────────────────
    public function studentFees(Student $student)
    {
        $this->authorize('fee.view');

        $student->load(['schoolClass', 'section', 'academicYear']);

        $year       = AcademicYear::current();
        $structures = FeeStructure::where('school_class_id', $student->school_class_id)
            ->where('academic_year_id', $year->id)
            ->get();

        // Map each structure to its latest payment status
        $feeStatus = $structures->map(function($struct) use ($student) {
            $payment = FeePayment::where('student_id', $student->id)
                ->where('fee_structure_id', $struct->id)
                ->latest()
                ->first();

            return [
                'structure' => $struct,
                'payment'   => $payment,
                'paid'      => $payment?->status === 'paid',
            ];
        });

        $totalDue     = $structures->sum('amount');
        $totalPaid    = FeePayment::where('student_id', $student->id)
            ->paid()->sum('amount_paid');
        $totalPending = $totalDue - $totalPaid;

        return view('fees.student', compact(
            'student', 'feeStatus', 'totalDue', 'totalPaid', 'totalPending'
        ));
    }

    // ── Generate & stream receipt PDF ─────────────────────
    public function receipt(FeePayment $payment)
    {
        $this->authorize('fee.view');

        $payment->load([
            'student.schoolClass',
            'student.section',
            'feeStructure.academicYear',
            'collectedBy',
        ]);

        $pdf = Pdf::loadView('fees.receipt-pdf', compact('payment'))
            ->setPaper('a5', 'portrait')
            ->setOptions([
                'dpi'                   => 150,
                'defaultFont'           => 'sans-serif',
                'isHtml5ParserEnabled'  => true,
                'isRemoteEnabled'       => false,
            ]);

        $filename = 'Receipt-' . $payment->receipt_no . '.pdf';

        return $pdf->stream($filename);
    }
}