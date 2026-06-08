<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class ExamType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'code', 'weightage_percent',
        'max_marks', 'pass_marks', 'counts_for_promotion', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'weightage_percent'    => 'decimal:2',
            'counts_for_promotion' => 'boolean',
        ];
    }

    public function exams()
    {
        return $this->hasMany(Exam::class);
    }
}