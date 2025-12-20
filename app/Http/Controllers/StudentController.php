<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\AcademicYear;
use App\Models\StudentClass;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yajra\DataTables\DataTables;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::with(['class', 'academicYear'])->get();
        return view('admin.student.index', compact('students'));
    }

    public function data()
    {
        $query = Student::with(['class', 'academicYear', 'user']);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('gender_text', function ($row) {
                return $row->gender == 'L' ? 'Laki-laki' : ($row->gender == 'P' ? 'Perempuan' : '-');
            })
            ->addColumn('class_name', fn($row) => $row->class->name ?? '-')

            ->addColumn('year_name', function ($row) {
                return $row->academicYear->year ?? '-';
            })
            ->addColumn('email', function ($row) {
                return $row->user->email ?? '-';
            })
            ->addColumn('status_badge', function ($row) {
                $color = $row->status == 'active' ? 'success' : 'secondary';
                return '<span class="badge bg-' . $color . '">' . ucfirst($row->status) . '</span>';
            })
            ->addColumn('action', function ($row) {
                return '
                <div class="d-flex gap-2">
                    <a href="' . route('students.detail', $row->id) . '" class="btn btn-sm btn-outline-info">Detail</a>
                    <a href="' . route('students.edit', $row->id) . '" class="btn btn-sm btn-outline-warning">Edit</a>
                    <button class="btn btn-sm btn-outline-danger btn-delete" data-id="' . $row->id . '">Hapus</button>
                    <form id="deleteForm' . $row->id . '" action="' . route('students.destroy', $row->id) . '" method="POST" class="d-none">
                        ' . csrf_field() . method_field('DELETE') . '
                    </form>
                </div>
            ';
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

        // Validasi input
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
            'status' => 'active',
        ]));

        return redirect()->route('students.index')
            ->with('success', 'Data siswa berhasil diperbarui!');
    }


    public function destroy($id)
    {
        Student::findOrFail($id)->delete();
        return redirect()->route('students.index')->with('success', 'Siswa berhasil dihapus!');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        $spreadsheet = IOFactory::load($request->file('file'));
        $sheet = $spreadsheet->getActiveSheet();

        $rows = $sheet->toArray(null, true, true, true);
        $header = array_shift($rows);

        $mapping = [];
        foreach ($header as $col => $value) {
            $key = strtolower(trim($value));
            $mapping[$key] = $col;
        }

        foreach ($rows as $row) {
            $name = $row[$mapping['name']] ?? null;
            $nis = $row[$mapping['nis']] ?? null;
            $nisn = $row[$mapping['nisn']] ?? null;
            $gender = $row[$mapping['gender']] ?? null;
            $phone = $row[$mapping['phone']] ?? null;
            $address = $row[$mapping['address']] ?? null;
            $email = $row[$mapping['email']] ?? null;
            $password = $row[$mapping['password']] ?? null;

            $className = $row[$mapping['class']] ?? null;
            $gradeLevel = $row[$mapping['grade level']] ?? null;
            $major = $row[$mapping['major']] ?? null;

            $yearName = $row[$mapping['academic year']] ?? null;

            $class = StudentClass::firstOrCreate(
                ['name' => $className, 'grade_level' => $gradeLevel, 'major' => $major]
            );

            $year = AcademicYear::firstOrCreate(
                ['year' => $yearName]
            );

            if (!$email) {
                $baseEmail = preg_replace('/[^a-z0-9]/', '', strtolower($name));
                $email = $baseEmail . '@sekolah.com';

                $counter = 1;
                while (User::where('email', $email)->exists()) {
                    $email = $baseEmail . $counter . '@sekolah.com';
                    $counter++;
                }
            }

            if (!$password) {
                $password = $nis;
            }

            $student = Student::updateOrCreate(
                ['nis' => $nis],
                [
                    'name' => $name,
                    'nisn' => $nisn,
                    'gender' => $gender,
                    'phone' => $phone,
                    'address'  >$address,
                    'class_id' => $class->id,
                    'academic_year_id' => $year->id,
                ]
            );

            User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'password' => Hash::make($password),
                    'role' => 'siswa',
                    'student_id' => $student->id,
                ]
            );
        }

        return back()->with('success', 'Import siswa berhasil! Semua kelas dan tahun ajaran baru otomatis dibuat.');
    }


    public function detail($id)
    {
        $student = Student::with(['class', 'academicYear', 'user'])->findOrFail($id);
        return view('admin.student.detail', compact('student'));
    }
}
