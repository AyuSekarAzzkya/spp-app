<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller

{
    public function student()
    {
        $user = Auth::user();
        $student = $user->student;

        if (!$student) {
            return redirect()->back()->with('error', 'Profil siswa tidak ditemukan.');
        }

        // 1. Ambil semua tagihan belum dibayar (untuk list & hitung total)
        $unpaidBills = Bill::with('sppRate')
            ->where('student_id', $student->id)
            ->where('status', 'unpaid')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        // 2. Hitung statistik untuk Stat Cards
        $totalUnpaid = $unpaidBills->sum(function ($bill) {
            return $bill->sppRate->amount;
        });

        $unpaidCount = $unpaidBills->count();

        // 3. Cek status bulan berjalan (Januari = 1, dst)
        $currentMonth = now()->month;
        $currentYear = now()->year;
        $isPaidThisMonth = !Bill::where('student_id', $student->id)
            ->where('month', $currentMonth)
            ->where('year', $currentYear)
            ->where('status', 'unpaid')
            ->exists();

        // 4. Pembayaran terakhir
        $latestPayment = Payment::where('student_id', $student->id)
            ->latest()
            ->first();

        // 5. Pembayaran bermasalah (Ditolak)
        $rejectedPayments = Payment::where('student_id', $student->id)
            ->where('status', 'rejected')
            ->latest()
            ->get();

        // 6. Ambil Tahun Ajaran Aktif (opsional, jika ada tabel AcademicYear)
        // Diasumsikan Anda memiliki relasi atau cara menentukan tahun aktif
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
        return view('dashboard.petugas');
    }

    public function admin()
    {
        return view('admin.dashboard.index');
    }
}
