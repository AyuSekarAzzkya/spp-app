<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bill extends Model
{
    use SoftDeletes;

    protected $casts = [
        'due_date' => 'datetime',
    ];
    protected $fillable = [
        'student_id',
        'spp_rate_id',
        'month',
        'year',
        'due_date',
        'status',
        'paid_at',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function sppRate()
    {
        return $this->belongsTo(SppRate::class, 'spp_rate_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }
}
