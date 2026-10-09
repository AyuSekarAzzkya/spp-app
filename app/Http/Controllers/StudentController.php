<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\AcademicYear;
use App\Models\StudentClass;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yajra\DataTables\DataTables;

class StudentController extends Controller
{
    public function index()
    {
        // View didukung 100% oleh Server-Side DataTables AJAX, tidak perlu query berat di sini
        return view('admin.student.index');
    }

    public function data()
    {
        $query = Student::with([
            'class:id,name',
            'academicYear:id,year',
            'user:id,email'
        ])->select('students.*');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('gender_text', function ($row) {
                return $row->gender == 'L' ? 'Laki-laki' : ($row->gender == 'P' ? 'Perempuan' : '-');
            })
            ->addColumn('class_name', fn($row) => $row->class->name ?? '-')
            ->addColumn('year_name', fn($row) => $row->academicYear->year ?? '-')
            ->addColumn('status_badge', function ($row) {
                if ($row->status == 'active') {
                    return '<span class="badge badge-lunas"><i class="mdi mdi-check-circle-outline"></i> Aktif</span>';
                }
                return '<span class="badge badge-secondary"><i class="mdi mdi-close-circle-outline"></i> Nonaktif</span>';
            })
            ->addColumn('action', function ($row) {
                return '
                    <div class="d-flex align-items-center justify-content-center gap-1">
                        <a href="' . route('students.detail', $row->id) . '" class="btn btn-sm btn-outline-secondary" title="Detail Siswa">
                            <i class="mdi mdi-eye-outline"></i>
                        </a>
                        <a href="' . route('students.edit', $row->id) . '" class="btn btn-sm btn-secondary" title="Edit Data">
                            <i class="mdi mdi-pencil-outline"></i>
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-danger btn-delete" data-id="' . $row->id . '" title="Hapus Siswa">
                            <i class="mdi mdi-trash-can-outline"></i>
                        </button>
                        <form id="deleteForm' . $row->id . '" action="' . route('students.destroy', $row->id) . '" method="POST" class="d-none">
                            ' . csrf_field() . method_field('DELETE') . '
                        </form>
                    </div>
                ';
            })

            ->filterColumn('class_name', function ($query, $keyword) {
                $query->whereHas('class', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('year_name', function ($query, $keyword) {
                $query->whereHas('academicYear', function ($q) use ($keyword) {
                    $q->where('year', 'like', "%{$keyword}%");
                });
            })
            ->rawColumns(['status_badge', 'action'])
            ->make(true);
    }
    public function create()
    {
        $classes = StudentClass::orderBy('grade_level')->get();
        $years   = AcademicYear::orderBy('year', 'desc')->get();

        return view('admin.student.create', compact('classes', 'years'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nis' => 'required|unique:students',
            'nisn' => 'nullable|unique:students',
            'name' => 'required',
            'phone' =>  'nullable',
            'gender' => 'nullable|in:L,P',
            'address' => 'nullable',
            'class_id' => 'required',
            'academic_year_id' => 'required',
        ]);

        Student::create($request->all());

        return redirect()->route('students.index')->with('success', 'Siswa berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $student = Student::findOrFail($id);
        $classes = StudentClass::all();
        $years   = AcademicYear::all();

        return view('admin.student.edit', compact('student', 'classes', 'years'));
    }

    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $request->validate([
            'nis' => 'required|unique:students,nis,' . $student->id,
            'nisn' => 'nullable|unique:students,nisn,' . $student->id,
            'name' => 'required',
            'phone' => 'nullable',
            'gender' => 'nullable|in:L,P',
            'address' => 'nullable',
            'class_id' => 'required',
            'academic_year_id' => 'required',
        ]);

        $student->update(array_merge($request->all(), [
            'status' => $request->status,
        ]));

        return redirect()->route('students.index')
            ->with('success', 'Data siswa berhasil diperbarui!');
    }


    public function destroy($id)
    {
        $student = Student::findOrFail($id);

        // Jika siswa memiliki riwayat transaksi approved/pending, lakukan soft delete tanpa merusak rekaman keuangan
        $hasPayments = $student->payments()->whereIn('status', ['approved', 'pending'])->exists();
        if ($hasPayments) {
            $student->update(['status' => 'inactive']);
            $student->delete();
            return redirect()->route('students.index')->with('success', 'Siswa memiliki riwayat pembayaran. Data berhasil diarsipkan (soft delete) dan dinonaktifkan demi menjaga integritas data keuangan.');
        }

        // Jika hanya memiliki tagihan belum lunas tanpa pembayaran, bersihkan tagihan unpaid
        $student->bills()->where('status', 'unpaid')->delete();
        $student->delete();

        return redirect()->route('students.index')->with('success', 'Data siswa berhasil dihapus!');
    }

    /**
     * Import data siswa dari berkas Spreadsheet (Excel/CSV)
     * Dioptimalkan dengan PhpSpreadsheet ReadDataOnly, In-Memory Lookups, Single DB Transaction, dan Idempotent User Linkage.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv,txt|max:20480',
        ], [
            'file.required' => 'Silakan pilih file spreadsheet yang ingin diimport.',
            'file.mimes'    => 'Format file harus berupa .xlsx, .xls, atau .csv.',
            'file.max'      => 'Ukuran file maksimal adalah 20MB.',
        ]);

        // Tingkatkan batas eksekusi & memori untuk menangani hingga 50.000 baris dengan aman
        @ini_set('max_execution_time', '600');
        @ini_set('memory_limit', '512M');

        $file = $request->file('file');
        $filePath = $file->getRealPath();
        $extension = strtolower($file->getClientOriginalExtension());

        // 1. Parsing Berkas: Streaming native fgetcsv untuk CSV/TXT (super hemat RAM & instan), atau PhpSpreadsheet ReadDataOnly untuk Excel
        $rows = [];
        if (in_array($extension, ['csv', 'txt'])) {
            if (($handle = fopen($filePath, 'r')) !== false) {
                // Deteksi delimiter (, atau ;)
                $firstLine = fgets($handle);
                $delimiter = (substr_count($firstLine, ';') > substr_count($firstLine, ',')) ? ';' : ',';
                rewind($handle);

                while (($data = fgetcsv($handle, 0, $delimiter)) !== false) {
                    $rows[] = $data;
                }
                fclose($handle);
            }
        } else {
            try {
                $reader = IOFactory::createReaderForFile($filePath);
                $reader->setReadDataOnly(true);
                $reader->setReadEmptyCells(false);
                $spreadsheet = $reader->load($filePath);
                $sheet = $spreadsheet->getActiveSheet();
                $rows = $sheet->toArray(null, true, true, false);
            } catch (\Throwable $e) {
                return back()->with('error', 'Gagal membaca berkas spreadsheet: ' . $e->getMessage());
            }
        }

        if (empty($rows)) {
            return back()->with('error', 'Berkas spreadsheet kosong atau tidak memiliki data.');
        }

        $header = array_shift($rows);
        if (empty($header) || empty($rows)) {
            return back()->with('error', 'Berkas spreadsheet tidak memiliki baris data.');
        }

        // 2. Petakan kolom secara fleksibel (case-insensitive & multibahasa)
        $mapping = [];
        foreach ($header as $col => $value) {
            if ($value !== null && trim((string)$value) !== '') {
                $key = strtolower(trim((string)$value));
                $mapping[$key] = $col;
            }
        }

        $findCol = function (array $aliases) use ($mapping) {
            foreach ($aliases as $alias) {
                if (isset($mapping[$alias])) {
                    return $mapping[$alias];
                }
            }
            return null;
        };

        $colName         = $findCol(['name', 'nama', 'nama siswa', 'nama lengkap']);
        $colNis          = $findCol(['nis', 'nomor induk', 'no induk']);
        $colNisn         = $findCol(['nisn']);
        $colGender       = $findCol(['gender', 'jenis kelamin', 'jk']);
        $colPhone        = $findCol(['phone', 'telepon', 'no telp', 'no telepon', 'hp']);
        $colAddress      = $findCol(['address', 'alamat']);
        $colEmail        = $findCol(['email']);
        $colPassword     = $findCol(['password', 'kata sandi']);
        $colClass        = $findCol(['class', 'kelas', 'nama kelas']);
        $colGrade        = $findCol(['grade level', 'tingkat', 'tingkat kelas', 'grade']);
        $colMajor        = $findCol(['major', 'jurusan']);
        $colYear         = $findCol(['academic year', 'tahun ajaran', 'tahun akademik']);

        if ($colNis === null || $colName === null) {
            return back()->with('error', 'Format berkas tidak sesuai: Kolom "nis" dan "name" wajib ada pada baris pertama.');
        }

        // 3. Pre-load in-memory lookups untuk MENGELIMINASI 100% masalah N+1 Database Queries
        $classes = StudentClass::withTrashed()->get();
        $classMap = [];
        foreach ($classes as $c) {
            $compositeKey = strtolower(trim($c->name)) . '|' . strtolower(trim((string)$c->grade_level)) . '|' . strtolower(trim((string)$c->major));
            $classMap[$compositeKey] = $c->id;
            $nameKey = strtolower(trim($c->name));
            if (!isset($classMap[$nameKey])) {
                $classMap[$nameKey] = $c->id;
            }
        }

        $years = AcademicYear::withTrashed()->get();
        $yearMap = [];
        foreach ($years as $y) {
            $yearMap[strtolower(trim($y->year))] = $y->id;
        }

        $defaultActiveYear = AcademicYear::where('is_active', true)->first();

        // Ambil seluruh NIS dan email yang sudah ada ke dalam Set O(1)
        $existingStudents = Student::withTrashed()->get(['id', 'nis', 'user_id', 'deleted_at'])->keyBy('nis');
        $existingUsers = User::pluck('id', 'email')->toArray();

        // Cek Spatie Role 'siswa' hanya satu kali di awal untuk mencegah exception overhead berulang
        $siswaRole = null;
        if (class_exists(\Spatie\Permission\Models\Role::class)) {
            $siswaRole = \Spatie\Permission\Models\Role::where('name', 'siswa')->first();
        }

        $importedCount = 0;
        $updatedCount  = 0;
        $skippedCount  = 0;

        // 4. Eksekusi seluruh baris dalam satu DATABASE TRANSACTION tunggal dengan Chunked Batch Ingestion (500 baris per batch)
        DB::transaction(function () use (
            $rows,
            $colName, $colNis, $colNisn, $colGender, $colPhone, $colAddress,
            $colEmail, $colPassword, $colClass, $colGrade, $colMajor, $colYear,
            &$classMap, &$yearMap, $defaultActiveYear, $siswaRole,
            &$existingStudents, &$existingUsers,
            &$importedCount, &$updatedCount, &$skippedCount
        ) {
            $now = now();
            $passwordCache = [];
            $newUsersBatch = [];
            $newStudentsMetaBatch = [];

            $flushBatch = function () use (
                &$newUsersBatch, &$newStudentsMetaBatch, &$existingUsers,
                &$importedCount, $siswaRole, $now
            ) {
                if (empty($newUsersBatch)) {
                    return;
                }

                // A. Bulk insert users (1 SQL query)
                DB::table('users')->insert($newUsersBatch);

                // B. Ambil user_id yang baru saja diinsert berdasarkan email (1 SQL query terindeks)
                $chunkEmails = array_column($newUsersBatch, 'email');
                $userMap = DB::table('users')->whereIn('email', $chunkEmails)->pluck('id', 'email')->toArray();

                // C. Siapkan record students dengan user_id yang valid
                $studentsToInsert = [];
                $rolesToInsert = [];

                foreach ($newStudentsMetaBatch as $meta) {
                    $userId = $userMap[$meta['email']] ?? null;
                    $studentsToInsert[] = [
                        'nis'              => $meta['nis'],
                        'nisn'             => $meta['nisn'],
                        'name'             => $meta['name'],
                        'phone'            => $meta['phone'],
                        'gender'           => $meta['gender'],
                        'address'          => $meta['address'],
                        'class_id'         => $meta['class_id'],
                        'academic_year_id' => $meta['academic_year_id'],
                        'user_id'          => $userId,
                        'status'           => 'active',
                        'created_at'       => $now,
                        'updated_at'       => $now,
                    ];

                    if ($siswaRole && $userId) {
                        $rolesToInsert[] = [
                            'role_id'    => $siswaRole->id,
                            'model_type' => 'App\\Models\\User',
                            'model_id'   => $userId,
                        ];
                    }
                }

                // D. Bulk insert students (1 SQL query)
                if (!empty($studentsToInsert)) {
                    DB::table('students')->insert($studentsToInsert);
                }

                // E. Bulk insert roles jika role terdaftar (1 SQL query)
                if (!empty($rolesToInsert)) {
                    DB::table('model_has_roles')->insertOrIgnore($rolesToInsert);
                }

                $importedCount += count($studentsToInsert);

                // Reset batch memori
                $newUsersBatch = [];
                $newStudentsMetaBatch = [];
            };

            foreach ($rows as $row) {
                $nis  = isset($colNis) && isset($row[$colNis]) ? trim((string)$row[$colNis]) : null;
                $name = isset($colName) && isset($row[$colName]) ? trim((string)$row[$colName]) : null;

                // Lewati baris kosong
                if (empty($nis) || empty($name)) {
                    $skippedCount++;
                    continue;
                }

                $nisn          = isset($colNisn) && isset($row[$colNisn]) ? trim((string)$row[$colNisn]) : null;
                $genderRaw     = isset($colGender) && isset($row[$colGender]) ? strtoupper(trim((string)$row[$colGender])) : null;
                $gender        = in_array($genderRaw, ['L', 'LAKI-LAKI', 'PRIA', 'M']) ? 'L' : (in_array($genderRaw, ['P', 'PEREMPUAN', 'WANITA', 'F']) ? 'P' : null);
                $phone         = isset($colPhone) && isset($row[$colPhone]) ? trim((string)$row[$colPhone]) : null;
                $address       = isset($colAddress) && isset($row[$colAddress]) ? trim((string)$row[$colAddress]) : null;
                $emailInput    = isset($colEmail) && isset($row[$colEmail]) ? strtolower(trim((string)$row[$colEmail])) : null;
                $passwordInput = isset($colPassword) && isset($row[$colPassword]) ? trim((string)$row[$colPassword]) : null;

                $className     = isset($colClass) && isset($row[$colClass]) ? trim((string)$row[$colClass]) : null;
                $gradeLevel    = isset($colGrade) && isset($row[$colGrade]) ? trim((string)$row[$colGrade]) : null;
                $major         = isset($colMajor) && isset($row[$colMajor]) ? trim((string)$row[$colMajor]) : null;
                $yearName      = isset($colYear) && isset($row[$colYear]) ? trim((string)$row[$colYear]) : null;

                // Resolusi Kelas (Memory lookup O(1))
                $classId = null;
                if ($className) {
                    $compositeKey = strtolower($className) . '|' . strtolower((string)$gradeLevel) . '|' . strtolower((string)$major);
                    $nameKey = strtolower($className);

                    if (isset($classMap[$compositeKey])) {
                        $classId = $classMap[$compositeKey];
                    } elseif (isset($classMap[$nameKey])) {
                        $classId = $classMap[$nameKey];
                    } else {
                        $newClass = StudentClass::create([
                            'name'        => $className,
                            'grade_level' => $gradeLevel,
                            'major'       => $major,
                        ]);
                        $classId = $newClass->id;
                        $classMap[$compositeKey] = $classId;
                        $classMap[$nameKey] = $classId;
                    }
                }

                // Resolusi Tahun Ajaran (Memory lookup O(1))
                $yearId = null;
                if ($yearName) {
                    $yearKey = strtolower($yearName);
                    if (isset($yearMap[$yearKey])) {
                        $yearId = $yearMap[$yearKey];
                    } else {
                        $newYear = AcademicYear::create([
                            'year'      => $yearName,
                            'is_active' => false,
                        ]);
                        $yearId = $newYear->id;
                        $yearMap[$yearKey] = $yearId;
                    }
                } elseif ($defaultActiveYear) {
                    $yearId = $defaultActiveYear->id;
                }

                // Cek apakah siswa sudah terdaftar sebelumnya
                $student = $existingStudents->get($nis);

                if ($student) {
                    // Update data siswa yang sudah ada tanpa re-hash password
                    $studentModel = Student::withTrashed()->find($student->id);
                    if ($studentModel) {
                        if ($studentModel->trashed()) {
                            $studentModel->restore();
                        }
                        $studentModel->update([
                            'name'             => $name,
                            'nisn'             => $nisn ?: $studentModel->nisn,
                            'gender'           => $gender ?: $studentModel->gender,
                            'phone'            => $phone ?: $studentModel->phone,
                            'address'          => $address ?: $studentModel->address,
                            'class_id'         => $classId ?: $studentModel->class_id,
                            'academic_year_id' => $yearId ?: $studentModel->academic_year_id,
                            'status'           => 'active',
                        ]);

                        if ($studentModel->user_id) {
                            User::where('id', $studentModel->user_id)->update(['name' => $name]);
                        }
                    }
                    $updatedCount++;
                } else {
                    // Siapkan Email unik
                    $email = $emailInput ?: ($nis . '@siswa.sekolah.id');
                    if (isset($existingUsers[$email])) {
                        $email = $nis . '.' . time() . '.' . mt_rand(10, 99) . '@siswa.sekolah.id';
                    }
                    $existingUsers[$email] = true;

                    // Password Hashing dengan In-Memory Cache (Cost 10, kompatibel penuh dengan Auth::attempt)
                    $rawPassword = $passwordInput ?: $nis;
                    if (!isset($passwordCache[$rawPassword])) {
                        $passwordCache[$rawPassword] = password_hash($rawPassword, PASSWORD_BCRYPT, ['cost' => 10]);
                    }
                    $hashedPassword = $passwordCache[$rawPassword];

                    $newUsersBatch[] = [
                        'name'       => $name,
                        'email'      => $email,
                        'password'   => $hashedPassword,
                        'role'       => 'siswa',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];

                    $newStudentsMetaBatch[] = [
                        'nis'              => $nis,
                        'nisn'             => $nisn,
                        'name'             => $name,
                        'phone'            => $phone,
                        'gender'           => $gender,
                        'address'          => $address,
                        'class_id'         => $classId,
                        'academic_year_id' => $yearId,
                        'email'            => $email,
                    ];

                    // Jika batch mencapai 500 baris, lakukan flush ke database
                    if (count($newUsersBatch) >= 500) {
                        $flushBatch();
                    }
                }
            }

            // Flush sisa baris terakhir
            $flushBatch();
        });

        $msg = "Import berhasil! {$importedCount} data siswa baru ditambahkan";
        if ($updatedCount > 0) {
            $msg .= ", {$updatedCount} data siswa diperbarui";
        }
        if ($skippedCount > 0) {
            $msg .= " ({$skippedCount} baris kosong dilewati)";
        }
        $msg .= '.';

        return back()->with('success', $msg);
    }


    public function detail($id)
    {
        $student = Student::with(['class', 'academicYear', 'user'])->findOrFail($id);
        return view('admin.student.detail', compact('student'));
    }
}
