<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Expense;
use App\Models\FeePayment;
use App\Models\Payroll;
use App\Models\SchoolNotification;
use App\Models\Staff;
use App\Models\Student;
use Illuminate\Http\Request;

class CorrespondentApiController extends Controller
{
    // GET /api/correspondent/dashboard
    public function dashboard(Request $request)
    {
        $year = AcademicYear::current();

        $totalStudents  = Student::currentYear()->active()->count();
        $totalStaff     = Staff::active()->count();
        $monthYear      = now()->format('Y-m');

        $feeThisMonth   = FeePayment::paid()
            ->whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->sum('amount_paid');

        $pendingPayroll = Payroll::where('month_year', $monthYear)
            ->where('status', 'approved')
            ->sum('net_salary');

        $expenseThisMonth = Expense::where('status', 'approved')
            ->whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year)
            ->sum('amount');

        $classWiseStrength = Student::currentYear()
            ->active()
            ->with('schoolClass')
            ->get()
            ->groupBy('school_class_id')
            ->map(fn($g) => [
                'class' => $g->first()->schoolClass?->name,
                'count' => $g->count(),
            ])
            ->values();

        return response()->json([
            'success' => true,
            'data'    => [
                'academic_year'    => $year->label,
                'total_students'   => $totalStudents,
                'total_staff'      => $totalStaff,
                'fee_collected'    => (float) $feeThisMonth,
                'payroll_pending'  => (float) $pendingPayroll,
                'expenses'         => (float) $expenseThisMonth,
                'net_income'       => (float) ($feeThisMonth - $expenseThisMonth),
                'class_strength'   => $classWiseStrength,
            ],
        ]);
    }

    // GET /api/correspondent/fee-summary
    public function feeSummary(Request $request)
    {
        $year  = AcademicYear::current();
        $month = $request->input('month', now()->format('Y-m'));
        [$y, $m] = explode('-', $month);

        $collected = FeePayment::paid()
            ->whereYear('payment_date', $y)
            ->whereMonth('payment_date', $m)
            ->selectRaw('payment_mode, SUM(amount_paid) as total, COUNT(*) as count')
            ->groupBy('payment_mode')
            ->get();

        $totalDue = \App\Models\FeeStructure::where('academic_year_id', $year->id)->sum('amount')
            * Student::currentYear()->active()->count();

        return response()->json([
            'success'   => true,
            'month'     => $month,
            'collected' => $collected->map(fn($r) => [
                'mode'  => $r->payment_mode,
                'total' => (float) $r->total,
                'count' => $r->count,
            ]),
            'total_collected' => (float) FeePayment::paid()
                ->whereYear('payment_date', $y)
                ->whereMonth('payment_date', $m)
                ->sum('amount_paid'),
        ]);
    }

    // GET /api/correspondent/payroll-summary?month=2025-06
    public function payrollSummary(Request $request)
    {
        $month = $request->input('month_year', now()->format('Y-m'));

        $payrolls = Payroll::with('staff.department')
            ->where('month_year', $month)
            ->get();

        $summary = [
            'month'           => $month,
            'total_staff'     => $payrolls->count(),
            'gross_total'     => (float) $payrolls->sum('gross_salary'),
            'deduction_total' => (float) $payrolls->sum('total_deduction'),
            'net_total'       => (float) $payrolls->sum('net_salary'),
            'draft'           => $payrolls->where('status', 'draft')->count(),
            'approved'        => $payrolls->where('status', 'approved')->count(),
            'paid'            => $payrolls->where('status', 'paid')->count(),
        ];

        $byDept = $payrolls->groupBy('staff.department.name')
            ->map(fn($g, $dept) => [
                'department' => $dept ?? 'Unknown',
                'count'      => $g->count(),
                'net_total'  => (float) $g->sum('net_salary'),
            ])->values();

        return response()->json([
            'success'   => true,
            'summary'   => $summary,
            'by_dept'   => $byDept,
        ]);
    }

    // GET /api/correspondent/staff  — full staff list
    public function staffList(Request $request)
    {
        $staff = Staff::with(['user', 'department'])
            ->when($request->department_id, fn($q, $v) => $q->where('department_id', $v))
            ->when($request->status, fn($q, $v) => $q->where('status', $v))
            ->active()
            ->orderBy('name')
            ->paginate(30);

        return response()->json([
            'success' => true,
            'data'    => $staff->map(fn($s) => [
                'id'          => $s->id,
                'employee_id' => $s->employee_id,
                'name'        => $s->name,
                'designation' => $s->designation,
                'department'  => $s->department?->name,
                'staff_type'  => $s->staff_type,
                'phone'       => $s->user->phone,
                'status'      => $s->status,
            ]),
            'meta' => [
                'current_page' => $staff->currentPage(),
                'last_page'    => $staff->lastPage(),
                'total'        => $staff->total(),
            ],
        ]);
    }

    // GET /api/correspondent/expenses
    public function expenses(Request $request)
    {
        $expenses = Expense::with(['expenseHead', 'approvedBy'])
            ->when($request->status, fn($q, $v) => $q->where('status', $v))
            ->when($request->month, function ($q, $v) {
                [$y, $m] = explode('-', $v);
                $q->whereYear('expense_date', $y)->whereMonth('expense_date', $m);
            })
            ->orderByDesc('expense_date')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data'    => $expenses->map(fn($e) => [
                'id'           => $e->id,
                'expense_head' => $e->expenseHead?->name,
                'description'  => $e->description,
                'amount'       => (float) $e->amount,
                'expense_date' => $e->expense_date->format('d M Y'),
                'status'       => $e->status,
                'approved_by'  => $e->approvedBy?->name,
            ]),
            'meta' => [
                'current_page' => $expenses->currentPage(),
                'last_page'    => $expenses->lastPage(),
            ],
        ]);
    }

    // POST /api/correspondent/notifications/send
    public function sendNotification(Request $request)
    {
        $this->authorize('notification.send');

        $request->validate([
            'title'      => 'required|string|max:100',
            'body'       => 'required|string',
            'target_role'=> 'required|in:all,parent,student,teacher',
            'type'       => 'nullable|string|max:30',
        ]);

        $notification = SchoolNotification::create([
            'title'       => $request->title,
            'body'        => $request->body,
            'target_role' => $request->target_role,
            'type'        => $request->input('type', 'general'),
            'sent_by'     => $request->user()->id,
            'sent_at'     => now(),
        ]);

        // Dispatch FCM push in background
        dispatch(new \App\Jobs\SendResultNotification($notification->id, $request->target_role));

        return response()->json([
            'success' => true,
            'message' => 'Notification sent to ' . $request->target_role . '.',
        ], 201);
    }
}
