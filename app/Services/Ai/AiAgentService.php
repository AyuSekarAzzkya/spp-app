<?php

namespace App\Services\Ai;

use App\Models\User;
use App\Services\AuditLogService;
use Exception;
use Illuminate\Support\Facades\Log;

class AiAgentService
{
    protected AiIntentService $intentService;
    protected AiToolRegistry $toolRegistry;
    protected AuditLogService $auditLogService;

    public function __construct(
        AiIntentService $intentService,
        AiToolRegistry $toolRegistry,
        AuditLogService $auditLogService
    ) {
        $this->intentService = $intentService;
        $this->toolRegistry = $toolRegistry;
        $this->auditLogService = $auditLogService;
    }

    /**
     * Jalankan query AI agent dengan otorisasi role dan audit log.
     *
     * @param User $user
     * @param string $prompt
     * @return array
     */
    public function process(User $user, string $prompt): array
    {
        $prompt = trim($prompt);
        if (empty($prompt)) {
            return [
                'status' => 'error',
                'title' => 'Perintah Kosong',
                'response_type' => 'error',
                'message' => 'Silakan ketik perintah atau pencarian data yang Anda butuhkan.',
                'data' => [],
                'actions' => [],
                'type' => 'error'
            ];
        }

        // 1. Deteksi Intent
        $intentResult = $this->intentService->detectIntent($prompt);
        $intent = $intentResult['intent'];
        $params = $intentResult['parameters'];

        if ($intent === 'unknown') {
            return [
                'status' => 'error',
                'title' => 'Perintah Belum Dikenali',
                'response_type' => 'error',
                'message' => "Perintah belum dikenali.\n\nContoh perintah:\n• \"Tampilkan siswa yang belum bayar bulan ini\"\n• \"Berapa total pembayaran bulan ini?\"\n• \"Cari siswa Ahmad Fauzan\"\n• \"Tampilkan tunggakan kelas XII PPLG\"\n• \"Rekap pembayaran bulan ini\"",
                'data' => [],
                'actions' => [],
                'type' => 'error'
            ];
        }

        // 2. Otorisasi Akses Berdasarkan Role
        if (!$this->isAuthorized($user, $intent)) {
            $this->logActivity($user, $prompt, $intent, 'UNAUTHORIZED');

            return [
                'status' => 'unauthorized',
                'title' => 'Akses Dibatasi',
                'response_type' => 'error',
                'message' => 'Kamu tidak memiliki akses untuk melihat data tersebut.',
                'data' => [],
                'actions' => [],
                'type' => 'unauthorized'
            ];
        }

        // 3. Eksekusi Tool Terdaftar
        try {
            $toolResult = $this->executeTool($user, $intent, $params);

            // 4. Catat Audit Log
            $this->logActivity($user, $prompt, $intent, 'SUCCESS');

            return [
                'status' => $toolResult['status'] ?? 'success',
                'intent' => $intent,
                'title' => $toolResult['title'] ?? 'Hasil Permintaan',
                'response_type' => $toolResult['response_type'] ?? 'info',
                'message' => $toolResult['message'] ?? 'Data berhasil ditemukan.',
                'data' => $toolResult['data'] ?? [],
                'actions' => $toolResult['actions'] ?? [],
                'type' => $toolResult['type'] ?? 'info'
            ];
        } catch (Exception $e) {
            Log::error('AI Agent Error: ' . $e->getMessage());

            $this->logActivity($user, $prompt, $intent, 'ERROR');

            return [
                'status' => 'error',
                'title' => 'Kendala Sistem',
                'response_type' => 'error',
                'message' => 'Terjadi kendala saat memproses data. Silakan coba kembali sesaat lagi.',
                'data' => [],
                'actions' => [],
                'type' => 'error'
            ];
        }
    }

    /**
     * Periksa apakah user memiliki hak akses untuk memanggil intent tool ini.
     */
    protected function isAuthorized(User $user, string $intent): bool
    {
        $role = $user->role;

        // Admin memiliki akses penuh ke seluruh tools
        if ($role === 'admin') {
            return true;
        }

        // Petugas boleh mengakses operasional
        if ($role === 'petugas') {
            $petugasAllowed = [
                'search_students',
                'get_unpaid_students',
                'get_payment_summary',
                'get_pending_payments',
                'get_student_arrears',
                'get_monthly_report',
                'navigate_page',
                'get_my_status'
            ];
            return in_array($intent, $petugasAllowed);
        }

        // Siswa HANYA boleh mengakses data dirinya sendiri atau navigasi halamannya
        if ($role === 'siswa') {
            return in_array($intent, ['get_my_status', 'navigate_page']);
        }

        return false;
    }

    /**
     * Panggil tool aman dari AiToolRegistry.
     */
    protected function executeTool(User $user, string $intent, array $params): array
    {
        return match ($intent) {
            'search_students'      => $this->toolRegistry->searchStudents($user, $params),
            'get_unpaid_students'  => $this->toolRegistry->getUnpaidStudents($user, $params),
            'get_payment_summary'  => $this->toolRegistry->getPaymentSummary($user, $params),
            'get_monthly_report'   => $this->toolRegistry->getMonthlyReport($user, $params),
            'get_pending_payments' => $this->toolRegistry->getPendingPayments($user, $params),
            'get_student_arrears'  => $this->toolRegistry->getStudentArrears($user, $params),
            'navigate_page'        => $this->toolRegistry->navigatePage($user, $params),
            'get_my_status'        => $this->toolRegistry->getMyStatus($user, $params),
            default                => [
                'success' => false,
                'status' => 'error',
                'title' => 'Tool Tidak Dikenali',
                'response_type' => 'error',
                'message' => 'Perintah belum dikenali.',
                'data' => [],
                'actions' => [],
                'type' => 'error'
            ],
        };
    }

    /**
     * Catat interaksi AI ke audit log.
     */
    protected function logActivity(User $user, string $prompt, string $intent, string $status): void
    {
        try {
            $this->auditLogService->log(
                action: 'AI_QUERY',
                model: null,
                oldValues: null,
                newValues: [
                    'prompt' => $prompt,
                    'intent' => $intent,
                    'status' => $status
                ],
                userId: $user->id
            );
        } catch (Exception $e) {
            Log::warning('Failed to write AI audit log: ' . $e->getMessage());
        }
    }
}
