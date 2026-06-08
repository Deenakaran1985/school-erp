<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeStructure extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_year_id', 'school_class_id', 'fee_head',
        'amount', 'term', 'due_date', 'is_optional',
    ];

    protected function casts(): array
    {
        return [
            'amount'      => 'decimal:2',
            'due_date'    => 'date',
            'is_optional' => 'boolean',
        ];
    }

    public function academicYear() { return $this->belongsTo(AcademicYear::class); }
    public function schoolClass()  { return $this->belongsTo(SchoolClass::class); }

    public function payments()
    {
        return $this->hasMany(FeePayment::class);
    }
}
