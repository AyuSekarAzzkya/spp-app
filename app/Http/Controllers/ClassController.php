<?php

namespace App\Http\Controllers;

use App\Models\StudentClass;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function index()
    {
        $classes = StudentClass::orderBy('major')->orderBy('grade_level')->orderBy('name')->get();

        return view('admin.classes.index', compact('classes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required',
            'major'       => 'nullable|string',
            'grade_level' => 'nullable|string',
        ]);

        StudentClass::create($request->all());

        return back()->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'        => 'required',
            'major'       => 'nullable|string',
            'grade_level' => 'nullable|string',
        ]);

        $class = StudentClass::findOrFail($id);
        $class->update($request->all());

        return back()->with('success', 'Kelas berhasil diperbarui.');
    }

    public function destroy($id)
    {
        StudentClass::findOrFail($id)->delete();

        return back()->with('success', 'Kelas berhasil dihapus.');
    }

    public function students($id)
    {
        $class = StudentClass::findOrFail($id);
        $students = $class->students()->orderBy('name')->get();

        return view('admin.classes.students', compact('class', 'students'));
    }
}
