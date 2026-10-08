<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Bill;
use App\Models\Payment;
use App\Models\SppRate;
use App\Models\Student;
use App\Services\BillGenerationService;
use Illuminate\Http\Request;

class BillController extends Controller
{
    public function students()
    {
        $activeYear = AcademicYear::where('is_active', 1)->first();

        if (!$activeYear) {
            return back()->with('error', 'Tahun ajaran aktif belum diset.');
        }

        $sppRates = SppRate::where('academic_year_id', $activeYear->id)->get();

        if ($sppRates->isEmpty()) {
            return back()->with('error', 'Tarif SPP untuk tahun ajaran aktif belum diset.');
        }

        $students = Student::with(['class', 'academicYear'])
            ->withCount([
                'bills as unpaid_bills_count' => fn($q) => $q->where('status', 'unpaid'),
                'bills as paid_bills_count' => fn($q) => $q->where('status', 'paid'),
            ])
            ->orderBy('name')
            ->get();

        return view('admin.bills.students', compact('students', 'activeYear', 'sppRates'));
    }

    public function generateAllBills(BillGenerationService $service, Request $request)
    {
        try {
            $month = $request->input('month', now()->month);
            $year = $request->input('year', now()->year);

            $result = $service->generateMonthlyBills((int)$month, (int)$year);

            return back()->with('success', "Generate tagihan selesai: {$result['created_count']} tagihan baru dibuat, {$result['skipped_count']} tagihan sudah ada sebelumnya.");
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal men-generate tagihan: ' . $e->getMessage());
        }
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
        $payment = Payment::with(['student.class', 'details.bill.academicYear', 'proofs'])
            ->whereHas('details', function ($q) use ($id) {
                $q->where('bill_id', $id);
            })->first();

        if (!$payment) {
            return back()->with('error', 'Transaksi pembayaran untuk tagihan ini tidak ditemukan.');
        }

        return view('admin.bills.show', compact('payment'));
    }
}
