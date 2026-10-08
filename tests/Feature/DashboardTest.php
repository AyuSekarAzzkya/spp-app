<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Ensure roles exist in Spatie
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'petugas']);
        Role::firstOrCreate(['name' => 'siswa']);
    }

    public function test_admin_dashboard_can_be_rendered(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin_test@test.com'],
            ['name' => 'Admin Test', 'password' => bcrypt('password'), 'role' => 'admin']
        );
        $admin->syncRoles(['admin']);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Ringkasan performa administrasi');
        $response->assertSee('Pemasukan Bulan Ini');
        // Verify SPP Assistant Command Bar on Dashboard
        $response->assertSee('spp-dashboard-command-bar');
        $response->assertSee('Tanya SPP Assistant, cari data, atau buat laporan');
        // Verify Sidebar Structure & Standardized Icons
        $response->assertSee('sidebar-section-title');
        $response->assertSee('SPP Assistant');
        $response->assertSee('nav-icon');
        $response->assertSee('nav-text');
        // Verify Dashboard Chart Container Wrapper
        $response->assertSee('dashboard-chart-wrapper');
    }

    public function test_landing_page_remains_intact(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_petugas_dashboard_can_be_rendered(): void
    {
        $petugas = User::firstOrCreate(
            ['email' => 'petugas_test@test.com'],
            ['name' => 'Petugas Test', 'password' => bcrypt('password'), 'role' => 'petugas']
        );
        $petugas->syncRoles(['petugas']);

        $response = $this->actingAs($petugas)->get(route('petugas.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Portal Operasional Petugas');
        $response->assertSee('Menunggu Verifikasi');
    }

    public function test_student_dashboard_can_be_rendered(): void
    {
        $academicYear = AcademicYear::firstOrCreate(
            ['year' => '2026/2027'],
            ['is_active' => true]
        );

        $class = StudentClass::firstOrCreate(
            ['name' => 'X RPL 1'],
            ['academic_year_id' => $academicYear->id]
        );

        $user = User::firstOrCreate(
            ['email' => 'siswa_test@test.com'],
            ['name' => 'Siswa Test', 'password' => bcrypt('password'), 'role' => 'siswa']
        );
        $user->syncRoles(['siswa']);

        $student = Student::firstOrCreate(
            ['user_id' => $user->id],
            [
                'nis' => '1234567890',
                'name' => 'Siswa Test',
                'gender' => 'L',
                'student_class_id' => $class->id,
                'academic_year_id' => $academicYear->id,
                'status' => 'active'
            ]
        );

        $response = $this->actingAs($user)->get(route('student.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Portal Pembayaran SPP Siswa');
        $response->assertSee('Sisa Tagihan SPP');
        $response->assertSee('Rekening Resmi Pembayaran');
    }
}
