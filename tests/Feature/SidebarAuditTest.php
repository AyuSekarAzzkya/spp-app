<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SidebarAuditTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'petugas']);
        Role::firstOrCreate(['name' => 'siswa']);
    }

    /**
     * Test Admin Sidebar Structure, Menus, Alignment, and Footer
     */
    public function test_admin_sidebar_has_standardized_structure_and_all_menus(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin_sidebar_test@test.com'],
            ['name' => 'Admin Sidebar Test', 'password' => bcrypt('password'), 'role' => 'admin']
        );
        $admin->syncRoles(['admin']);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);

        // 1. Sidebar Header & Branding
        $response->assertSee('sidebar-header');
        $response->assertSee('brand-logo-badge');
        $response->assertSee('mdi-shield-check');
        $response->assertSee('sidebar-brand-text');
        $response->assertSee('brand-title-white');
        $response->assertSee('E-SPP');
        $response->assertSee('brand-title-orange');
        $response->assertSee('SYSTEM');

        // 2. Sidebar Menu Wrapper
        $response->assertSee('sidebar-menu-wrapper');

        // 3. Section Titles
        $response->assertSee('sidebar-section-title');
        $response->assertSee('Utama');
        $response->assertSee('Master Data');
        $response->assertSee('Transaksi');
        $response->assertSee('Laporan');
        $response->assertSee('Pengaturan');

        // 4. Icons and Text Classes
        $response->assertSee('nav-icon');
        $response->assertSee('nav-text');

        // 5. Menu Items (including Verifikasi Pembayaran & SPP Assistant)
        $response->assertSee('Dashboard');
        $response->assertSee('Data Siswa');
        $response->assertSee('Data Kelas');
        $response->assertSee('Tahun Ajaran');
        $response->assertSee('Tarif SPP');
        $response->assertSee('Tagihan Siswa');
        $response->assertSee('Verifikasi Pembayaran');
        $response->assertSee('Laporan Keuangan');
        $response->assertSee('Data Pengguna');
        $response->assertSee('SPP Assistant');
        $response->assertSee('sidebar-kbd-badge');
        $response->assertSee('Ctrl K');

        // 6. Sidebar Footer & Logout
        $response->assertSee('sidebar-footer');
        $response->assertSee('sidebar-logout-btn');
        $response->assertSee('Keluar');
        $response->assertSee('sidebar-logout-form');
    }

    /**
     * Test Petugas Sidebar Structure and Menus
     */
    public function test_petugas_sidebar_has_standardized_structure(): void
    {
        $petugas = User::firstOrCreate(
            ['email' => 'petugas_sidebar_test@test.com'],
            ['name' => 'Petugas Sidebar Test', 'password' => bcrypt('password'), 'role' => 'petugas']
        );
        $petugas->syncRoles(['petugas']);

        $response = $this->actingAs($petugas)->get(route('petugas.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('sidebar-header');
        $response->assertSee('sidebar-menu-wrapper');
        $response->assertSee('Verifikasi Pembayaran');
        $response->assertSee('sidebar-footer');
        $response->assertSee('sidebar-logout-btn');
    }

    /**
     * Test Siswa Sidebar Structure and Menus
     */
    public function test_siswa_sidebar_has_standardized_structure(): void
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
            ['email' => 'siswa_sidebar_test@test.com'],
            ['name' => 'Siswa Sidebar Test', 'password' => bcrypt('password'), 'role' => 'siswa']
        );
        $user->syncRoles(['siswa']);

        Student::firstOrCreate(
            ['user_id' => $user->id],
            [
                'nis' => '9988776655',
                'name' => 'Siswa Sidebar Test',
                'gender' => 'P',
                'student_class_id' => $class->id,
                'academic_year_id' => $academicYear->id,
                'status' => 'active'
            ]
        );

        $response = $this->actingAs($user)->get(route('student.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('sidebar-header');
        $response->assertSee('sidebar-menu-wrapper');
        $response->assertSee('Bayar SPP Sekarang');
        $response->assertSee('sidebar-footer');
        $response->assertSee('sidebar-logout-btn');
    }

    /**
     * Test CSS Rules Conformity in admin-theme.css
     */
    public function test_admin_theme_css_rules_conformity(): void
    {
        $cssPath = public_path('css/admin-theme.css');
        $this->assertFileExists($cssPath);

        $css = file_get_contents($cssPath);

        // Check CSS variables & colors
        $this->assertStringContainsString('--navy-primary: #0F172A;', $css);
        $this->assertStringContainsString('--orange-brand: #F87B1B;', $css);

        // Check layout widths
        $this->assertStringContainsString('width: 260px !important;', $css);
        $this->assertStringContainsString('width: 78px !important;', $css);
        $this->assertStringContainsString('margin-left: 260px !important;', $css);
        $this->assertStringContainsString('margin-left: 78px !important;', $css);

        // Check overflow protection & flex layout
        $this->assertStringContainsString('overflow-x: hidden !important;', $css);
        $this->assertStringContainsString('overflow-y: auto !important;', $css);
        $this->assertStringContainsString('min-width: 0 !important;', $css);

        // Check sidebar classes
        $this->assertStringContainsString('.sidebar-header', $css);
        $this->assertStringContainsString('.sidebar-menu-wrapper', $css);
        $this->assertStringContainsString('.sidebar-footer', $css);
        $this->assertStringContainsString('.sidebar-logout-btn', $css);
        $this->assertStringContainsString('.sidebar-kbd-badge', $css);
    }
}
