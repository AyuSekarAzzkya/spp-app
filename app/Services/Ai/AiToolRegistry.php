<?php

namespace App\Services\Ai;

use App\Models\Bill;
use App\Models\Payment;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;

class AiToolRegistry
{
    /**
     * Daftar route yang diizinkan untuk navigasi cepat via natural language.
     */
    protected array $whitelistedRoutes = [
        'students.index'          => ['roles' => ['admin', 'petugas'], 'label' => 'Data Siswa', 'desc' => 'Daftar seluruh siswa terdaftar'],
        'students.create'         => ['roles' => ['admin', 'petugas'], 'label' => 'Tambah Siswa', 'desc' => 'Formulir pendaftaran siswa baru'],
        'admin.payments.index'    => ['roles' => ['admin', 'petugas'], 'label' => 'Verifikasi Pembayaran', 'desc' => 'Daftar pembayaran menunggu verifikasi petugas'],
        'reports.index'           => ['roles' => ['admin', 'petugas'], 'label' => 'Laporan Pembayaran', 'desc' => 'Rekap transaksi dan pemasukan SPP'],
        'reports.arrears'         => ['roles' => ['admin', 'petugas'], 'label' => 'Laporan Tunggakan', 'desc' => 'Monitoring siswa menunggak per kelas'],
        'billing.students'        => ['roles' => ['admin', 'petugas'], 'label' => 'Data Tagihan', 'desc' => 'Monitoring dan cetak tagihan siswa'],
        'classes.index'           => ['roles' => ['admin', 'petugas'], 'label' => 'Data Kelas', 'desc' => 'Manajemen kelas dan konsentrasi keahlian'],
        'academic-years.index'    => ['roles' => ['admin'], 'label' => 'Tahun Ajaran', 'desc' => 'Pengaturan tahun ajaran aktif'],
        'spp.index'               => ['roles' => ['admin'], 'label' => 'Tarif SPP', 'desc' => 'Pengaturan nominal tarif SPP'],
        'users.index'             => ['roles' => ['admin'], 'label' => 'Manajemen Pengguna', 'desc' => 'Kelola akun admin, petugas, dan siswa'],
        'student.payments.create' => ['roles' => ['siswa'], 'label' => 'Bayar SPP', 'desc' => 'Form pembayaran SPP online siswa'],
        'payments.history'        => ['roles' => ['siswa'], 'label' => 'Riwayat Pembayaran', 'desc' => 'Riwayat transaksi pembayaran SPP'],
    ];

    /**
     * Cari siswa berdasarkan nama atau NIS.
     * Hanya boleh diakses oleh: admin, petugas.
     */
    public function searchStudents(User $user, array $params): array
    {
        $keyword = trim($params['query'] ?? '');
        if (empty($keyword)) {
            return [
                'success' => false,
                'status' => 'error',
                'title' => 'Pencarian Siswa',
                'response_type' => 'error',
                'message' => 'Silakan masukkan nama atau NIS siswa yang ingin dicari.',
                'data' => [],
                'actions' => [],
                'type' => 'error'
            ];
        }

        $students = Student::with(['class', 'academicYear'])
            ->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('nis', 'like', "%{$keyword}%")
                  ->orWhere('nisn', 'like', "%{$keyword}%");
            })
            ->limit(10)
            ->get();

        if ($students->isEmpty()) {
            return [
                'success' => true,
                'status' => 'empty',
                'title' => 'Siswa Tidak Ditemukan',
                'response_type' => 'empty',
                'message' => "Tidak ada data siswa yang cocok dengan \"{$keyword}\".",
                'data' => [],
                'actions' => [
                    ['label' => 'Buka Data Siswa', 'url' => route('students.index'), 'variant' => 'secondary', 'icon' => 'mdi-account-multiple']
                ],
                'type' => 'student_list'
            ];
        }

        $items = $students->map(function ($s) {
            return [
                'id' => $s->id,
                'nis' => $s->nis,
                'name' => $s->name,
                'class' => $s->class->name ?? '-',
                'year' => $s->academicYear->year ?? '-',
                'gender' => $s->gender === 'L' ? 'Laki-laki' : 'Perempuan',
                'status' => $s->status === 'active' ? 'Aktif' : 'Non-aktif',
                'detail_url' => route('students.detail', $s->id),
                'billing_url' => route('billing.index', $s->id),
            ];
        })->toArray();

        $count = count($items);
        $title = $count === 1 ? 'Siswa Ditemukan' : "Ditemukan {$count} Siswa";

        return [
            'success' => true,
            'status' => 'success',
            'title' => $title,
            'response_type' => $count === 1 ? 'student' : 'student_list',
            'message' => $count === 1 ? 'Data siswa cocok dengan kata kunci pencarian.' : "Menampilkan {$count} siswa yang cocok dengan \"{$keyword}\".",
            'data' => $count === 1 ? $items[0] : $items,
            'actions' => [
                ['label' => 'Buka Data Siswa', 'url' => route('students.index'), 'variant' => 'secondary', 'icon' => 'mdi-account-multiple']
            ],
            'type' => 'student_list'
        ];
    }

    /**
     * Tampilkan siswa yang belum membayar SPP pada bulan tertentu.
     * Hanya boleh diakses oleh: admin, petugas.
     */
    public function getUnpaidStudents(User $user, array $params): array
    {
        $month = (int)($params['month'] ?? now()->month);
        $year = (int)($params['year'] ?? now()->year);
        $className = trim($params['class'] ?? '');

        $query = Bill::with(['student.class', 'sppRate'])
            ->where('month', $month)
            ->where('year', $year)
            ->where('status', 'unpaid');

        if (!empty($className)) {
            $query->whereHas('student.class', function ($q) use ($className) {
                $q->where('name', 'like', "%{$className}%");
            });
        }

        $totalUnpaidCount = (clone $query)->count();
        $bills = $query->limit(20)->get();
        $monthName = Carbon::create()->month($month)->translatedFormat('F');
        $title = "Tunggakan {$monthName} {$year}";

        if ($totalUnpaidCount === 0) {
            return [
                'success' => true,
                'status' => 'success',
                'title' => $title,
                'response_type' => 'empty',
                'message' => "Seluruh siswa" . (!empty($className) ? " di kelas {$className}" : "") . " sudah lunas SPP untuk bulan {$monthName} {$year}.",
                'data' => [],
                'actions' => [
                    ['label' => 'Lihat Laporan Tunggakan', 'url' => route('reports.arrears', ['year' => $year]), 'variant' => 'secondary', 'icon' => 'mdi-file-chart']
                ],
                'type' => 'unpaid_list'
            ];
        }

        $items = $bills->map(function ($b) {
            return [
                'student_id' => $b->student_id,
                'name' => $b->student->name ?? '-',
                'nis' => $b->student->nis ?? '-',
                'class' => $b->student->class->name ?? '-',
                'amount' => 'Rp ' . number_format($b->sppRate->amount ?? 0, 0, ',', '.'),
                'due_date' => Carbon::parse($b->due_date)->format('d M Y'),
                'billing_url' => route('billing.index', $b->student_id),
            ];
        })->toArray();

        return [
            'success' => true,
            'status' => 'success',
            'title' => $title,
            'response_type' => 'table',
            'message' => "{$totalUnpaidCount} siswa" . (!empty($className) ? " di kelas {$className}" : "") . " belum membayar.",
            'data' => [
                'total_count' => $totalUnpaidCount,
                'shown_count' => count($items),
                'month' => $monthName,
                'year' => $year,
                'class' => $className ?: null,
                'students' => $items,
            ],
            'actions' => [
                ['label' => 'Buka Data Tunggakan', 'url' => route('reports.arrears', ['year' => $year]), 'variant' => 'primary', 'icon' => 'mdi-open-in-new']
            ],
            'type' => 'unpaid_list'
        ];
    }

    /**
     * Dapatkan rekap dan ringkasan pembayaran bulan ini / hari ini.
     * Hanya boleh diakses oleh: admin, petugas.
     */
    public function getPaymentSummary(User $user, array $params): array
    {
        $period = $params['period'] ?? 'this_month';
        $month = (int)($params['month'] ?? now()->month);
        $year = (int)($params['year'] ?? now()->year);
        $monthName = Carbon::create()->month($month)->translatedFormat('F');

        $query = Payment::where('status', 'approved');

        if ($period === 'today') {
            $query->whereDate('payment_date', now()->toDateString());
            $periodLabel = 'Hari Ini (' . now()->translatedFormat('d F Y') . ')';
        } else {
            $query->whereMonth('payment_date', $month)->whereYear('payment_date', $year);
            $periodLabel = "{$monthName} {$year}";
        }

        $payments = (clone $query)->with('proofs')->get();
        $totalAmount = $payments->sum(fn($p) => $p->proofs->sum('amount'));
        $totalTransactions = $payments->count();
        $pendingCount = Payment::where('status', 'pending')->count();

        $actions = [
            ['label' => 'Buka Rekap Pembayaran', 'url' => route('reports.index'), 'variant' => 'secondary', 'icon' => 'mdi-file-document-outline']
        ];
        if ($pendingCount > 0) {
            $actions[] = ['label' => "Verifikasi Pending ({$pendingCount})", 'url' => route('admin.payments.index'), 'variant' => 'primary', 'icon' => 'mdi-shield-check'];
        }

        return [
            'success' => true,
            'status' => 'success',
            'title' => "Pemasukan {$periodLabel}",
            'response_type' => 'stat',
            'message' => "{$totalTransactions} transaksi disetujui.",
            'data' => [
                'period' => $periodLabel,
                'total_amount' => 'Rp ' . number_format($totalAmount, 0, ',', '.'),
                'total_transactions' => $totalTransactions,
                'pending_verification' => $pendingCount,
                'report_url' => route('reports.index'),
                'verification_url' => route('admin.payments.index'),
            ],
            'actions' => $actions,
            'type' => 'summary_card'
        ];
    }

    /**
     * Dapatkan laporan bulanan komprehensif.
     * Hanya boleh diakses oleh: admin, petugas.
     */
    public function getMonthlyReport(User $user, array $params): array
    {
        $month = (int)($params['month'] ?? now()->month);
        $year = (int)($params['year'] ?? now()->year);
        $monthName = Carbon::create()->month($month)->translatedFormat('F');

        $approvedPayments = Payment::where('status', 'approved')
            ->whereMonth('payment_date', $month)
            ->whereYear('payment_date', $year)
            ->with('proofs')
            ->get();

        $totalTransactions = $approvedPayments->count();
        $totalAmount = $approvedPayments->sum(fn($p) => $p->proofs->sum('amount'));

        $paidStudents = Bill::where('month', $month)
            ->where('year', $year)
            ->where('status', 'paid')
            ->distinct('student_id')
            ->count('student_id');

        $unpaidStudents = Bill::where('month', $month)
            ->where('year', $year)
            ->where('status', 'unpaid')
            ->distinct('student_id')
            ->count('student_id');

        return [
            'success' => true,
            'status' => 'success',
            'title' => "Rekap {$monthName} {$year}",
            'response_type' => 'report',
            'message' => "Total transaksi: {$totalTransactions} • Pemasukan: Rp " . number_format($totalAmount, 0, ',', '.'),
            'data' => [
                'month' => $monthName,
                'year' => $year,
                'total_transactions' => number_format($totalTransactions, 0, ',', '.'),
                'total_payment' => 'Rp ' . number_format($totalAmount, 0, ',', '.'),
                'students_paid' => number_format($paidStudents, 0, ',', '.'),
                'students_unpaid' => number_format($unpaidStudents, 0, ',', '.'),
            ],
            'actions' => [
                ['label' => 'Lihat Rekap', 'url' => route('reports.index', ['month' => $month, 'year' => $year]), 'variant' => 'primary', 'icon' => 'mdi-file-document-outline'],
                ['label' => 'Export Excel', 'url' => route('reports.export.payments', ['month' => $month, 'year' => $year]), 'variant' => 'secondary', 'icon' => 'mdi-file-excel-outline'],
                ['label' => 'Data Tunggakan', 'url' => route('reports.arrears', ['year' => $year]), 'variant' => 'secondary', 'icon' => 'mdi-file-chart']
            ],
            'type' => 'monthly_report'
        ];
    }

    /**
     * Dapatkan daftar pembayaran pending yang butuh verifikasi.
     * Hanya boleh diakses oleh: admin, petugas.
     */
    public function getPendingPayments(User $user, array $params): array
    {
        $pending = Payment::with(['student.class', 'proofs'])
            ->where('status', 'pending')
            ->latest()
            ->limit(15)
            ->get();

        $totalPending = Payment::where('status', 'pending')->count();

        if ($pending->isEmpty()) {
            return [
                'success' => true,
                'status' => 'empty',
                'title' => 'Verifikasi Pembayaran',
                'response_type' => 'empty',
                'message' => 'Tidak ada pembayaran yang sedang menunggu verifikasi saat ini.',
                'data' => [],
                'actions' => [
                    ['label' => 'Riwayat Pembayaran', 'url' => route('admin.payments.index'), 'variant' => 'secondary', 'icon' => 'mdi-history']
                ],
                'type' => 'pending_list'
            ];
        }

        $items = $pending->map(function ($p) {
            return [
                'id' => $p->id,
                'student' => $p->student->name ?? '-',
                'class' => $p->student->class->name ?? '-',
                'date' => Carbon::parse($p->payment_date)->translatedFormat('d M Y'),
                'amount' => 'Rp ' . number_format($p->proofs->sum('amount'), 0, ',', '.'),
                'show_url' => route('admin.payments.show', $p->id),
            ];
        })->toArray();

        return [
            'success' => true,
            'status' => 'success',
            'title' => 'Pembayaran Butuh Verifikasi',
            'response_type' => 'table',
            'message' => "{$totalPending} pembayaran menunggu verifikasi petugas.",
            'data' => [
                'total_count' => $totalPending,
                'items' => $items,
            ],
            'actions' => [
                ['label' => 'Buka Antrean Verifikasi', 'url' => route('admin.payments.index'), 'variant' => 'primary', 'icon' => 'mdi-shield-check']
            ],
            'type' => 'pending_list'
        ];
    }

    /**
     * Dapatkan rekap tunggakan siswa atau per kelas.
     */
    public function getStudentArrears(User $user, array $params): array
    {
        $className = trim($params['class'] ?? '');
        $studentKeyword = trim($params['student'] ?? '');

        // Jika user adalah siswa, paksa hanya cari data dirinya sendiri
        if ($user->role === 'siswa') {
            return $this->getMyStatus($user, $params);
        }

        if (!empty($studentKeyword)) {
            $student = Student::with(['class', 'bills.sppRate'])
                ->where('name', 'like', "%{$studentKeyword}%")
                ->orWhere('nis', $studentKeyword)
                ->first();

            if (!$student) {
                return [
                    'success' => false,
                    'status' => 'empty',
                    'title' => 'Siswa Tidak Ditemukan',
                    'response_type' => 'empty',
                    'message' => "Siswa dengan nama/NIS \"{$studentKeyword}\" tidak ditemukan.",
                    'data' => [],
                    'actions' => [],
                    'type' => 'arrears_detail'
                ];
            }

            $unpaidBills = $student->bills()->where('status', 'unpaid')->with('sppRate')->get();
            $totalArrears = $unpaidBills->sum(fn($b) => $b->sppRate->amount ?? 0);

            return [
                'success' => true,
                'status' => 'success',
                'title' => "Tunggakan {$student->name}",
                'response_type' => 'student',
                'message' => "{$unpaidBills->count()} bulan tagihan belum lunas (Rp " . number_format($totalArrears, 0, ',', '.') . ").",
                'data' => [
                    'student_name' => $student->name,
                    'nis' => $student->nis,
                    'class' => $student->class->name ?? '-',
                    'unpaid_months_count' => $unpaidBills->count(),
                    'total_arrears' => 'Rp ' . number_format($totalArrears, 0, ',', '.'),
                    'billing_url' => route('billing.index', $student->id),
                    'months' => $unpaidBills->map(fn($b) => Carbon::create()->month($b->month)->translatedFormat('F') . ' ' . $b->year)->toArray()
                ],
                'actions' => [
                    ['label' => 'Rincian Tagihan', 'url' => route('billing.index', $student->id), 'variant' => 'primary', 'icon' => 'mdi-receipt']
                ],
                'type' => 'arrears_detail'
            ];
        }

        // Jika filter kelas
        $query = Bill::with(['student.class', 'sppRate'])
            ->where('status', 'unpaid');

        if (!empty($className)) {
            $query->whereHas('student.class', function ($q) use ($className) {
                $q->where('name', 'like', "%{$className}%");
            });
        }

        $totalUnpaidCount = $query->count();
        $totalArrearsAmount = (clone $query)->get()->sum(fn($b) => $b->sppRate->amount ?? 0);
        $target = !empty($className) ? "Kelas {$className}" : "Seluruh Kelas";

        return [
            'success' => true,
            'status' => 'success',
            'title' => "Tunggakan {$target}",
            'response_type' => 'stat',
            'message' => "{$totalUnpaidCount} tagihan belum lunas senilai Rp " . number_format($totalArrearsAmount, 0, ',', '.') . ".",
            'data' => [
                'target' => $target,
                'unpaid_bills_count' => $totalUnpaidCount,
                'total_amount' => 'Rp ' . number_format($totalArrearsAmount, 0, ',', '.'),
                'report_url' => route('reports.arrears'),
            ],
            'actions' => [
                ['label' => 'Buka Laporan Tunggakan', 'url' => route('reports.arrears'), 'variant' => 'primary', 'icon' => 'mdi-file-chart']
            ],
            'type' => 'class_arrears_card'
        ];
    }

    /**
     * Buka / navigasi ke halaman sistem secara aman melalui whitelist.
     */
    public function navigatePage(User $user, array $params): array
    {
        $targetRoute = $params['route'] ?? null;

        if (!$targetRoute || !isset($this->whitelistedRoutes[$targetRoute])) {
            return [
                'success' => false,
                'status' => 'error',
                'title' => 'Halaman Tidak Ditemukan',
                'response_type' => 'error',
                'message' => 'Halaman yang dimaksud tidak terdaftar dalam sistem navigasi.',
                'data' => [],
                'actions' => [],
                'type' => 'error'
            ];
        }

        $routeInfo = $this->whitelistedRoutes[$targetRoute];

        // Validasi Role
        if (!in_array($user->role, $routeInfo['roles'])) {
            return [
                'success' => false,
                'status' => 'unauthorized',
                'title' => 'Akses Dibatasi',
                'response_type' => 'error',
                'message' => 'Kamu tidak memiliki akses untuk membuka halaman tersebut.',
                'data' => [],
                'actions' => [],
                'type' => 'unauthorized'
            ];
        }

        $url = route($targetRoute);

        return [
            'success' => true,
            'status' => 'success',
            'title' => 'Navigasi Cepat',
            'response_type' => 'nav',
            'message' => "Membuka halaman {$routeInfo['label']}...",
            'data' => [
                'url' => $url,
                'label' => $routeInfo['label'],
                'description' => $routeInfo['desc'] ?? '',
            ],
            'actions' => [
                ['label' => "Buka {$routeInfo['label']}", 'url' => $url, 'variant' => 'primary', 'icon' => 'mdi-arrow-right']
            ],
            'type' => 'navigation'
        ];
    }

    /**
     * Dapatkan status dan tunggakan akun siswa yang sedang login.
     * Boleh diakses oleh: siswa, admin, petugas.
     */
    public function getMyStatus(User $user, array $params = []): array
    {
        $student = $user->student;

        if (!$student && in_array($user->role, ['admin', 'petugas'])) {
            return [
                'success' => true,
                'status' => 'success',
                'title' => 'Informasi Akun',
                'response_type' => 'stat',
                'message' => "Anda login sebagai " . strtoupper($user->role) . " ({$user->name}). Gunakan perintah pencarian siswa atau rekap untuk mengelola data sekolah.",
                'data' => [
                    'role' => strtoupper($user->role),
                    'name' => $user->name,
                ],
                'actions' => [
                    ['label' => 'Buka Data Siswa', 'url' => route('students.index'), 'variant' => 'secondary', 'icon' => 'mdi-account-multiple']
                ],
                'type' => 'info'
            ];
        }

        if (!$student) {
            return [
                'success' => false,
                'status' => 'error',
                'title' => 'Profil Siswa',
                'response_type' => 'error',
                'message' => 'Profil siswa Anda tidak ditemukan dalam sistem.',
                'data' => [],
                'actions' => [],
                'type' => 'error'
            ];
        }

        $unpaidBills = $student->bills()->where('status', 'unpaid')->with('sppRate')->orderBy('year')->orderBy('month')->get();
        $paidBills = $student->bills()->where('status', 'paid')->count();
        $totalArrears = $unpaidBills->sum(fn($b) => $b->sppRate->amount ?? 0);
        $monthsUnpaid = $unpaidBills->map(fn($b) => Carbon::create()->month($b->month)->translatedFormat('F') . ' ' . $b->year)->toArray();

        $message = $unpaidBills->count() > 0
            ? "Terdapat {$unpaidBills->count()} bulan tagihan yang belum lunas."
            : "Seluruh tagihan SPP Anda telah lunas.";

        return [
            'success' => true,
            'status' => 'success',
            'title' => "Status SPP {$student->name}",
            'response_type' => 'student_self_status',
            'message' => $message,
            'data' => [
                'name' => $student->name,
                'nis' => $student->nis,
                'class' => $student->class->name ?? '-',
                'paid_count' => $paidBills,
                'unpaid_count' => $unpaidBills->count(),
                'total_arrears' => 'Rp ' . number_format($totalArrears, 0, ',', '.'),
                'unpaid_months' => $monthsUnpaid,
                'pay_url' => route('student.payments.create'),
                'history_url' => route('payments.history'),
            ],
            'actions' => [
                ['label' => 'Bayar SPP Sekarang', 'url' => route('student.payments.create'), 'variant' => 'primary', 'icon' => 'mdi-credit-card-outline'],
                ['label' => 'Riwayat Pembayaran', 'url' => route('payments.history'), 'variant' => 'secondary', 'icon' => 'mdi-history']
            ],
            'type' => 'student_self_status'
        ];
    }
}
