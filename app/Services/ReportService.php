<?php

namespace App\Services;

use App\Models\Bill;
use App\Models\Payment;
use App\Models\PaymentDetail;
use App\Models\StudentClass;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * Query dasar laporan transaksi pembayaran yang teroptimasi.
     */
    public function getPaymentsQuery(array $filters = []): Builder
    {
        $query = Payment::with(['student.class', 'verifier', 'details.bill.sppRate', 'latestProof'])
            ->latest('payment_date');

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['date'])) {
            $query->whereDate('payment_date', $filters['date']);
        }

        if (!empty($filters['month'])) {
            $query->whereMonth('payment_date', $filters['month']);
        }

        if (!empty($filters['year'])) {
            $query->whereYear('payment_date', $filters['year']);
        }

        if (!empty($filters['class_id'])) {
            $query->whereHas('student', function ($q) use ($filters) {
                $q->where('class_id', $filters['class_id']);
            });
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    /**
     * Hitung total penerimaan riil HANYA dari transaksi yang APPROVED.
     */
    public function calculateTotalApprovedRevenue(array $filters = []): int
    {
        $filters['status'] = 'approved';
        $paymentIds = $this->getPaymentsQuery($filters)->pluck('id');

        return (int) PaymentDetail::whereIn('payment_id', $paymentIds)->sum('amount');
    }

    /**
     * Query laporan tunggakan siswa (mendukung tunggakan multi-tahun).
     */
    public function getArrearsQuery(array $filters = []): Builder
    {
        $query = Bill::with(['student.class', 'sppRate'])
            ->where('status', 'unpaid');

        if (!empty($filters['class_id'])) {
            $query->whereHas('student', function ($q) use ($filters) {
                $q->where('class_id', $filters['class_id']);
            });
        }

        if (!empty($filters['academic_year_id'])) {
            $query->whereHas('sppRate', function ($q) use ($filters) {
                $q->where('academic_year_id', $filters['academic_year_id']);
            });
        }

        if (!empty($filters['year'])) {
            $query->where('year', $filters['year']);
        }

        if (!empty($filters['student_id'])) {
            $query->where('student_id', $filters['student_id']);
        }

        return $query;
    }

    /**
     * Rekapitulasi pembayaran per kelas.
     */
    public function getClassRecap(): Collection
    {
        $classes = StudentClass::withCount(['students as total_students'])
            ->orderBy('grade_level')
            ->orderBy('name')
            ->get();

        return $classes->map(function ($class) {
            $studentIds = $class->students()->pluck('id');

            // Total tagihan unpaid (tunggakan)
            $totalArrears = (int) Bill::join('spp_rates', 'bills.spp_rate_id', '=', 'spp_rates.id')
                ->whereIn('bills.student_id', $studentIds)
                ->where('bills.status', 'unpaid')
                ->sum('spp_rates.amount');

            // Total pembayaran approved
            $totalPaid = (int) PaymentDetail::join('payments', 'payment_details.payment_id', '=', 'payments.id')
                ->whereIn('payments.student_id', $studentIds)
                ->where('payments.status', 'approved')
                ->sum('payment_details.amount');

            // Siswa yang memiliki tunggakan
            $studentsInArrearsCount = Bill::whereIn('student_id', $studentIds)
                ->where('status', 'unpaid')
                ->distinct('student_id')
                ->count('student_id');

            return [
                'class_id'             => $class->id,
                'class_name'           => $class->name,
                'grade_level'          => $class->grade_level,
                'major'                => $class->major,
                'total_students'       => $class->total_students,
                'students_in_arrears'  => $studentsInArrearsCount,
                'students_all_paid'    => max(0, $class->total_students - $studentsInArrearsCount),
                'total_paid_revenue'   => $totalPaid,
                'total_arrears_amount' => $totalArrears,
            ];
        });
    }

    /**
     * Rekapitulasi per bulan dalam 1 tahun kalender tertentu.
     */
    public function getMonthlyRecap(int $year): Collection
    {
        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        return collect(range(1, 12))->map(function ($month) use ($year, $monthNames) {
            $payments = Payment::whereYear('payment_date', $year)
                ->whereMonth('payment_date', $month)
                ->get();

            $approvedPayments = $payments->where('status', 'approved');
            $approvedPaymentIds = $approvedPayments->pluck('id');

            $revenue = (int) PaymentDetail::whereIn('payment_id', $approvedPaymentIds)->sum('amount');

            return [
                'month'          => $month,
                'month_name'     => $monthNames[$month],
                'year'           => $year,
                'total_approved' => $approvedPayments->count(),
                'total_pending'  => $payments->where('status', 'pending')->count(),
                'total_rejected' => $payments->where('status', 'rejected')->count(),
                'revenue'        => $revenue,
            ];
        });
    }
}
