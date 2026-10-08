<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\Bill;
use App\Models\SppRate;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BillGenerationService
{
    public function __construct(
        protected AuditLogService $auditService
    ) {}

    /**
     * Generate tagihan SPP bulanan untuk seluruh siswa aktif.
     * Bersifat idempotent dan aman dari kehabisan memori (chunkById).
     *
     * @param int|null $month
     * @param int|null $year
     * @return array
     * @throws \DomainException
     */
    public function generateMonthlyBills(?int $month = null, ?int $year = null): array
    {
        $targetMonth = $month ?? now()->month;
        $targetYear  = $year ?? now()->year;

        // 1. Validasi tahun akademik aktif
        $activeYear = AcademicYear::where('is_active', true)->first();
        if (!$activeYear) {
            throw new \DomainException('Tahun akademik aktif belum diset di pengaturan sistem.');
        }

        // 2. Validasi tarif SPP untuk tahun akademik aktif
        $sppRate = SppRate::where('academic_year_id', $activeYear->id)->first();
        if (!$sppRate) {
            throw new \DomainException("Tarif SPP untuk tahun akademik {$activeYear->year} belum diatur.");
        }

        // 3. Tentukan tanggal jatuh tempo (tanggal 10 bulan berikutnya, aman terhadap pergantian tahun)
        $dueDate = Carbon::create($targetYear, $targetMonth, 1)->addMonth()->day(10)->toDateString();

        $createdCount = 0;
        $skippedCount = 0;
        $totalActiveStudents = Student::where('status', 'active')->count();

        // 4. Proses siswa aktif per batch 200 siswa untuk menghemat RAM
        Student::where('status', 'active')
            ->select('id', 'name', 'status')
            ->orderBy('id')
            ->chunkById(200, function ($students) use ($sppRate, $targetMonth, $targetYear, $dueDate, &$createdCount, &$skippedCount) {
                
                $studentIds = $students->pluck('id')->toArray();

                // Ambil daftar student_id yang sudah memiliki tagihan bulan ini
                $existingBillStudentIds = Bill::whereIn('student_id', $studentIds)
                    ->where('month', $targetMonth)
                    ->where('year', $targetYear)
                    ->pluck('student_id')
                    ->flip()
                    ->toArray();

                $billsToInsert = [];
                $now = now();

                foreach ($students as $student) {
                    if (isset($existingBillStudentIds[$student->id])) {
                        $skippedCount++;
                        continue;
                    }

                    $billsToInsert[] = [
                        'student_id'  => $student->id,
                        'spp_rate_id' => $sppRate->id,
                        'month'       => $targetMonth,
                        'year'        => $targetYear,
                        'due_date'    => $dueDate,
                        'status'      => 'unpaid',
                        'paid_at'     => null,
                        'created_at'  => $now,
                        'updated_at'  => $now,
                    ];

                    $createdCount++;
                }

                if (!empty($billsToInsert)) {
                    // Gunakan insertOrIgnore sebagai proteksi lapis ganda terhadap unique constraint
                    DB::table('bills')->insertOrIgnore($billsToInsert);
                }
            });

        // 5. Catat ke audit log
        $this->auditService->log('GENERATE_BILLS', null, null, [
            'academic_year' => $activeYear->year,
            'spp_rate_id'   => $sppRate->id,
            'amount'        => $sppRate->amount,
            'month'         => $targetMonth,
            'year'          => $targetYear,
            'created'       => $createdCount,
            'skipped'       => $skippedCount,
            'total_active'  => $totalActiveStudents,
        ]);

        return [
            'academic_year'         => $activeYear->year,
            'rate_amount'           => $sppRate->amount,
            'month'                 => $targetMonth,
            'year'                  => $targetYear,
            'due_date'              => $dueDate,
            'created_count'         => $createdCount,
            'skipped_count'         => $skippedCount,
            'total_active_students' => $totalActiveStudents,
        ];
    }
}
