<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Payment;
use App\Models\Student;
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

        $unpaidBills = Bill::with('sppRate')
            ->where('student_id', $student->id)
            ->where('status', 'unpaid')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $totalUnpaid = $unpaidBills->sum(fn($bill) => $bill->sppRate->amount);
        $unpaidCount = $unpaidBills->count();

        $currentMonth = now()->month;
        $currentYear = now()->year;

        $isPaidThisMonth = !Bill::where('student_id', $student->id)
            ->where('month', $currentMonth)
            ->where('year', $currentYear)
            ->where('status', 'unpaid')
            ->exists();

        $latestPayment = Payment::where('student_id', $student->id)
            ->latest()
            ->first();

        $rejectedPayments = Payment::where('student_id', $student->id)
            ->where('status', 'rejected')
            ->latest()
            ->get();

        $activeYear = \App\Models\AcademicYear::where('is_active', true)->first();

        return view('student.dashboard.index', compact(
            'unpaidBills',
            'totalUnpaid',
            'unpaidCount',
            'isPaidThisMonth',
            'latestPayment',
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

        $todayTransactionsCount = Payment::whereDate('created_at', $today)
            ->whereIn('status', ['approved', 'pending'])
            ->count();

        $todayRevenue = Payment::whereDate('created_at', $today)
            ->where('status', 'approved')
            ->with('proofs')
            ->get()
            ->sum(function ($payment) {
                return $payment->proofs->sum('amount');
            });

        $recentActivities = Payment::with(['student.class', 'proofs'])
            ->latest()
            ->limit(10)
            ->get();

        return view('petugas.dashboard.index', compact(
            'pendingPaymentsCount',
            'arrearsCount',
            'todayTransactionsCount',
            'todayRevenue',
            'recentActivities'
        ));
    }


    public function admin()
    {
        $totalRevenueMonth = Payment::join('payment_proofs', 'payments.id', '=', 'payment_proofs.payment_id')
            ->where('payments.status', 'approved')
            ->whereMonth('payment_proofs.created_at', now()->month)
            ->whereYear('payment_proofs.created_at', now()->year)
            ->sum('payment_proofs.amount');

        $totalArrears = Bill::join('spp_rates', 'bills.spp_rate_id', '=', 'spp_rates.id')
            ->where('bills.status', 'unpaid')
            ->sum('spp_rates.amount');

        $totalStudents = Student::count();
        $pendingApprovals = Payment::where('status', 'pending')->count();

        $rawMonthlyRevenue = Payment::join('payment_proofs', 'payments.id', '=', 'payment_proofs.payment_id')
            ->where('payments.status', 'approved')
            ->whereBetween('payment_proofs.created_at', [
                now()->subMonths(5)->startOfMonth(),
                now()->endOfMonth()
            ])
            ->selectRaw('
                MONTH(payment_proofs.created_at) as month,
                YEAR(payment_proofs.created_at) as year,
                SUM(payment_proofs.amount) as total
            ')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $monthlyRevenue = collect(range(0, 5))->map(function ($i) use ($rawMonthlyRevenue) {
            $date = now()->subMonths(5 - $i);

            $data = $rawMonthlyRevenue
                ->where('month', $date->month)
                ->where('year', $date->year)
                ->first();

            return [
                'label' => $date->translatedFormat('M'),
                'total' => $data->total ?? 0
            ];
        });

        $classDistribution = Student::select('class_id', DB::raw('count(*) as total'))
            ->with('class')
            ->groupBy('class_id')
            ->get();

        return view('admin.dashboard.index', compact(
            'totalRevenueMonth',
            'totalArrears',
            'totalStudents',
            'pendingApprovals',
            'monthlyRevenue',
            'classDistribution'
        ));
    }
}
