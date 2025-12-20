<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SppRate extends Model
{
    protected $fillable = [
        'academic_year_id',
        'amount',
        'description',
    ];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function bills()
    {
        return $this->hasMany(Bill::class);
    }
}

