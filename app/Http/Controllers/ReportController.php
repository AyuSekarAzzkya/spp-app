<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\StudentClass;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with('student')->withSum('proofs as total_amount', 'amount');

        if ($request->filled('date')) {
            $query->whereDate('payment_date', $request->date);
        }

        if ($request->filled('month')) {
            $query->whereMonth('payment_date', $request->month);
            $query->whereYear('payment_date', $request->year ?? now()->year);
        } elseif ($request->filled('year')) {
            $query->whereYear('payment_date', $request->year);
        }

        $payments = $query->latest('payment_date')->get();
        $total = $payments->sum('total_amount');

        return view('admin.reports.index', compact('payments', 'total'));
    }

    public function arrears(Request $request)
    {
        $classes = StudentClass::orderBy('name')->get();

        $query = Bill::with(['student.class'])
            ->where('status', '!=', 'paid');

        if ($request->filled('class_id')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('class_id', $request->class_id);
            });
        }

        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        $arrears = $query
            ->selectRaw('student_id, year, COUNT(*) as total_months')
            ->groupBy('student_id', 'year')
            ->get();

        return view('admin.reports.arrears_report', compact(
            'arrears',
            'classes'
        ));
    }


    public function arrearsDetail(Request $request, $studentId)
    {
        $query = Bill::with(['student.class', 'sppRate'])
            ->where('student_id', $studentId)
            ->where('status', '!=', 'paid');

        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        $bills = $query
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $student = $bills->first()?->student;
        $totalTunggakan = $bills->sum(fn($bill) => $bill->sppRate->amount ?? 0);

        return view('admin.reports.arrears_detail', compact(
            'bills',
            'student',
            'totalTunggakan'
        ));
    }

}
