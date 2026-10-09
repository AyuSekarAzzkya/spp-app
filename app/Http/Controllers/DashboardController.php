<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Bill;
use App\Models\Payment;
use App\Models\PaymentDetail;
use App\Models\Student;
use App\Models\StudentClass;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function student()
    {
        $user = Auth::user();
        $student = $user->student;

        if (!$student) {
            return redirect()->back()->with('error', 'Profil siswa tidak ditemukan.');
        }

        $student->loadMissing(['class', 'academicYear']);

        $unpaidBills = Bill::with('sppRate')
            ->where('student_id', $student->id)
            ->where('status', 'unpaid')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $totalUnpaid = (int) $unpaidBills->sum(fn($bill) => $bill->sppRate->amount ?? 0);
        $unpaidCount = $unpaidBills->count();

        $paidBillsCount = Bill::where('student_id', $student->id)
            ->where('status', 'paid')
            ->count();

        $totalPaidAmount = (int) PaymentDetail::join('payments', 'payment_details.payment_id', '=', 'payments.id')
            ->where('payments.student_id', $student->id)
            ->where('payments.status', 'approved')
            ->sum('payment_details.amount');

        $currentMonth = now()->month;
        $currentYear = now()->year;

        $isPaidThisMonth = !Bill::where('student_id', $student->id)
            ->where('month', $currentMonth)
            ->where('year', $currentYear)
            ->where('status', 'unpaid')
            ->exists();

        $latestPayment = Payment::with(['details.bill', 'latestProof'])
            ->where('student_id', $student->id)
            ->latest()
            ->first();

        $pendingPaymentsCount = Payment::where('student_id', $student->id)
            ->where('status', 'pending')
            ->count();

        $rejectedPayments = Payment::where('student_id', $student->id)
            ->where('status', 'rejected')
            ->latest()
            ->get();

        $activeYear = AcademicYear::where('is_active', true)->first();

        return view('student.dashboard.index', compact(
            'student',
            'unpaidBills',
            'totalUnpaid',
            'unpaidCount',
            'paidBillsCount',
            'totalPaidAmount',
            'isPaidThisMonth',
            'latestPayment',
            'pendingPaymentsCount',
            'rejectedPayments',
            'activeYear'
        ));
    }

    public function petugas()
    {
        $today = Carbon::today();

        $pendingPaymentsCount = Payment::where('status', 'pending')->count();

        $arrearsCount = Bill::where('status', 'unpaid')
            ->whereDate('due_date', '<', $today)
            ->distinct('student_id')
            ->count('student_id');

        $todayTransactionsCount = Payment::whereDate('created_at', $today)->count();

        // Hitung pemasukan riil hari ini dari transaksi approved
        $todayRevenue = (int) PaymentDetail::join('payments', 'payment_details.payment_id', '=', 'payments.id')
            ->where('payments.status', 'approved')
            ->whereDate('payments.verified_at', $today)
            ->sum('payment_details.amount');

        // Hitung pemasukan bulan ini
        $thisMonthRevenue = (int) PaymentDetail::join('payments', 'payment_details.payment_id', '=', 'payments.id')
            ->where('payments.status', 'approved')
            ->whereMonth('payments.verified_at', now()->month)
            ->whereYear('payments.verified_at', now()->year)
            ->sum('payment_details.amount');

        $totalApproved = Payment::where('status', 'approved')->count();
        $totalRejected = Payment::where('status', 'rejected')->count();

        $pendingPayments = Payment::with(['student.class', 'details.bill', 'latestProof'])
            ->where('status', 'pending')
            ->latest()
            ->take(10)
            ->get();

        $recentActivities = Payment::with(['student.class', 'details.bill', 'latestProof'])
            ->latest()
            ->limit(10)
            ->get();

        return view('petugas.dashboard.index', compact(
            'pendingPaymentsCount',
            'arrearsCount',
            'todayTransactionsCount',
            'todayRevenue',
            'thisMonthRevenue',
            'totalApproved',
            'totalRejected',
            'pendingPayments',
            'recentActivities'
        ));
    }

    public function admin()
    {
        // 1. Total Pemasukan Bulan Ini (Riil dari transaksi approved)
        $totalRevenueMonth = (int) PaymentDetail::join('payments', 'payment_details.payment_id', '=', 'payments.id')
            ->where('payments.status', 'approved')
            ->whereMonth('payments.verified_at', now()->month)
            ->whereYear('payments.verified_at', now()->year)
            ->sum('payment_details.amount');

        // 2. Total Seluruh Pemasukan Sepanjang Waktu
        $totalRevenueAll = (int) PaymentDetail::join('payments', 'payment_details.payment_id', '=', 'payments.id')
            ->where('payments.status', 'approved')
            ->sum('payment_details.amount');

        // 3. Total Tunggakan SPP Saat Ini
        $totalArrears = (int) Bill::join('spp_rates', 'bills.spp_rate_id', '=', 'spp_rates.id')
            ->where('bills.status', 'unpaid')
            ->sum('spp_rates.amount');

        // 4. Agregasi Statistik Siswa (1 Query bukan 2)
        $studentStats = Student::selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active
        ")->first();
        $totalStudents  = $studentStats->total ?? 0;
        $activeStudents = (int) ($studentStats->active ?? 0);
        $totalClasses   = StudentClass::count();

        // 5. Agregasi Statistik Tagihan (1 Query bukan 3)
        $billStats = Bill::selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN status = 'paid' THEN 1 ELSE 0 END) as paid,
            SUM(CASE WHEN status = 'unpaid' THEN 1 ELSE 0 END) as unpaid
        ")->first();
        $totalBills       = $billStats->total ?? 0;
        $paidBillsCount   = (int) ($billStats->paid ?? 0);
        $unpaidBillsCount = (int) ($billStats->unpaid ?? 0);

        // 6. Agregasi Statistik Pembayaran (1 Query bukan 3)
        $paymentStats = Payment::selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected
        ")->first();
        $totalPayments         = $paymentStats->total ?? 0;
        $pendingApprovals      = (int) ($paymentStats->pending ?? 0);
        $rejectedPaymentsCount = (int) ($paymentStats->rejected ?? 0);

        // 7. Tren Pemasukan 6 Bulan Riil (1 Query GROUP BY menggantikan 6 JOIN queries dalam loop)
        $startRange = now()->subMonths(5)->startOfMonth();
        $dateFormat = DB::getDriverName() === 'sqlite'
            ? "strftime('%Y-%m', payments.verified_at)"
            : "DATE_FORMAT(payments.verified_at, '%Y-%m')";

        $rawMonthlyRevenue = PaymentDetail::join('payments', 'payment_details.payment_id', '=', 'payments.id')
            ->where('payments.status', 'approved')
            ->where('payments.verified_at', '>=', $startRange)
            ->selectRaw("{$dateFormat} as ym, SUM(payment_details.amount) as total")
            ->groupBy('ym')
            ->pluck('total', 'ym');

        $monthlyRevenue = collect(range(0, 5))->map(function ($i) use ($rawMonthlyRevenue) {
            $date = now()->subMonths(5 - $i);
            $ym = $date->format('Y-m');

            return [
                'label' => $date->translatedFormat('M Y'),
                'total' => (int) ($rawMonthlyRevenue[$ym] ?? 0),
            ];
        });

        // 8. Distribusi Siswa per Kelas
        $classDistribution = StudentClass::withCount('students')->get();

        // 9. Transaksi Pembayaran Terbaru
        $recentPayments = Payment::with(['student.class', 'details.bill', 'latestProof'])
            ->latest()
            ->take(6)
            ->get();

        return view('admin.dashboard.index', compact(
            'totalRevenueMonth',
            'totalRevenueAll',
            'totalArrears',
            'totalStudents',
            'activeStudents',
            'totalClasses',
            'totalBills',
            'paidBillsCount',
            'unpaidBillsCount',
            'pendingApprovals',
            'rejectedPaymentsCount',
            'totalPayments',
            'monthlyRevenue',
            'classDistribution',
            'recentPayments'
        ));
    }
}
