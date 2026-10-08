<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\PaymentProof;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PaymentProofService
{
    /**
     * Simpan file bukti transfer ke private storage dengan versi histori.
     *
     * @param Payment $payment
     * @param UploadedFile $file
     * @param int|null $amount
     * @param string|null $note
     * @return PaymentProof
     */
    public function storeProof(Payment $payment, UploadedFile $file, ?int $amount = null, ?string $note = null): PaymentProof
    {
        // 1. Hitung versi berikutnya berdasarkan histori bukti payment
        $currentMaxVersion = (int) $payment->proofs()->max('version');
        $nextVersion = $currentMaxVersion + 1;

        // 2. Simpan file ke disk 'local' (storage/app/private/payment-proofs)
        $path = $file->store('payment-proofs', 'local');

        // 3. Simpan record bukti transfer
        return PaymentProof::create([
            'payment_id' => $payment->id,
            'image_path' => $path,
            'amount'     => $amount ?? $payment->total_amount,
            'note'       => $note,
            'version'    => $nextVersion,
        ]);
    }

    /**
     * Otorisasi dan layani unduhan/tampilan file bukti transfer.
     * Mencegah Siswa A melihat bukti Siswa B secara langsung.
     *
     * @param PaymentProof $proof
     * @param User $user
     * @return BinaryFileResponse
     */
    public function getSecureProofResponse(PaymentProof $proof, User $user): BinaryFileResponse
    {
        // Otorisasi akses:
        // Admin & Petugas boleh melihat semua bukti.
        // Siswa HANYA boleh melihat bukti pembayaran miliknya sendiri.
        if ($user->isSiswa()) {
            $student = $user->student;
            if (!$student || $proof->payment->student_id !== $student->id) {
                abort(403, 'Anda tidak memiliki hak akses untuk melihat dokumen bukti pembayaran ini.');
            }
        } elseif (!$user->isAdmin() && !$user->isPetugas()) {
            abort(403, 'Akses ditolak.');
        }

        // Resolusi file di disk privat atau publik (backward compatibility)
        $filePath = null;

        if (Storage::disk('local')->exists($proof->image_path)) {
            $filePath = Storage::disk('local')->path($proof->image_path);
        } elseif (Storage::disk('public')->exists($proof->image_path)) {
            $filePath = Storage::disk('public')->path($proof->image_path);
        } elseif (file_exists(storage_path('app/' . $proof->image_path))) {
            $filePath = storage_path('app/' . $proof->image_path);
        } elseif (file_exists(storage_path('app/public/' . $proof->image_path))) {
            $filePath = storage_path('app/public/' . $proof->image_path);
        } elseif (file_exists(storage_path('app/private/' . $proof->image_path))) {
            $filePath = storage_path('app/private/' . $proof->image_path);
        } else {
            abort(404, 'File bukti transfer fisik tidak ditemukan di server.');
        }

        return response()->file($filePath);
    }
}
