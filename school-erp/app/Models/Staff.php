<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Staff extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'department_id', 'employee_id', 'name',
        'date_of_birth', 'gender', 'photo', 'aadhar_number',
        'pan_number', 'qualification', 'designation', 'staff_type',
        'joining_date', 'relieving_date', 'basic_salary', 'da_percent',
        'hra_percent', 'other_allowance', 'pf_percent', 'bank_account',
        'bank_name', 'bank_ifsc', 'status',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth'   => 'date',
            'joining_date'    => 'date',
            'relieving_date'  => 'date',
            'basic_salary'    => 'decimal:2',
            'da_percent'      => 'decimal:2',
            'hra_percent'     => 'decimal:2',
            'pf_percent'      => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }

    public function examsCreated()
    {
        return $this->hasMany(Exam::class, 'created_by', 'user_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeTeaching($query)
    {
        return $query->where('staff_type', 'teaching');
    }

    // Calculate gross salary
    public function getGrossSalaryAttribute(): float
    {
        $da  = $this->basic_salary * ($this->da_percent / 100);
        $hra = $this->basic_salary * ($this->hra_percent / 100);
        return $this->basic_salary + $da + $hra + $this->other_allowance;
    }
}
