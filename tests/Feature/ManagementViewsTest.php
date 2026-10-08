<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Bill;
use App\Models\Payment;
use App\Models\PaymentDetail;
use App\Models\SppRate;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ManagementViewsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected AcademicYear $year;
    protected StudentClass $class;
    protected SppRate $sppRate;
    protected Student $student;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'petugas']);
        Role::firstOrCreate(['name' => 'siswa']);

        $this->admin = User::create([
            'name'     => 'Admin Test',
            'email'    => 'admin@example.com',
            'password' => bcrypt('password'),
            'role'     => 'admin',
        ]);
        $this->admin->syncRoles(['admin']);

        $this->year = AcademicYear::create([
            'year'      => '2026/2027',
            'is_active' => true,
        ]);

        $this->class = StudentClass::create([
            'name'             => 'XII RPL 1',
            'grade_level'      => 12,
            'major'            => 'Rekayasa Perangkat Lunak',
            'academic_year_id' => $this->year->id,
        ]);

        $this->sppRate = SppRate::create([
            'academic_year_id' => $this->year->id,
            'amount'           => 250000,
        ]);

        $this->student = Student::create([
            'nis'              => '10001',
            'nisn'             => '0010001',
            'name'             => 'Budi Siswa',
            'gender'           => 'L',
            'class_id'         => $this->class->id,
            'academic_year_id' => $this->year->id,
            'status'           => 'active',
        ]);
    }

    public function test_student_management_views_render_successfully(): void
    {
        // 1. Index
        $res = $this->actingAs($this->admin)->get(route('students.index'));
        $res->assertStatus(200);
        $res->assertSee('Manajemen Data Siswa');

        // 2. Create
        $res = $this->actingAs($this->admin)->get(route('students.create'));
        $res->assertStatus(200);
        $res->assertSee('Tambah Siswa Baru');

        // 3. Edit
        $res = $this->actingAs($this->admin)->get(route('students.edit', $this->student->id));
        $res->assertStatus(200);
        $res->assertSee('Perbarui Data Siswa');

        // 4. Detail
        $res = $this->actingAs($this->admin)->get(route('students.detail', $this->student->id));
        $res->assertStatus(200);
        $res->assertSee('Informasi Lengkap Siswa');
    }

    public function test_payment_management_views_render_successfully(): void
    {
        $bill = Bill::create([
            'student_id'  => $this->student->id,
            'spp_rate_id' => $this->sppRate->id,
            'month'       => 7,
            'year'        => 2026,
            'due_date'    => '2026-07-10',
            'status'      => 'unpaid',
        ]);

        $payment = Payment::create([
            'student_id'   => $this->student->id,
            'payment_date' => now(),
            'status'       => 'pending',
        ]);

        PaymentDetail::create([
            'payment_id' => $payment->id,
            'bill_id'    => $bill->id,
            'amount'     => 250000,
        ]);

        // 1. Payment Index
        $res = $this->actingAs($this->admin)->get(route('admin.payments.index'));
        $res->assertStatus(200);
        $res->assertSee('Verifikasi Pembayaran SPP');

        // 2. Payment Show
        $res = $this->actingAs($this->admin)->get(route('admin.payments.show', $payment->id));
        $res->assertStatus(200);
        $res->assertSee('Verifikasi Transaksi Pembayaran');
    }

    public function test_bills_management_views_render_successfully(): void
    {
        $bill = Bill::create([
            'student_id'  => $this->student->id,
            'spp_rate_id' => $this->sppRate->id,
            'month'       => 7,
            'year'        => 2026,
            'due_date'    => '2026-07-10',
            'status'      => 'paid',
            'paid_at'     => now(),
        ]);

        $payment = Payment::create([
            'student_id'   => $this->student->id,
            'payment_date' => now(),
            'status'       => 'approved',
        ]);

        PaymentDetail::create([
            'payment_id' => $payment->id,
            'bill_id'    => $bill->id,
            'amount'     => 250000,
        ]);

        // 1. Bills Students Index
        $res = $this->actingAs($this->admin)->get(route('billing.students'));
        $res->assertStatus(200);
        $res->assertSee('Manajemen Tagihan SPP Siswa');

        // 2. Bills Student Sheet
        $res = $this->actingAs($this->admin)->get(route('billing.index', $this->student->id));
        $res->assertStatus(200);
        $res->assertSee('Lembar Tagihan SPP Siswa');

        // 3. Bills Show Detail
        $res = $this->actingAs($this->admin)->get(route('admin.bills.show', $bill->id));
        $res->assertStatus(200);
        $res->assertSee('Rincian Transaksi Pembayaran');
    }
}
