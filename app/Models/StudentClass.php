<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentClass extends Model
{   
    use SoftDeletes;
    protected $table = 'classes';

    protected $fillable = [
        'name',
        'major',
        'grade_level',
    ];

    public function students()
    {
        return $this->hasMany(Student::class, 'class_id');
    }
}
