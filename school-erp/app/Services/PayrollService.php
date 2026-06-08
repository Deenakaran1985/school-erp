<?php
namespace App\Services;

use App\Models\AcademicYear;
use App\Models\Payroll;
use App\Models\Staff;
use Carbon\Carbon;

class PayrollService
{
    // ── Calculate salary components for one staff ──────────
    public function calculate(
        Staff  $staff,
        int    $workingDays,
        int    $presentDays,
        float  $tdsDeduction   = 0,
        float  $loanDeduction  = 0,
        float  $otherDeduction = 0,
    ): array {
        $basic  = (float) $staff->basic_salary;
        $leaveDays = max(0, $workingDays - $presentDays);

        // ── Earnings ───────────────────────────────────────────
        $da    = round($basic * ((float) $staff->da_percent  / 100), 2);
        $hra   = round($basic * ((float) $staff->hra_percent / 100), 2);
        $other = (float) $staff->other_allowance;
        $gross = round($basic + $da + $hra + $other, 2);

        // ── LOP (Loss of Pay) ──────────────────────────────────
        $lopPerDay  = $workingDays > 0 ? round($gross / $workingDays, 4) : 0;
        $lopAmount  = round($lopPerDay * $leaveDays, 2);
        $grossAfterLop = $gross - $lopAmount;

        // ── PF Deduction (on basic only per Indian rules) ──────
        $pf = round($basic * ((float) $staff->pf_percent / 100), 2);

        // ── ESI (only if gross ≤ ₹21,000) ─────────────────────
        $esi = $grossAfterLop <= 21000
            ? round($grossAfterLop * 0.0075, 2)
            : 0;

        $totalDeduction = round($lopAmount + $pf + $esi + $tdsDeduction + $loanDeduction + $otherDeduction, 2);
        $netSalary      = round($grossAfterLop - $pf - $esi - $tdsDeduction - $loanDeduction - $otherDeduction, 2);

        return [
            // Earnings
            'basic_salary'   => $basic,
            'da_amount'      => $da,
            'hra_amount'     => $hra,
            'other_allowance'=> $other,
            'gross_salary'   => $gross,
            // Attendance
            'working_days'   => $workingDays,
            'present_days'   => $presentDays,
            'leave_days'     => $leaveDays,
            // Deductions
            'lop_amount'     => $lopAmount,
            'pf_deduction'   => $pf,
            'esi_deduction'  => $esi,
            'tds_deduction'  => $tdsDeduction,
            'loan_deduction' => $loanDeduction,
            'other_deduction'=> $otherDeduction,
            'total_deduction'=> $totalDeduction,
            // Net
            'net_salary'     => $netSalary,
        ];
    }

    // ── Generate payroll for a single staff member ─────────
    public function generateForStaff(
        Staff        $staff,
        AcademicYear $year,
        string       $monthYear,
        int          $workingDays,
        int          $presentDays,
        array        $overrides = []
    ): Payroll {
        // Skip if already generated for this month
        $existing = Payroll::where('staff_id', $staff->id)
            ->where('month_year', $monthYear)
            ->first();

        if ($existing) {
            return $existing;
        }

        $data = $this->calculate(
            $staff,
            $workingDays,
            $presentDays,
            tdsDeduction:   $overrides['tds_deduction']   ?? 0,
            loanDeduction:  $overrides['loan_deduction']  ?? 0,
            otherDeduction: $overrides['other_deduction'] ?? 0,
        );

        return Payroll::create(array_merge($data, [
            'staff_id'         => $staff->id,
            'academic_year_id' => $year->id,
            'month_year'       => $monthYear,
            'status'           => 'draft',
        ]));
    }

    // ── Batch: generate for ALL active staff ───────────────
    public function generateBatch(
        AcademicYear $year,
        string       $monthYear,
        int          $workingDays = 26
    ): array {
        $staffList = Staff::active()->get();
        $created  = 0;
        $skipped  = 0;

        foreach ($staffList as $staff) {
            $exists = Payroll::where('staff_id', $staff->id)
                ->where('month_year', $monthYear)
                ->exists();

            if ($exists) { $skipped++; continue; }

            $this->generateForStaff(
                $staff, $year, $monthYear,
                workingDays: $workingDays,
                presentDays: $workingDays, // default full month; adjust per attendance
            );
            $created++;
        }

        return ['created' => $created, 'skipped' => $skipped];
    }

    // ── Monthly summary totals ─────────────────────────────
    public function monthlySummary(string $monthYear): array
    {
        $rows = Payroll::where('month_year', $monthYear)->get();

        return [
            'staff_count'      => $rows->count(),
            'total_gross'      => $rows->sum('gross_salary'),
            'total_deductions' => $rows->sum('total_deduction'),
            'total_net'        => $rows->sum('net_salary'),
            'total_pf'         => $rows->sum('pf_deduction'),
            'total_esi'        => $rows->sum('esi_deduction'),
            'draft_count'      => $rows->where('status', 'draft')->count(),
            'approved_count'   => $rows->where('status', 'approved')->count(),
            'paid_count'       => $rows->where('status', 'paid')->count(),
        ];
    }
}