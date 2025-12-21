<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Bill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function adminIndex()
    {
        $payments = Payment::with('student', 'proofs')
            ->latest()
            ->get();

        return view('admin.payment.index', compact('payments'));
    }

    public function adminShow($id)
    {
        $payment = Payment::with([
            'student',
            'details.bill.sppRate',
            'proofs',
        ])->findOrFail($id);

        return view('admin.payment.show', compact('payment'));
    }

    public function approve($id)
    {
        DB::transaction(function () use ($id) {

            $payment = Payment::with(['details.bill', 'proofs'])
                ->findOrFail($id);

            $totalTagihan = $payment->details->sum('amount');
            $totalBayar   = $payment->proofs->sum('amount');

            if ($totalBayar < $totalTagihan) {
                abort(403, 'Nominal pembayaran belum mencukupi.');
            }

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

        return back()->with('success', 'Pembayaran berhasil disetujui.');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'note' => 'required|string',
        ]);

        Payment::findOrFail($id)->update([
            'status'      => 'rejected',
            'note'        => $request->note,
            'verified_by' => Auth::id(),
            'verified_at' => now(),
        ]);

        return back()->with('success', 'Pembayaran ditolak.');
    }

    public function studentIndex()
    {
        $payments = Payment::where('student_id', Auth::user()->student->id)
            ->latest()
            ->get();

        return view('student.payments.index', compact('payments'));
    }

    public function studentShow($id)
    {
        $payment = Payment::with(['details.bill.sppRate', 'proofs'])
            ->where('student_id', Auth::user()->student->id)
            ->findOrFail($id);

        return view('student.payments.show', compact('payment'));
    }

    public function create()
    {
        $student = Auth::user()->student;

        if (!$student) {
            abort(404, 'Data siswa tidak ditemukan.');
        }

        $bills = $student->bills()
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
            'amount'      => 'required|numeric|min:1',
            'proof_image' => 'required|image|max:2048',
            'note'        => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {

            $student = Auth::user()->student;

            $path = $request->file('proof_image')
                ->store('payment-proofs', 'public');

            $payment = Payment::create([
                'student_id'   => $student->id,
                'payment_date' => now(),
                'status'       => 'pending',
            ]);

            foreach ($request->bill_ids as $billId) {

                $bill = Bill::where('id', $billId)
                    ->where('student_id', $student->id)
                    ->where('status', 'unpaid')
                    ->firstOrFail();

                $payment->details()->create([
                    'bill_id' => $bill->id,
                    'amount'  => $bill->sppRate->amount,
                ]);
            }

            $payment->proofs()->create([
                'image_path' => $path,
                'amount'     => $request->amount,
                'note'       => $request->note,
            ]);
        });

        return redirect()
            ->route('student.payments.index')
            ->with('success', 'Bukti pembayaran berhasil dikirim.');
    }

    public function uploadAdditionalProof(Request $request, $paymentId)
    {
        $payment = Payment::where('id', $paymentId)
            ->where('student_id', Auth::user()->student->id)
            ->whereIn('status', ['pending', 'rejected'])
            ->firstOrFail();

        $request->validate([
            'amount'      => 'required|numeric|min:1',
            'proof_image' => 'required|image|max:2048',
            'note'        => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $payment) {

            $path = $request->file('proof_image')
                ->store('payment-proofs', 'public');

            $payment->proofs()->create([
                'image_path' => $path,
                'amount'     => $request->amount,
                'note'       => $request->note,
            ]);

            $payment->update([
                'status' => 'pending',
            ]);
        });

        return back()->with('success', 'Bukti pembayaran tambahan berhasil dikirim.');
    }
}
