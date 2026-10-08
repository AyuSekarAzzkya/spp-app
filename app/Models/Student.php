<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;

class Student extends Model
{
    use SoftDeletes;

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

    public function payments()
    {
        return $this->hasMany(Payment::class, 'student_id', 'id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    protected static function booted()
    {
        static::created(function ($student) {
            // Jika user_id sudah diset, tidak perlu membuat user otomatis
            if ($student->user_id) {
                return;
            }

            $email = $student->nis . '@siswa.sekolah.id';

            // Fallback jika email tersebut sudah ada
            if (User::where('email', $email)->exists()) {
                $email = $student->nis . '.' . time() . '@siswa.sekolah.id';
            }

            $user = User::create([
                'name'     => $student->name,
                'email'    => $email,
                'password' => Hash::make($student->nis),
                'role'     => 'siswa',
            ]);

            // Assign role Spatie jika role 'siswa' terdaftar
            if (function_exists('spatie_permission_exists') || method_exists($user, 'assignRole')) {
                try {
                    $user->assignRole('siswa');
                } catch (\Throwable $e) {
                    // Ignore if role not seeded yet
                }
            }

            $student->updateQuietly([
                'user_id' => $user->id,
            ]);
        });
    }
}
