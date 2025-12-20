<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Bill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    /* =====================================================
     * ADMIN
     * ===================================================== */

    public function adminIndex()
    {
        $payments = Payment::with('student')
            ->orderByDesc('id')
            ->get();

        return view('admin.payment.index', compact('payments'));
    }

    public function adminShow($id)
    {
        $payment = Payment::with([
            'student',
            'details.bill.sppRate'
        ])->findOrFail($id);

        // $bill = Bill::with(['student.class', 'student.academicYear'])->findOrFail($id);

        return view('admin.payment.show', compact('payment',));
    }

    public function approve($id)
    {
        DB::transaction(function () use ($id) {

            $payment = Payment::with('details.bill')->findOrFail($id);

            foreach ($payment->details as $detail) {
                $detail->bill->update([
                    'status'  => 'paid',
                    'paid_at' => now(),
                ]);
            }

            $payment->update([
                'status'      => 'approved',
                'verified_by' => Auth::id(),
                'verified_at' => now(),
            ]);
        });

        return redirect()->back()->with('success', 'Pembayaran berhasil disetujui.');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'note' => 'required'
        ]);

        Payment::findOrFail($id)->update([
            'status'      => 'rejected',
            'note'        => $request->note,
            'verified_by' => Auth::id(),
            'verified_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Pembayaran ditolak.');
    }

    /* =====================================================
     * STUDENT
     * ===================================================== */

    public function studentIndex()
    {
        $payments = Payment::where('student_id', Auth::user()->student->id)
            ->orderByDesc('id')
            ->get();

        return view('student.payments.index', compact('payments'));
    }

    public function studentShow($id)
    {
        $payment = Payment::with('details.bill.sppRate')
            ->where('student_id', Auth::user()->student->id)
            ->findOrFail($id);

        return view('student.payments.show', compact('payment'));
    }

    public function create()
    {
        $user = Auth::user();

        if (!$user->student) {
            abort(404, 'Data siswa tidak ditemukan.');
        }

        $bills = $user->student->bills()
            ->with('sppRate')
            ->where('status', 'unpaid')
            ->get();

        return view('student.payments.create', compact('bills'));
    }

  public function store(Request $request)
{
    $request->validate([
        'bill_ids'    => 'required|array|min:1',
        'bill_ids.*'  => 'exists:bills,id',
        'paid_amount' => 'required|numeric|min:1',
        'proof_image' => 'required|image|max:2048',
    ]);

    DB::transaction(function () use ($request) {

        $path = $request->file('proof_image')
            ->store('payment-proofs', 'public');

        $payment = Payment::create([
            'student_id'   => Auth::user()->student->id,
            'payment_date' => now(),
            'paid_amount'  => $request->paid_amount,
            'proof_image'  => $path,
            'status'       => 'pending',
        ]);

        $total = 0;

        foreach ($request->bill_ids as $billId) {

            $bill = Bill::where('id', $billId)
                ->where('student_id', Auth::user()->student->id)
                ->where('status', 'unpaid')
                ->firstOrFail();

            $payment->details()->create([
                'bill_id' => $bill->id,
                'amount'  => $bill->sppRate->amount,
            ]);

            $total += $bill->sppRate->amount;
        }

        if ($request->paid_amount != $total) {
            abort(403, 'Jumlah pembayaran tidak valid');
        }
    });

    return redirect()
        ->route('student.payments.index')
        ->with('success', 'Bukti pembayaran berhasil dikirim.');
}

}
