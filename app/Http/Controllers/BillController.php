<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Bill;
use App\Models\Payment;
use App\Models\SppRate;
use App\Models\Student;

class BillController extends Controller
{
    public function students()
    {
        $activeYear = AcademicYear::where('is_active', true)->first();

        if (!$activeYear) {
            return back()->with('error', 'Tahun ajaran aktif belum diset.');
        }

        $sppRates = SppRate::where('academic_year_id', $activeYear->id)->get();

        if ($sppRates->isEmpty()) {
            return back()->with('error', 'SPP Rate untuk tahun ajaran aktif belum diset.');
        }

        $students = Student::with('class')->orderBy('name')->get();

        return view('admin.bills.students', compact('students', 'activeYear', 'sppRates'));
    }

    public function generateAllBills()
    {
        $activeYear = AcademicYear::where('is_active', true)->first();

        if (!$activeYear) {
            return back()->with('error', 'Tahun ajaran aktif tidak ditemukan.');
        }

        $sppRate = SppRate::where('academic_year_id', $activeYear->id)->first();

        if (!$sppRate) {
            return back()->with('error', 'SPP Rate untuk tahun ajaran aktif belum diset.');
        }

        $students = Student::all();

        if ($students->isEmpty()) {
            return back()->with('error', 'Tidak ada siswa terdaftar.');
        }

        $yearParts = explode('/', $activeYear->year);
        $billingYear = intval($yearParts[0]);

        $createdCount = 0;

        foreach ($students as $student) {
            for ($month = 1; $month <= 12; $month++) {

                $exists = Bill::where('student_id', $student->id)
                    ->where('month', $month)
                    ->where('year', $billingYear)
                    ->exists();

                if ($exists) continue;

                Bill::create([
                    'student_id'  => $student->id,
                    'spp_rate_id' => $sppRate->id,
                    'month'       => $month,
                    'year'        => $billingYear,
                    'due_date'    => now()->setDate($billingYear, $month, 10),
                    'status'      => 'unpaid',
                ]);

                $createdCount++;
            }
        }

        return back()->with('success', "Generate selesai. Total tagihan baru: {$createdCount}");
    }

    public function index($studentId)
    {
        $student = Student::with('class')->findOrFail($studentId);
        $activeYear = AcademicYear::where('is_active', true)->first();

        $sppRates = SppRate::where('academic_year_id', $activeYear->id)->get();
        $sppRate  = SppRate::where('academic_year_id', $activeYear->id)->first(); 
        
        $bills = Bill::where('student_id', $studentId)
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        return view('admin.bills.index', compact(
            'student',
            'bills',
            'activeYear',
            'sppRates',
            'sppRate'
        ));
    }

    public function showDetail($id) 
    {
        $bill = Bill::findOrFail($id);
        $payment = Payment::with(['student', 'details.bill.academicYear'])
            ->whereHas('details', function ($q) use ($id) {
                $q->where('bill_id', $id);
            })->first();

        if (!$payment) {
            return back()->with('error', 'Transaksi pembayaran untuk tagihan ini tidak ditemukan.');
        }

        return view('admin.bills.show', compact('payment'));
    }
}
