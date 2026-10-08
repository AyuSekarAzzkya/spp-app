<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SppRate extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'academic_year_id',
        'amount',
        'description',
    ];

    protected $casts = [
        'amount' => 'integer',
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
