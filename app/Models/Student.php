<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class Student extends Model
{
    protected $fillable = [
        'nis',
        'nisn',
        'name',
        'phone',
        'gender',
        'address',
        'class_id',
        'academic_year_id',
        'user_id',
        'status',
    ];

    public function class()
    {
        return $this->belongsTo(StudentClass::class, 'class_id')->withTrashed();
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bills()
    {
        return $this->hasMany(Bill::class);
    }

    protected static function booted()
    {
        static::created(function ($student) {
            $baseEmail = preg_replace('/[^a-z0-9]/', '', strtolower($student->name));

            $email = $baseEmail . '@sekolah.com';

            $counter = 1;
            while (User::where('email', $email)->exists()) {
                $email = $baseEmail . $counter . '@sekolah.com';
                $counter++;
            }

            $user = User::create([
                'name'     => $student->name,
                'email'    => $email,
                'password' => Hash::make($student->nis),
                'role'     => 'siswa',
            ]);

            $student->update([
                'user_id' => $user->id
            ]);
        });
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'student_id', 'id');
    }
}
