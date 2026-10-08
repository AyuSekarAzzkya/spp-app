<?php

namespace App\Services;

use App\Models\Bill;
use App\Models\Payment;
use App\Models\PaymentDetail;
use App\Models\Student;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function __construct(
        protected PaymentProofService $proofService,
        protected AuditLogService $auditService
    ) {}

    /**
     * Buat transaksi pembayaran transfer baru oleh siswa dengan pessimistic locking.
     * Mencegah double submit, race condition, dan manipulasi nominal frontend.
     *
     * @param Student $student
     * @param array $billIds
     * @param UploadedFile $proofFile
     * @param string|null $note
     * @return Payment
     * @throws \DomainException
     */
    public function createTransferPayment(
        Student $student,
        array $billIds,
        UploadedFile $proofFile,
        ?string $note = null
    ): Payment {
        if (empty($billIds)) {
            throw new \DomainException('Harap pilih minimal satu bulan tagihan untuk dibayar.');
        }

        // Pastikan tidak ada ID duplikat yang dikirim dari input form
        $uniqueBillIds = array_unique($billIds);

        return DB::transaction(function () use ($student, $uniqueBillIds, $proofFile, $note) {
            
            // 1. Validasi status siswa
            if ($student->status !== 'active') {
                throw new \DomainException('Akun siswa Anda sedang berstatus nonaktif. Silakan hubungi pihak sekolah.');
            }

            // 2. Kunci baris tagihan dengan lockForUpdate untuk mencegah race condition
            $bills = Bill::whereIn('id', $uniqueBillIds)
                ->where('student_id', $student->id)
                ->with('sppRate')
                ->lockForUpdate()
                ->get();

            // 3. Validasi apakah seluruh tagihan yang diminta benar-benar ditemukan dan milik siswa
            if ($bills->count() !== count($uniqueBillIds)) {
                throw new \DomainException('Satu atau lebih tagihan tidak valid atau bukan milik data siswa Anda.');
            }

            // 4. Validasi apakah ada tagihan yang sudah lunas (paid)
            foreach ($bills as $bill) {
                if ($bill->status === 'paid') {
                    throw new \DomainException("Tagihan bulan {$bill->month_name} {$bill->year} sudah berstatus lunas.");
                }
            }

            // 5. Validasi ANTI-DOUBLE PAYMENT: Pastikan tidak ada tagihan yang sedang PENDING di transaksi lain
            $pendingBillIds = PaymentDetail::whereIn('bill_id', $uniqueBillIds)
                ->whereHas('payment', function ($q) {
                    $q->where('status', 'pending');
                })
                ->pluck('bill_id')
                ->toArray();

            if (!empty($pendingBillIds)) {
                $pendingBill = $bills->firstWhere('id', $pendingBillIds[0]);
                $monthName = $pendingBill ? $pendingBill->month_name . ' ' . $pendingBill->year : 'terpilih';
                throw new \DomainException("Tagihan {$monthName} sedang dalam proses verifikasi pembayaran oleh petugas. Tidak dapat dibayar ganda.");
            }

            // 6. Hitung ulang total riil di backend berdasarkan tarif database
            $calculatedTotal = $bills->sum(fn($b) => $b->sppRate->amount);
            if ($calculatedTotal <= 0) {
                throw new \DomainException('Total nominal tagihan tidak valid.');
            }

            // 7. Buat record transaksi pembayaran (status = pending)
            $payment = Payment::create([
                'student_id'   => $student->id,
                'payment_date' => now()->toDateString(),
                'status'       => 'pending',
                'note'         => $note,
            ]);

            // 8. Buat rincian tagihan (payment_details)
            foreach ($bills as $bill) {
                PaymentDetail::create([
                    'payment_id' => $payment->id,
                    'bill_id'    => $bill->id,
                    'amount'     => $bill->sppRate->amount,
                ]);
            }

            // 9. Simpan file bukti transfer ke private storage (version 1)
            $this->proofService->storeProof($payment, $proofFile, $calculatedTotal, $note);

            // 10. Catat aktivitas ke audit log
            $this->auditService->log('CREATE_PAYMENT', $payment, null, [
                'student_id'   => $student->id,
                'bill_ids'     => $uniqueBillIds,
                'total_amount' => $calculatedTotal,
                'bills_count'  => count($uniqueBillIds),
            ]);

            return $payment;
        });
    }
}
