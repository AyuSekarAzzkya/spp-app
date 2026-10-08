<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bill extends Model
{
    use SoftDeletes;

    protected $casts = [
        'due_date' => 'date',
        'paid_at'  => 'datetime',
        'month'    => 'integer',
        'year'     => 'integer',
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
        return $this->hasOneThrough(
            AcademicYear::class,
            SppRate::class,
            'id',               // Foreign key di SppRate (spp_rates.id)
            'id',               // Foreign key di AcademicYear (academic_years.id)
            'spp_rate_id',      // Local key di Bill (bills.spp_rate_id)
            'academic_year_id'  // Local key di SppRate (spp_rates.academic_year_id)
        );
    }

    public function paymentDetails()
    {
        return $this->hasMany(PaymentDetail::class);
    }

    public function payments()
    {
        return $this->hasManyThrough(
            Payment::class,
            PaymentDetail::class,
            'bill_id',      // Foreign key di PaymentDetail
            'id',           // Foreign key di Payment
            'id',           // Local key di Bill
            'payment_id'    // Local key di PaymentDetail
        );
    }

    public function scopeUnpaid($query)
    {
        return $query->where('status', 'unpaid');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope tagihan yang siap dibayar (unpaid dan tidak sedang dalam transaksi pending).
     */
    public function scopeAvailableForPayment($query)
    {
        return $query->where('status', 'unpaid')
            ->whereDoesntHave('paymentDetails.payment', function ($q) {
                $q->where('status', 'pending');
            });
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isUnpaid(): bool
    {
        return $this->status === 'unpaid';
    }

    public function hasPendingPayment(): bool
    {
        return $this->paymentDetails()
            ->whereHas('payment', fn($q) => $q->where('status', 'pending'))
            ->exists();
    }

    public function getMonthNameAttribute(): string
    {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        return $months[$this->month] ?? (string)$this->month;
    }
}
