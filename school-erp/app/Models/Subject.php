<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_class_id', 'name', 'code', 'type',
        'is_core', 'max_marks', 'pass_marks',
        'sort_order', 'is_active',
    ];

    protected function casts(): array
    {
        return ['is_core' => 'boolean', 'is_active' => 'boolean'];
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function exams()
    {
        return $this->hasMany(Exam::class);
    }

    public function homework()
    {
        return $this->hasMany(Homework::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    public function scopeCore($query)
    {
        return $query->where('is_core', true);
    }
}