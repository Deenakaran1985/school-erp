<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Section extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_class_id', 'name', 'medium',
        'max_strength', 'class_teacher_id',
    ];

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function classTeacher()
    {
        return $this->belongsTo(User::class, 'class_teacher_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class)->where('status', 'active');
    }

    public function timetables()
    {
        return $this->hasMany(Timetable::class);
    }

    public function getLabelAttribute(): string
    {
        return $this->schoolClass->name . ' - ' . $this->name;
    }
}