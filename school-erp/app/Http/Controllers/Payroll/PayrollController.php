<?php
namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Payroll;
use App\Models\Staff;
use App\Services\PayrollService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    public function __construct(private PayrollService $payrollService) {}

    // ── List payroll records ───────────────────────────────
    public function index(Request $request)
    {
        $this->authorize('payroll.view');

        // Default to current month
        $monthYear = $request->input('month_year', now()->format('Y-m'));
        $year      = AcademicYear::current();

        $payrolls = Payroll::with(['staff.user', 'staff.department'])
            ->where('month_year', $monthYear)
            ->when($request->department_id, fn($q, $v) =>
                $q->whereHas('staff', fn($q) => $q->where('department_id', $v)))
            ->when($request->status, fn($q, $v) => $q->where('status', $v))
            ->orderBy('created_at')
            ->paginate(30)
            ->withQueryString();

        $summary   = $this->payrollService->monthlySummary($monthYear);
        $generated = Payroll::where('month_year', $monthYear)->exists();

        return view('payroll.index', compact(
            'payrolls', 'monthYear', 'summary', 'generated', 'year'
        ));
    }

    // ── Show generate form ─────────────────────────────────
    public function showGenerate()
    {
        $this->authorize('payroll.generate');
        $staffCount = Staff::active()->count();
        return view('payroll.generate', compact('staffCount'));
    }

    // ── Batch generate for all staff ───────────────────────
    public function generate(Request $request)
    {
        $this->authorize('payroll.generate');

        $validated = $request->validate([
            'month_year'   => 'required|date_format:Y-m',
            'working_days' => 'required|integer|min:1|max:31',
        ]);

        $year   = AcademicYear::current();
        $result = $this->payrollService->generateBatch(
            $year,
            $validated['month_year'],
            (int) $validated['working_days'],
        );

        return redirect()
            ->route('admin.payroll.index', ['month_year' => $validated['month_year']])
            ->with('success', "Payroll generated: {$result['created']} new, {$result['skipped']} already existed.");
    }

    // ── Show single payroll detail ─────────────────────────
    public function show(Payroll $payroll)
    {
        $this->authorize('payroll.view');
        $payroll->load(['staff.user', 'staff.department', 'academicYear', 'approvedBy']);
        return view('payroll.detail', compact('payroll'));
    }

    // ── Approve payroll ────────────────────────────────────
    public function approve(Payroll $payroll)
    {
        $this->authorize('payroll.approve');

        if ($payroll->status !== 'draft') {
            return back()->with('error', 'Only draft payrolls can be approved.');
        }

        $payroll->update([
            'status'      => 'approved',
            'approved_by' => auth()->id(),
        ]);

        return back()->with('success', 'Payroll approved.');
    }

    // ── Approve ALL drafts for a month ─────────────────────
    public function approveAll(Request $request)
    {
        $this->authorize('payroll.approve');

        $monthYear = $request->validate([
            'month_year' => 'required|date_format:Y-m',
        ])['month_year'];

        $count = Payroll::where('month_year', $monthYear)
            ->where('status', 'draft')
            ->update([
                'status'      => 'approved',
                'approved_by' => auth()->id(),
            ]);

        return back()->with('success', "{$count} payrolls approved.");
    }

    // ── Mark as PAID ───────────────────────────────────────
    public function markPaid(Request $request, Payroll $payroll)
    {
        $this->authorize('payroll.mark_paid');

        $validated = $request->validate([
            'payment_mode' => 'required|in:bank_transfer,cash,cheque',
            'paid_on'      => 'required|date',
        ]);

        $payroll->update(array_merge($validated, ['status' => 'paid']));

        return back()->with('success', 'Salary marked as paid.');
    }

    // ── Generate & stream payslip PDF ─────────────────────
    public function payslip(Payroll $payroll)
    {
        $this->authorize('payroll.view');

        // Staff can only view their own payslip
        if (auth()->user()->hasRole('teacher')) {
            $staffId = auth()->user()->staff?->id;
            if ($staffId !== $payroll->staff_id) {
                abort(403);
            }
        }

        $payroll->load(['staff.user', 'staff.department', 'academicYear', 'approvedBy']);

        $pdf = Pdf::loadView('payroll.payslip-pdf', compact('payroll'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'dpi'                  => 150,
                'defaultFont'          => 'sans-serif',
                'isHtml5ParserEnabled' => true,
            ]);

        $filename = 'Payslip-' . $payroll->staff->employee_id . '-' . $payroll->month_year . '.pdf';

        return $pdf->stream($filename);
    }

    // ── Edit individual adjustments (TDS, Loan etc.) ───────
    public function update(Request $request, Payroll $payroll)
    {
        $this->authorize('payroll.generate');

        if ($payroll->status !== 'draft') {
            return back()->with('error', 'Cannot edit approved/paid payroll.');
        }

        $validated = $request->validate([
            'present_days'   => 'required|integer|min:0',
            'tds_deduction'  => 'nullable|numeric|min:0',
            'loan_deduction' => 'nullable|numeric|min:0',
            'other_deduction'=> 'nullable|numeric|min:0',
            'remarks'        => 'nullable|string|max:200',
        ]);

        // Recalculate with new values
        $recalculated = $this->payrollService->calculate(
            $payroll->staff,
            workingDays:    $payroll->working_days,
            presentDays:    (int) $validated['present_days'],
            tdsDeduction:   (float) ($validated['tds_deduction']   ?? 0),
            loanDeduction:  (float) ($validated['loan_deduction']  ?? 0),
            otherDeduction: (float) ($validated['other_deduction'] ?? 0),
        );

        $payroll->update(array_merge($recalculated, [
            'remarks' => $validated['remarks'] ?? null,
        ]));

        return back()->with('success', 'Payroll recalculated and saved.');
    }
}