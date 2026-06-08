<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Department extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'head_id', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function head()
    {
        return $this->belongsTo(User::class, 'head_id');
    }

    public function staff()
    {
        return $this->hasMany(Staff::class);
    }
}
