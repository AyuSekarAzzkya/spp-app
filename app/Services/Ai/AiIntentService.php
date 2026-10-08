<?php

namespace App\Services\Ai;

use Carbon\Carbon;

class AiIntentService
{
    /**
     * Petakan prompt natural language ke intent dan parameter terstruktur.
     *
     * @param string $prompt
     * @return array [ 'intent' => string, 'parameters' => array, 'confidence' => float ]
     */
    public function detectIntent(string $prompt): array
    {
        $cleanPrompt = mb_strtolower(trim($prompt));

        // 1. Cek query mandiri siswa (tagihan saya, tunggakan saya, riwayat saya)
        if ($this->isSelfQuery($cleanPrompt)) {
            return [
                'intent' => 'get_my_status',
                'parameters' => [],
                'confidence' => 0.98
            ];
        }

        // 2. Cek intent navigasi halaman (whitelist routes)
        $navigationIntent = $this->detectNavigationIntent($cleanPrompt);
        if ($navigationIntent !== null) {
            return $navigationIntent;
        }

        // 3. Intent: Rekap Laporan Bulanan (buat rekap pembayaran september, dsb.)
        if (preg_match('/(buat rekap|rekap pembayaran|rekap bulan|laporan bulan|rekap transaksi)/i', $cleanPrompt)) {
            $month = $this->extractMonth($cleanPrompt);
            $year = $this->extractYear($cleanPrompt);

            return [
                'intent' => 'get_monthly_report',
                'parameters' => [
                    'month' => $month ?? now()->month,
                    'year' => $year ?? now()->year,
                ],
                'confidence' => 0.95
            ];
        }

        // 4. Intent: Pembayaran Pending / Butuh Verifikasi
        if (preg_match('/(pembayaran pending|menunggu verifikasi|butuh verifikasi|belum disetujui|antrean verifikasi|approval)/i', $cleanPrompt)) {
            return [
                'intent' => 'get_pending_payments',
                'parameters' => [],
                'confidence' => 0.95
            ];
        }

        // 5. Intent: Total / Ringkasan Pembayaran / Pemasukan
        if (preg_match('/(pemasukan|total pembayaran|jumlah pembayaran|berapa.*pembayaran|pendapatan|pembayaran hari ini)/i', $cleanPrompt)) {
            $month = $this->extractMonth($cleanPrompt);
            $year = $this->extractYear($cleanPrompt);
            $period = preg_match('/(hari ini|today)/i', $cleanPrompt) ? 'today' : 'month';

            return [
                'intent' => 'get_payment_summary',
                'parameters' => [
                    'period' => $period,
                    'month' => $month ?? now()->month,
                    'year' => $year ?? now()->year,
                ],
                'confidence' => 0.92
            ];
        }

        // 6. Intent: Siswa Belum Bayar / Tunggakan
        if (preg_match('/(belum bayar|belum lunas|menunggak|tunggakan|nunggak)/i', $cleanPrompt)) {
            $month = $this->extractMonth($cleanPrompt);
            $year = $this->extractYear($cleanPrompt);
            $class = $this->extractClass($cleanPrompt);

            // Jika query menyebutkan kelas tertentu (misal: "tunggakan kelas XII PPLG")
            if (!empty($class)) {
                return [
                    'intent' => 'get_student_arrears',
                    'parameters' => [
                        'class' => $class,
                    ],
                    'confidence' => 0.95
                ];
            }

            $student = $this->extractStudentName($cleanPrompt);

            // Jika spesifik mencari tunggakan 1 nama siswa
            if (!empty($student)) {
                return [
                    'intent' => 'get_student_arrears',
                    'parameters' => [
                        'student' => $student,
                    ],
                    'confidence' => 0.94
                ];
            }

            // Jika menanyakan rekap tunggakan umum
            if (preg_match('/(rekap tunggakan|total tunggakan)/i', $cleanPrompt)) {
                return [
                    'intent' => 'get_student_arrears',
                    'parameters' => [
                        'class' => null,
                    ],
                    'confidence' => 0.92
                ];
            }

            return [
                'intent' => 'get_unpaid_students',
                'parameters' => [
                    'month' => $month ?? now()->month,
                    'year' => $year ?? now()->year,
                    'class' => null,
                ],
                'confidence' => 0.93
            ];
        }

        // 7. Intent: Pencarian Siswa (Cari siswa / NIS)
        if (preg_match('/(cari|temukan|profil|nama siswa|nis)\s+/i', $cleanPrompt) || preg_match('/^siswa\s+/i', $cleanPrompt)) {
            $keyword = preg_replace('/^(cari(kan)?|temukan|lihat|profil|data)?\s*(siswa|murid)?\s*(bernama|dengan nis|atas nama)?\s*/i', '', $prompt);
            $keyword = trim($keyword);

            if (!empty($keyword)) {
                return [
                    'intent' => 'search_students',
                    'parameters' => [
                        'query' => $keyword,
                    ],
                    'confidence' => 0.92
                ];
            }
        }

        // 8. Fallback cerdas: Jika prompt menyebutkan kata kunci pendek tanpa struktur (misal nama/NIS langsung)
        if (strlen($cleanPrompt) >= 3 && !preg_match('/\b(halo|hai|p|test|ai)\b/i', $cleanPrompt)) {
            return [
                'intent' => 'search_students',
                'parameters' => [
                    'query' => $prompt,
                ],
                'confidence' => 0.65
            ];
        }

        return [
            'intent' => 'unknown',
            'parameters' => [],
            'confidence' => 0.0
        ];
    }

    /**
     * Deteksi perintah navigasi langsung ke halaman tertentu.
     */
    protected function detectNavigationIntent(string $prompt): ?array
    {
        $navigationMap = [
            'students.index'        => ['/(buka|ke|halaman|lihat)\s+(data\s+)?siswa\b/i', '/^data\s+siswa$/i'],
            'students.create'       => ['/(tambah|input|buat)\s+(data\s+)?siswa/i'],
            'admin.payments.index'  => ['/(lihat|buka|ke)\s+pembayaran\s+pending/i', '/(buka|ke|halaman)\s+verifikasi(\s+pembayaran)?/i'],
            'reports.index'         => ['/(buka|ke|lihat)\s+laporan(\s+bulan\s+ini|\s+pembayaran|\s+spp)?$/i'],
            'reports.arrears'       => ['/(buka|ke|lihat)\s+(laporan|rekap)\s+tunggakan/i'],
            'billing.students'      => ['/(buka|ke|lihat)\s+(data\s+)?tagihan/i'],
            'classes.index'         => ['/(buka|ke|halaman)\s+(data\s+)?kelas/i'],
            'academic-years.index'  => ['/(buka|ke|halaman)\s+tahun\s+ajaran/i'],
            'spp.index'             => ['/(buka|ke|halaman)\s+(tarif\s+spp|setting\s+spp)/i'],
            'users.index'           => ['/(buka|ke|halaman)\s+(user|pengguna|manajemen\s+user)/i'],
            'student.payments.create' => ['/(bayar\s+spp(\s+sekarang)?|buka\s+form\s+bayar)/i'],
            'payments.history'      => ['/(buka|lihat)\s+riwayat\s+pembayaran/i'],
        ];

        foreach ($navigationMap as $routeName => $patterns) {
            foreach ($patterns as $pattern) {
                if (preg_match($pattern, $prompt)) {
                    return [
                        'intent' => 'navigate_page',
                        'parameters' => [
                            'route' => $routeName,
                        ],
                        'confidence' => 0.95
                    ];
                }
            }
        }

        return null;
    }

    protected function isSelfQuery(string $prompt): bool
    {
        return (bool) preg_match('/(tagihan saya|tunggakan saya|status saya|pembayaran saya|punya saya|data saya|milik saya|riwayat saya|profil saya|berapa tunggakan saya|apakah saya sudah lunas|status spp saya)/i', $prompt);
    }

    protected function extractMonth(string $prompt): ?int
    {
        $months = [
            'januari' => 1, 'january' => 1, 'jan' => 1,
            'februari' => 2, 'february' => 2, 'feb' => 2,
            'maret' => 3, 'march' => 3, 'mar' => 3,
            'april' => 4, 'apr' => 4,
            'mei' => 5, 'may' => 5,
            'juni' => 6, 'june' => 6, 'jun' => 6,
            'juli' => 7, 'july' => 7, 'jul' => 7,
            'agustus' => 8, 'august' => 8, 'agu' => 8, 'ags' => 8,
            'september' => 9, 'sep' => 9, 'sept' => 9,
            'oktober' => 10, 'october' => 10, 'okt' => 10,
            'november' => 11, 'nov' => 11,
            'desember' => 12, 'december' => 12, 'des' => 12,
        ];

        foreach ($months as $name => $number) {
            if (str_contains($prompt, $name)) {
                return $number;
            }
        }

        if (preg_match('/(bulan ini|this month)/i', $prompt)) {
            return now()->month;
        }

        if (preg_match('/(bulan lalu|last month)/i', $prompt)) {
            return now()->subMonth()->month;
        }

        return null;
    }

    protected function extractYear(string $prompt): ?int
    {
        if (preg_match('/\b(202[0-9])\b/', $prompt, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }

    protected function extractClass(string $prompt): ?string
    {
        // Cari pola kelas seperti X RPL 1, XII PPLG, XI AKL, dsb.
        if (preg_match('/\b(x|xi|xii)\s*([a-z0-9\s-]+)/i', $prompt, $matches)) {
            $class = trim($matches[0]);
            // Bersihkan kata sambung di ujung jika ada
            $class = preg_replace('/\s+(bulan|tahun|yang|belum|sudah).*$/i', '', $class);
            return strtoupper(trim($class));
        }

        return null;
    }

    protected function extractStudentName(string $prompt): ?string
    {
        if (preg_match('/(siswa|bernama|atas nama|an|tunggakan siswa)\s+([a-zA-Z\s]+)/i', $prompt, $matches)) {
            $name = trim($matches[2]);
            $name = preg_replace('/(yang|belum|sudah|di|kelas|bulan|tahun|ini).*$/i', '', $name);
            $name = trim($name);
            // Jangan anggap kata kelas atau tunggakan sebagai nama siswa
            if (!empty($name) && !preg_match('/^(x|xi|xii|kelas|tunggakan)\b/i', $name)) {
                return $name;
            }
        }

        return null;
    }
}
