<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'academic_year_id', 'school_class_id', 'section_id',
        'parent_user_id', 'admission_no', 'emis_number', 'name',
        'father_name', 'mother_name', 'date_of_birth', 'gender',
        'blood_group', 'photo', 'community', 'caste', 'religion',
        'mother_tongue', 'parent_mobile', 'alt_mobile', 'address',
        'pincode', 'aadhar_number', 'ration_card_no', 'roll_number',
        'admission_date', 'status', 'uses_transport',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth'  => 'date',
            'admission_date' => 'date',
            'uses_transport' => 'boolean',
        ];
    }

    // ── Relationships ──────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parentUser()
    {
        return $this->belongsTo(User::class, 'parent_user_id');
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function examResults()
    {
        return $this->hasMany(ExamResult::class);
    }

    public function feePayments()
    {
        return $this->hasMany(FeePayment::class);
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class);
    }

    public function homeworkSubmissions()
    {
        return $this->hasMany(HomeworkSubmission::class);
    }

    public function promotions()
    {
        return $this->hasMany(StudentPromotion::class);
    }

    public function transport()
    {
        return $this->hasOne(StudentTransport::class);
    }

    // ── Scopes ─────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForClass($query, int $classId)
    {
        return $query->where('school_class_id', $classId);
    }

    public function scopeForSection($query, int $sectionId)
    {
        return $query->where('section_id', $sectionId);
    }

    public function scopeCurrentYear($query)
    {
        $year = AcademicYear::current();
        return $query->where('academic_year_id', $year->id);
    }

    // ── Helpers ────────────────────────────────────────

    public function getPhotoUrlAttribute(): string
    {
        return $this->photo
            ? asset('storage/' . $this->photo)
            : asset('images/default-student.png');
    }

    public function getAgeAttribute(): int
    {
        return $this->date_of_birth->age;
    }

    public function getClassSectionAttribute(): string
    {
        return optional($this->schoolClass)->name
             . ' - '
             . optional($this->section)->name;
    }

    // Total pending fee amount
    public function getPendingFeeAttribute(): float
    {
        return $this->feePayments()
            ->where('status', 'pending')
            ->sum('amount_due');
    }
}