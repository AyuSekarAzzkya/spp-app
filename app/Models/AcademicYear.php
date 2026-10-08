<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcademicYear extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'year',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function sppRates()
    {
        return $this->hasMany(SppRate::class);
    }

    public function sppRate()
    {
        return $this->hasOne(SppRate::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
