<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'student_id',
        'verified_by',
        'payment_date',
        'status',
        'note',
        'verified_at',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'verified_at'  => 'datetime',
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

    public function proofs()
    {
        return $this->hasMany(PaymentProof::class, 'payment_id')->orderBy('version', 'desc');
    }

    public function latestProof()
    {
        return $this->hasOne(PaymentProof::class, 'payment_id')->latestOfMany('version');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Total riil pembayaran berdasarkan rincian tagihan (bukan dari input manual bukti).
     */
    public function getTotalAmountAttribute(): int
    {
        return (int) $this->details->sum('amount');
    }
}
