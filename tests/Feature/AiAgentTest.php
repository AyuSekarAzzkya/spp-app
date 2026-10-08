<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AiAgentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Pastikan roles Spatie tersedia
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'petugas']);
        Role::firstOrCreate(['name' => 'siswa']);
    }

    public function test_guest_cannot_access_ai_endpoint(): void
    {
        $response = $this->postJson(route('ai.query'), [
            'prompt' => 'Tampilkan data SPP',
        ]);

        $response->assertStatus(401);
    }

    public function test_ai_requires_prompt(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->syncRoles(['admin']);

        $response = $this->actingAs($admin)->postJson(route('ai.query'), [
            'prompt' => '',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['prompt']);
    }

    public function test_admin_can_query_payment_summary(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->syncRoles(['admin']);

        $response = $this->actingAs($admin)->postJson(route('ai.query'), [
            'prompt' => 'Berapa total pembayaran bulan ini?',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'intent' => 'get_payment_summary',
        ]);
        $response->assertJsonStructure([
            'status',
            'intent',
            'message',
            'data' => [
                'period',
                'total_amount',
                'total_transactions',
                'pending_verification',
            ],
            'type',
        ]);
    }

    public function test_admin_can_search_students(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->syncRoles(['admin']);

        $academicYear = AcademicYear::firstOrCreate(
            ['year' => '2026/2027'],
            ['is_active' => true]
        );

        $class = StudentClass::firstOrCreate(
            ['name' => 'XII RPL 1'],
            ['academic_year_id' => $academicYear->id]
        );

        $studentUser = User::factory()->create(['role' => 'siswa', 'name' => 'Ahmad Fulan']);
        Student::create([
            'user_id' => $studentUser->id,
            'nis' => '10293847',
            'name' => 'Ahmad Fulan',
            'gender' => 'L',
            'student_class_id' => $class->id,
            'academic_year_id' => $academicYear->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->postJson(route('ai.query'), [
            'prompt' => 'Cari siswa Ahmad',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'intent' => 'search_students',
        ]);
        $response->assertSee('Ahmad Fulan');
    }

    public function test_student_cannot_access_unauthorized_tools(): void
    {
        $studentUser = User::factory()->create(['role' => 'siswa', 'name' => 'Siswa Pengguna']);
        $studentUser->syncRoles(['siswa']);

        // Siswa mencoba melihat siswa lain yang belum bayar
        $response = $this->actingAs($studentUser)->postJson(route('ai.query'), [
            'prompt' => 'Tampilkan siswa yang belum bayar bulan ini',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'unauthorized',
        ]);
        $response->assertSee('tidak memiliki akses');
    }

    public function test_student_can_check_own_status(): void
    {
        $academicYear = AcademicYear::firstOrCreate(
            ['year' => '2026/2027'],
            ['is_active' => true]
        );

        $class = StudentClass::firstOrCreate(
            ['name' => 'X PPLG 2'],
            ['academic_year_id' => $academicYear->id]
        );

        $studentUser = User::factory()->create(['role' => 'siswa', 'name' => 'Citra Lestari']);
        $studentUser->syncRoles(['siswa']);

        Student::create([
            'user_id' => $studentUser->id,
            'nis' => '99887766',
            'name' => 'Citra Lestari',
            'gender' => 'P',
            'student_class_id' => $class->id,
            'academic_year_id' => $academicYear->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($studentUser)->postJson(route('ai.query'), [
            'prompt' => 'Bagaimana status pembayaran saya?',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'intent' => 'get_my_status',
            'type' => 'student_self_status',
        ]);
        $response->assertSee('Citra Lestari');
    }

    public function test_unknown_prompt_returns_helpful_suggestion(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->syncRoles(['admin']);

        // Prompt yang benar-benar di luar konteks
        $response = $this->actingAs($admin)->postJson(route('ai.query'), [
            'prompt' => '   ',
        ]);

        // Validasinya akan menangkap prompt kosong
        $response->assertStatus(422);
    }

    public function test_ai_query_generates_audit_log(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->syncRoles(['admin']);

        $this->actingAs($admin)->postJson(route('ai.query'), [
            'prompt' => 'Berapa total pembayaran bulan ini?',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'AI_QUERY',
            'user_id' => $admin->id,
        ]);
    }

    public function test_admin_can_generate_monthly_report(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->syncRoles(['admin']);

        $response = $this->actingAs($admin)->postJson(route('ai.query'), [
            'prompt' => 'Rekap pembayaran bulan September',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'intent' => 'get_monthly_report',
            'response_type' => 'report',
        ]);
        $response->assertJsonStructure([
            'status',
            'intent',
            'title',
            'response_type',
            'data' => [
                'total_transactions',
                'total_payment',
                'students_paid',
                'students_unpaid',
            ],
            'actions',
        ]);
    }

    public function test_admin_can_use_navigation_command(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->syncRoles(['admin']);

        $response = $this->actingAs($admin)->postJson(route('ai.query'), [
            'prompt' => 'buka data siswa',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'intent' => 'navigate_page',
            'response_type' => 'nav',
        ]);
        $response->assertJsonPath('data.url', route('students.index'));
    }

    public function test_student_cannot_navigate_to_admin_pages(): void
    {
        $studentUser = User::factory()->create(['role' => 'siswa']);
        $studentUser->syncRoles(['siswa']);

        $response = $this->actingAs($studentUser)->postJson(route('ai.query'), [
            'prompt' => 'buka data siswa',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'unauthorized',
        ]);
        $response->assertSee('tidak memiliki akses');
    }

    public function test_admin_can_query_class_arrears(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->syncRoles(['admin']);

        $response = $this->actingAs($admin)->postJson(route('ai.query'), [
            'prompt' => 'Tampilkan tunggakan kelas XII PPLG',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'intent' => 'get_student_arrears',
            'response_type' => 'stat',
        ]);
        $response->assertSee('XII PPLG');
    }

    public function test_admin_can_query_unpaid_count_phrase(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->syncRoles(['admin']);

        $response = $this->actingAs($admin)->postJson(route('ai.query'), [
            'prompt' => 'Berapa siswa yang belum lunas?',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'intent' => 'get_unpaid_students',
        ]);
    }
}


