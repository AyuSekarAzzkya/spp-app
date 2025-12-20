<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcademicYear extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'year',
        'semester',
        'is_active',
    ];

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function sppRates()
    {
        return $this->hasMany(SppRate::class);
    }
}

