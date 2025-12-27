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

            $payments = Payment::with(['details.bill', 'proofs'])
                ->where('student_id', $student->id)
                ->latest()
                ->get();
        } else {
            $payments = Payment::with(['student', 'details.bill', 'proofs'])
                ->latest()
                ->get();
        }

        return view('history.index', compact('payments'));
    }
    public function show($id)
    {
        $payment = Payment::with([
            'student',
            'details.bill.sppRate',
            'proofs',
        ])->findOrFail($id);

        return view('history.show', compact('payment'));
    }
}
