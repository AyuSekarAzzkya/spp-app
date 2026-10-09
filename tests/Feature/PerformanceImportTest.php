<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PerformanceImportTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'petugas', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'siswa', 'guard_name' => 'web']);

        $this->admin = User::create([
            'name'     => 'Admin Test',
            'email'    => 'admin@test.com',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);
        $this->admin->assignRole('admin');
    }

    public function test_student_import_creates_classes_years_students_and_users_atomically(): void
    {
        // 1. Buat berkas CSV mock dengan banyak siswa
        $csvHeader = "nis,name,nisn,gender,phone,address,class,grade level,major,academic year\n";
        $csvRows = [
            "2026001,Ahmad Dahlan,0012345671,L,081234567891,Jl. Merdeka No 1,X PPLG 1,10,PPLG,2025/2026",
            "2026002,Siti Fatimah,0012345672,P,081234567892,Jl. Mawar No 2,X PPLG 1,10,PPLG,2025/2026",
            "2026003,Budi Santoso,0012345673,L,081234567893,Jl. Melati No 3,X DKV 1,10,DKV,2025/2026",
            "2026004,Dewi Lestari,0012345674,P,081234567894,Jl. Anggrek No 4,XI TJKT 2,11,TJKT,2024/2025",
        ];
        $csvContent = $csvHeader . implode("\n", $csvRows);

        $tempFile = UploadedFile::fake()->createWithContent('data_siswa.csv', $csvContent);

        // 2. Jalankan request import
        $response = $this->actingAs($this->admin)->post(route('students.import'), [
            'file' => $tempFile,
        ]);

        $response->assertSessionHas('success');
        $response->assertRedirect();

        // 3. Verifikasi kelas otomatis terbuat tanpa duplikasi
        $this->assertDatabaseHas('classes', ['name' => 'X PPLG 1', 'major' => 'PPLG']);
        $this->assertDatabaseHas('classes', ['name' => 'X DKV 1', 'major' => 'DKV']);
        $this->assertDatabaseHas('classes', ['name' => 'XI TJKT 2', 'major' => 'TJKT']);
        $this->assertEquals(3, StudentClass::count());

        // 4. Verifikasi tahun ajaran otomatis terbuat
        $this->assertDatabaseHas('academic_years', ['year' => '2025/2026']);
        $this->assertDatabaseHas('academic_years', ['year' => '2024/2025']);
        $this->assertEquals(2, AcademicYear::count());

        // 5. Verifikasi data siswa dan akun user
        $this->assertEquals(4, Student::count());

        $student1 = Student::where('nis', '2026001')->first();
        $this->assertNotNull($student1);
        $this->assertEquals('Ahmad Dahlan', $student1->name);
        $this->assertEquals('L', $student1->gender);
        $this->assertNotNull($student1->user_id);

        $user1 = User::find($student1->user_id);
        $this->assertNotNull($user1);
        $this->assertEquals('2026001@siswa.sekolah.id', $user1->email);
        $this->assertTrue(Hash::check('2026001', $user1->password));
        $this->assertEquals('siswa', $user1->role);

        // 6. Verifikasi tidak ada user yatim (jumlah user siswa harus tepat sama dengan jumlah siswa)
        $this->assertEquals(4, User::where('role', 'siswa')->count());
    }

    public function test_student_import_updates_existing_students_idempotently(): void
    {
        // Setup siswa awal
        $class = StudentClass::create(['name' => 'X PPLG 1', 'grade_level' => '10', 'major' => 'PPLG']);
        $year = AcademicYear::create(['year' => '2025/2026', 'is_active' => true]);

        $student = Student::create([
            'nis'              => '2026099',
            'name'             => 'Nama Lama',
            'phone'            => '081111111',
            'address'          => 'Alamat Lama',
            'class_id'         => $class->id,
            'academic_year_id' => $year->id,
            'status'           => 'active',
        ]);

        $initialUser = User::where('id', $student->user_id)->first();
        $this->assertNotNull($initialUser);

        // Import dengan NIS sama tetapi data diperbarui
        $csvHeader = "nis,name,nisn,gender,phone,address,class,grade level,major,academic year\n";
        $csvRow = "2026099,Nama Baru Terupdate,0099999999,L,082222222,Alamat Baru,X PPLG 1,10,PPLG,2025/2026\n";
        $tempFile = UploadedFile::fake()->createWithContent('update_siswa.csv', $csvHeader . $csvRow);

        $response = $this->actingAs($this->admin)->post(route('students.import'), [
            'file' => $tempFile,
        ]);

        $response->assertSessionHas('success');

        // Jumlah siswa tetap 1 (tidak duplikat)
        $this->assertEquals(1, Student::count());

        $student->refresh();
        $this->assertEquals('Nama Baru Terupdate', $student->name);
        $this->assertEquals('082222222', $student->phone);
        $this->assertEquals('Alamat Baru', $student->address);

        // Akun user tetap sama dan nama terupdate
        $userUpdated = User::find($student->user_id);
        $this->assertEquals('Nama Baru Terupdate', $userUpdated->name);
        $this->assertEquals($initialUser->id, $userUpdated->id);
    }

    public function test_student_index_loads_quickly_without_fetching_all_students_in_controller(): void
    {
        $response = $this->actingAs($this->admin)->get(route('students.index'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.student.index');
        // Pastikan view tidak dibebani variabel students koleksi besar
        $this->assertFalse(isset($response->original->getData()['students']));
    }

    public function test_batch_chunk_import_handles_hundreds_of_rows_cleanly_and_enables_student_login(): void
    {
        $csvHeader = "nis,name,gender,class,grade level,major,academic year\n";
        $csvRows = [];
        for ($i = 100; $i <= 250; $i++) {
            $csvRows[] = "2026{$i},Siswa Ke {$i},L,X PPLG 1,10,PPLG,2025/2026";
        }
        $csvContent = $csvHeader . implode("\n", $csvRows);

        $tempFile = UploadedFile::fake()->createWithContent('large_batch.csv', $csvContent);

        $response = $this->actingAs($this->admin)->post(route('students.import'), [
            'file' => $tempFile,
        ]);

        $response->assertSessionHas('success');
        $this->assertEquals(151, Student::count());
        $this->assertEquals(151, User::where('role', 'siswa')->count());

        // Verifikasi akun siswa dapat login dengan kredensial default NIS
        $student = Student::where('nis', '2026150')->first();
        $this->assertNotNull($student);
        $user = User::find($student->user_id);
        $this->assertNotNull($user);

        $loginSuccess = auth()->attempt([
            'email'    => $user->email,
            'password' => '2026150',
        ]);
        $this->assertTrue($loginSuccess);
    }

    public function test_import_rejects_missing_required_columns(): void
    {
        $csvContent = "telepon,alamat\n081234,Bogor";
        $tempFile = UploadedFile::fake()->createWithContent('invalid.csv', $csvContent);

        $response = $this->actingAs($this->admin)->post(route('students.import'), [
            'file' => $tempFile,
        ]);

        $response->assertSessionHas('error');
    }
}
