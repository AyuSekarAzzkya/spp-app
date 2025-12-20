<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use Illuminate\Http\Request;

class AcademicYearController extends Controller
{
    public function index()
    {
        $data = AcademicYear::orderBy('year', 'desc')->get();

        return view('admin.academic_year.index', compact('data'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'year' => 'required|string|unique:academic_years,year',
        ]);

        AcademicYear::create([
            'year' => $request->year,
            'is_active' => false
        ]);

        return back()->with('success', 'Tahun ajaran berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'year' => 'required|string|unique:academic_years,year,' . $id,
        ]);

        $ay = AcademicYear::findOrFail($id);
        $ay->update([
            'year' => $request->year,
        ]);

        return back()->with('success', 'Tahun ajaran berhasil diperbarui.');
    }

    public function destroy($id)
    {
        AcademicYear::findOrFail($id)->delete();

        return back()->with('success', 'Tahun ajaran berhasil dihapus.');
    }

    public function toggleActive($id)
    {
        $year = AcademicYear::findOrFail($id);

        if ($year->is_active) {
            $year->update(['is_active' => false]);
            return back()->with('success', 'Tahun ajaran dinonaktifkan.');
        }

        AcademicYear::query()->update(['is_active' => false]);
        $year->update(['is_active' => true]);

        return back()->with('success', 'Tahun ajaran berhasil diaktifkan.');
    }
}
