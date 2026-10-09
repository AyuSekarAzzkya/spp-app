<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Support\Facades\Auth as FacadesAuth;

class HistoryController extends Controller
{
    public function index()
    {
        $user = FacadesAuth::user();

        if ($user->role === 'siswa') {
            $student = $user->student;
            if (!$student) return abort(404, 'Data Siswa tidak ditemukan');

            $payments = Payment::with('proofs')
                ->where('student_id', $student->id)
                ->latest('payment_date')
                ->get();
        } else {
            $payments = Payment::with('proofs')
                ->latest('payment_date')
                ->get();
        }

        return view('history.index', compact('payments'));
    }
    public function show($id)
    {
        $user = FacadesAuth::user();
        $payment = Payment::with([
            'student',
            'details.bill.sppRate',
            'proofs',
        ])->findOrFail($id);

        if ($user && $user->role === 'siswa') {
            $student = $user->student;
            if (!$student || $payment->student_id !== $student->id) {
                abort(403, 'Anda tidak memiliki hak akses untuk melihat data pembayaran ini.');
            }
        }

        return view('history.show', compact('payment'));
    }
}
