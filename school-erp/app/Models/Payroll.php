<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Payroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id', 'academic_year_id', 'month_year',
        'working_days', 'present_days', 'leave_days',
        'basic_salary', 'da_amount', 'hra_amount', 'other_allowance',
        'gross_salary', 'pf_deduction', 'esi_deduction',
        'tds_deduction', 'loan_deduction', 'other_deduction',
        'total_deduction', 'net_salary', 'status',
        'payment_mode', 'paid_on', 'approved_by', 'remarks',
    ];

    protected function casts(): array
    {
        return [
            'basic_salary'    => 'decimal:2',
            'gross_salary'    => 'decimal:2',
            'net_salary'      => 'decimal:2',
            'total_deduction' => 'decimal:2',
            'paid_on'         => 'date',
        ];
    }

    public function staff()        { return $this->belongsTo(Staff::class); }
    public function academicYear() { return $this->belongsTo(AcademicYear::class); }
    public function approvedBy()  { return $this->belongsTo(User::class, 'approved_by'); }

    public function scopeDraft($query)    { return $query->where('status', 'draft'); }
    public function scopeApproved($query) { return $query->where('status', 'approved'); }
    public function scopePaid($query)     { return $query->where('status', 'paid'); }

    public function scopeForMonth($query, string $monthYear)
    {
        return $query->where('month_year', $monthYear);
    }
}
