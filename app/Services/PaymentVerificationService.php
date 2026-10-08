<?php

namespace App\Services;

use App\Models\Bill;
use App\Models\Payment;
use App\Models\PaymentProof;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class PaymentVerificationService
{
    public function __construct(
        protected PaymentProofService $proofService,
        protected AuditLogService $auditService
    ) {}

    /**
     * Setujui (Approve) pembayaran oleh petugas/admin.
     * Mengubah seluruh tagihan terkait menjadi PAID.
     *
     * @param Payment $payment
     * @param User $verifier
     * @return void
     * @throws \DomainException
     */
    public function approvePayment(Payment $payment, User $verifier): void
    {
        DB::transaction(function () use ($payment, $verifier) {
            
            // 1. Kunci baris transaksi pembayaran
            $lockedPayment = Payment::where('id', $payment->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedPayment->status !== 'pending') {
                throw new \DomainException("Hanya pembayaran berstatus 'Pending' yang dapat disetujui. Status saat ini: {$lockedPayment->status}.");
            }

            // 2. Kunci seluruh baris tagihan yang terkait
            $billIds = $lockedPayment->details()->pluck('bill_id')->toArray();
            $bills = Bill::whereIn('id', $billIds)
                ->lockForUpdate()
                ->get();

            // 3. Pastikan tidak ada tagihan yang telah dilunasi oleh transaksi lain
            foreach ($bills as $bill) {
                if ($bill->status === 'paid') {
                    throw new \DomainException("Tagihan bulan {$bill->month_name} {$bill->year} telah berstatus lunas pada transaksi lain.");
                }
            }

            $now = now();

            // 4. Update status seluruh tagihan menjadi PAID
            foreach ($bills as $bill) {
                $bill->update([
                    'status'  => 'paid',
                    'paid_at' => $now,
                ]);
            }

            // 5. Update status pembayaran menjadi APPROVED
            $lockedPayment->update([
                'status'      => 'approved',
                'verified_by' => $verifier->id,
                'verified_at' => $now,
            ]);

            // 6. Catat ke audit log
            $this->auditService->log('APPROVE_PAYMENT', $lockedPayment, [
                'status' => 'pending',
            ], [
                'status'      => 'approved',
                'verified_by' => $verifier->id,
                'verified_at' => $now->toDateTimeString(),
                'total_paid'  => $lockedPayment->total_amount,
            ], $verifier->id);
        });
    }

    /**
     * Tolak (Reject) pembayaran oleh petugas/admin dengan alasan wajib.
     * Tagihan tetap UNPAID dan dapat diperbaiki bukti transfernya oleh siswa.
     *
     * @param Payment $payment
     * @param User $verifier
     * @param string $rejectionReason
     * @return void
     * @throws \DomainException
     */
    public function rejectPayment(Payment $payment, User $verifier, string $rejectionReason): void
    {
        $cleanReason = trim($rejectionReason);
        if (empty($cleanReason)) {
            throw new \DomainException('Alasan penolakan pembayaran wajib diisi.');
        }

        DB::transaction(function () use ($payment, $verifier, $cleanReason) {
            
            $lockedPayment = Payment::where('id', $payment->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedPayment->status !== 'pending') {
                throw new \DomainException("Hanya pembayaran berstatus 'Pending' yang dapat ditolak. Status saat ini: {$lockedPayment->status}.");
            }

            $now = now();

            // Status pembayaran menjadi REJECTED, tagihan tetap UNPAID
            $lockedPayment->update([
                'status'      => 'rejected',
                'note'        => $cleanReason,
                'verified_by' => $verifier->id,
                'verified_at' => $now,
            ]);

            // Catat ke audit log
            $this->auditService->log('REJECT_PAYMENT', $lockedPayment, [
                'status' => 'pending',
            ], [
                'status'      => 'rejected',
                'note'        => $cleanReason,
                'verified_by' => $verifier->id,
                'verified_at' => $now->toDateTimeString(),
            ], $verifier->id);
        });
    }

    /**
     * Upload bukti transfer perbaikan (Re-upload) oleh siswa setelah pembayaran ditolak.
     * Mengembalikan status pembayaran menjadi PENDING tanpa menghapus bukti sebelumnya.
     *
     * @param Payment $payment
     * @param Student $student
     * @param UploadedFile $newProofFile
     * @param string|null $note
     * @return PaymentProof
     * @throws \DomainException
     */
    public function reuploadProof(
        Payment $payment,
        Student $student,
        UploadedFile $newProofFile,
        ?string $note = null
    ): PaymentProof {
        // Validasi kepemilikan pembayaran
        if ($payment->student_id !== $student->id) {
            throw new \DomainException('Anda tidak berhak memperbarui transaksi siswa lain.');
        }

        // Pembayaran yang sudah approved tidak boleh diedit
        if ($payment->status === 'approved') {
            throw new \DomainException('Pembayaran yang telah disetujui (Approved) tidak dapat diunggah ulang bukti transfernya.');
        }

        return DB::transaction(function () use ($payment, $newProofFile, $note) {
            
            $lockedPayment = Payment::where('id', $payment->id)
                ->lockForUpdate()
                ->firstOrFail();

            $oldStatus = $lockedPayment->status;

            // 1. Simpan bukti transfer baru dengan versi bertambah
            $proof = $this->proofService->storeProof(
                $lockedPayment,
                $newProofFile,
                $lockedPayment->total_amount,
                $note
            );

            // 2. Kembalikan status pembayaran menjadi PENDING
            $lockedPayment->update([
                'status' => 'pending',
            ]);

            // 3. Catat ke audit log
            $this->auditService->log('REUPLOAD_PROOF', $lockedPayment, [
                'status'        => $oldStatus,
            ], [
                'status'        => 'pending',
                'proof_version' => $proof->version,
            ]);

            return $proof;
        });
    }
}
