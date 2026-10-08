<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Bill;
use App\Models\Payment;
use App\Models\PaymentDetail;
use App\Models\PaymentProof;
use App\Models\SppRate;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\User;
use App\Services\BillGenerationService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RevisiSistemTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $petugas;
    protected User $siswaUser1;
    protected User $siswaUser2;
    protected Student $student1;
    protected Student $student2;
    protected StudentClass $class;
    protected AcademicYear $academicYear;
    protected SppRate $sppRate;

    protected function setUp(): void
    {
        parent::setUp();

        $this->academicYear = AcademicYear::create([
            'year'      => '2026/2027',
            'semester'  => 'ganjil',
            'is_active' => true,
        ]);

        $this->sppRate = SppRate::create([
            'academic_year_id' => $this->academicYear->id,
            'amount'           => 350000,
            'description'      => 'Tarif SPP 2026/2027',
        ]);

        $this->class = StudentClass::create([
            'name'  => 'XII RPL 1',
            'major' => 'PPLG',
        ]);

        $this->admin = User::create([
            'name'     => 'Admin SPP',
            'email'    => 'admin_test@spp.test',
            'password' => bcrypt('password'),
            'role'     => 'admin',
        ]);

        $this->petugas = User::create([
            'name'     => 'Petugas Loket',
            'email'    => 'petugas_test@spp.test',
            'password' => bcrypt('password'),
            'role'     => 'petugas',
        ]);

        $this->siswaUser1 = User::create([
            'name'     => 'Ahmad Siswa',
            'email'    => 'ahmad@spp.test',
            'password' => bcrypt('password'),
            'role'     => 'siswa',
        ]);

        $this->student1 = Student::create([
            'user_id'          => $this->siswaUser1->id,
            'class_id'         => $this->class->id,
            'academic_year_id' => $this->academicYear->id,
            'nisn'             => '0011223344',
            'nis'              => '12001',
            'name'             => 'Ahmad Siswa',
            'gender'           => 'L',
            'status'           => 'active',
        ]);

        $this->siswaUser2 = User::create([
            'name'     => 'Budi Siswa',
            'email'    => 'budi@spp.test',
            'password' => bcrypt('password'),
            'role'     => 'siswa',
        ]);

        $this->student2 = Student::create([
            'user_id'          => $this->siswaUser2->id,
            'class_id'         => $this->class->id,
            'academic_year_id' => $this->academicYear->id,
            'nisn'             => '0011223355',
            'nis'              => '12002',
            'name'             => 'Budi Siswa',
            'gender'           => 'L',
            'status'           => 'active',
        ]);
    }

    /**
     * 1. Test Due Date Logic (Oktober -> November, Desember -> Januari pergantian tahun).
     */
    public function test_due_date_is_calculated_to_the_next_month_safely(): void
    {
        $service = app(BillGenerationService::class);

        // Test Cases: [targetMonth, targetYear, expectedDueYear, expectedDueMonth]
        $cases = [
            [1, 2026, 2026, 2],    // Januari -> Februari
            [9, 2026, 2026, 10],   // September -> Oktober
            [10, 2026, 2026, 11],  // Oktober -> November
            [11, 2026, 2026, 12],  // November -> Desember
            [12, 2026, 2027, 1],   // Desember -> Januari tahun berikutnya!
        ];

        foreach ($cases as [$targetMonth, $targetYear, $expectedDueYear, $expectedDueMonth]) {
            $result = $service->generateMonthlyBills($targetMonth, $targetYear);

            $this->assertEquals($targetMonth, $result['month']);
            $this->assertEquals($targetYear, $result['year']);

            $expectedDueDate = sprintf('%04d-%02d-10', $expectedDueYear, $expectedDueMonth);
            $this->assertEquals($expectedDueDate, $result['due_date']);

            // Periksa langsung ke DB untuk student1
            $bill = Bill::where('student_id', $this->student1->id)
                ->where('month', $targetMonth)
                ->where('year', $targetYear)
                ->first();

            $this->assertNotNull($bill);
            $this->assertEquals($targetMonth, $bill->month);
            $this->assertEquals($targetYear, $bill->year);
            $this->assertEquals($expectedDueDate, Carbon::parse($bill->due_date)->format('Y-m-d'));
            $this->assertEquals('unpaid', $bill->status);
            $this->assertNull($bill->paid_at);
        }
    }

    /**
     * 2 & 3 & 4. Test Payment Proof Authorization & Retrieval.
     */
    public function test_payment_proof_secure_route_authorization(): void
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->image('bukti_transfer.jpg');
        $storedPath = $file->store('payment-proofs', 'public');

        $payment = Payment::create([
            'student_id'   => $this->student1->id,
            'payment_date' => now(),
            'status'       => 'pending',
        ]);

        $proof = PaymentProof::create([
            'payment_id' => $payment->id,
            'image_path' => $storedPath,
            'amount'     => 350000,
            'version'    => 1,
        ]);

        $this->assertTrue($proof->fileExists());
        $this->assertEquals(route('payments.proof.show', $proof->id), $proof->url);

        // a. Siswa pemilik bukti BOLEH mengakses gambarnya
        $responseSiswa1 = $this->actingAs($this->siswaUser1)->get(route('payments.proof.show', $proof->id));
        $responseSiswa1->assertStatus(200);

        // b. Siswa lain DITOLAK (403 Forbidden)
        $responseSiswa2 = $this->actingAs($this->siswaUser2)->get(route('payments.proof.show', $proof->id));
        $responseSiswa2->assertStatus(403);

        // c. Petugas BOLEH mengakses bukti untuk verifikasi
        $responsePetugas = $this->actingAs($this->petugas)->get(route('payments.proof.show', $proof->id));
        $responsePetugas->assertStatus(200);

        // d. Admin BOLEH mengakses bukti
        $responseAdmin = $this->actingAs($this->admin)->get(route('payments.proof.show', $proof->id));
        $responseAdmin->assertStatus(200);

        // e. Guest / non-login diarahkan ke login
        auth()->logout();
        $responseGuest = $this->get(route('payments.proof.show', $proof->id));
        $responseGuest->assertRedirect(route('login'));
    }

    /**
     * Test history view authorization prevents Student A from seeing Student B's payment.
     */
    public function test_history_show_ownership_authorization(): void
    {
        $payment1 = Payment::create([
            'student_id'   => $this->student1->id,
            'payment_date' => now(),
            'status'       => 'approved',
        ]);

        // Siswa 1 bisa melihat pembayarannya
        $res1 = $this->actingAs($this->siswaUser1)->get(route('payments.history.show', $payment1->id));
        $res1->assertStatus(200);

        // Siswa 2 tidak bisa melihat pembayaran siswa 1 (403)
        $res2 = $this->actingAs($this->siswaUser2)->get(route('payments.history.show', $payment1->id));
        $res2->assertStatus(403);
    }

    /**
     * 5. Test Konsistensi Warna - Tidak ada hex purple/violet di tema admin.
     */
    public function test_admin_theme_css_has_no_purple(): void
    {
        $cssPath = public_path('css/admin-theme.css');
        $this->assertFileExists($cssPath);
        $css = file_get_contents($cssPath);

        $this->assertStringNotContainsString('#8b5cf6', $css);
        $this->assertStringNotContainsString('#b66dff', $css);
        $this->assertStringNotContainsString('#9a55ff', $css);
        $this->assertStringNotContainsString('#7c3aed', $css);
        $this->assertStringNotContainsString('#6366f1', $css);
        $this->assertStringContainsString('--navy-primary: #0F172A;', $css);
        $this->assertStringContainsString('--orange-brand: #F87B1B;', $css);
        $this->assertStringContainsString('--info: #2563eb;', $css);
    }

    /**
     * 6. Test Dashboard Petugas Buttons Bersebelahan.
     */
    public function test_petugas_dashboard_renders_action_buttons_side_by_side(): void
    {
        $res = $this->actingAs($this->petugas)->get(route('petugas.dashboard'));
        $res->assertStatus(200);
        $res->assertSee('petugas-action-buttons');
        $res->assertSee('Verifikasi Pembayaran');
        $res->assertSee('Cek Tagihan Siswa');
    }

    /**
     * 7 & 8. Test Sidebar Graduation Cap Icon & Mobile Drawer.
     */
    public function test_sidebar_has_graduation_cap_icons_and_mobile_drawer(): void
    {
        $resAdmin = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $resAdmin->assertStatus(200);
        $resAdmin->assertSee('mdi-school');
        $resAdmin->assertSee('mdi-school-outline');
        $resAdmin->assertSee('sidebarMobileCloseBtn');
        $resAdmin->assertSee('sidebar-offcanvas');

        $resPetugas = $this->actingAs($this->petugas)->get(route('petugas.dashboard'));
        $resPetugas->assertStatus(200);
        $resPetugas->assertSee('mdi-school');
        $resPetugas->assertSee('mdi-school-outline');
        $resPetugas->assertSee('sidebarMobileCloseBtn');
    }
}
