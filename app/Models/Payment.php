<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'student_id',
        'verified_by',
        'payment_date',
        'paid_amount',
        'proof_image',
        'status',
        'note',
        'verified_at'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function details()
    {
        return $this->hasMany(PaymentDetail::class);
    }
}
