<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\PaymentProof;
use App\Models\Bill;
use App\Services\PaymentProofService;
use App\Services\PaymentVerificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function adminIndex(Request $request)
    {
        $status = $request->query('status');

        $query = Payment::with(['student.class', 'details.bill', 'latestProof'])
            ->latest();

        if ($status && in_array($status, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $status);
        }

        $payments = $query->get();

        $counts = [
            'all'      => Payment::count(),
            'pending'  => Payment::where('status', 'pending')->count(),
            'approved' => Payment::where('status', 'approved')->count(),
            'rejected' => Payment::where('status', 'rejected')->count(),
        ];

        return view('admin.payment.index', compact('payments', 'counts', 'status'));
    }

    public function adminShow($id)
    {
        $payment = Payment::with([
            'student.class',
            'student.academicYear',
            'details.bill.sppRate',
            'proofs',
            'verifier',
        ])->findOrFail($id);

        return view('admin.payment.show', compact('payment'));
    }

    public function approve(PaymentVerificationService $service, $id)
    {
        $payment = Payment::findOrFail($id);

        try {
            $service->approvePayment($payment, Auth::user());
            return back()->with('success', 'Pembayaran berhasil disetujui (Lunas). Seluruh tagihan terkait telah diperbarui.');
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan sistem saat memproses persetujuan.');
        }
    }

    public function reject(Request $request, PaymentVerificationService $service, $id)
    {
        $request->validate([
            'note' => 'required|string|max:500',
        ]);

        $payment = Payment::findOrFail($id);

        try {
            $service->rejectPayment($payment, Auth::user(), $request->note);
            return back()->with('success', 'Pembayaran telah ditolak. Alasan penolakan berhasil dicatat dan siswa dapat mengunggah bukti perbaikan.');
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan sistem saat memproses penolakan.');
        }
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
            ->route('student.payments.create')
            ->with('success', 'Bukti pembayaran berhasil dikirim.');
    }

    public function studentIndex()
    {
        $payments = Payment::with(['details.bill.sppRate', 'proofs'])
            ->where('student_id', Auth::user()->student->id)
            ->latest()
            ->take(5)
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

        return redirect()->route('student.payments.index', $paymentId)
            ->with('success', 'Bukti baru berhasil diunggah dan status kembali menjadi Pending.');
    }

    /**
     * Layani tampilan berkas bukti transfer dengan otorisasi berbasis peran (Admin, Petugas, Pemilik).
     */
    public function showProof(PaymentProof $proof, PaymentProofService $service)
    {
        return $service->getSecureProofResponse($proof, Auth::user());
    }
}
