<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\SppRate;
use Illuminate\Http\Request;

class SppRateController extends Controller
{
    public function index()
    {
        $rates = SppRate::with('academicYear')->latest()->get();
        $years = AcademicYear::orderBy('year', 'asc')->get();

        return view('admin.spp.index', compact('years','rates'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'academic_year_id' => 'required',
            'amount' => 'required|integer',
            'description' => 'nullable'
        ]);
        SPPRate::create($request->all());

        return back()->with('success', 'Nominal berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|integer',
            'description' => 'nullable'
        ]);

        $rate = SPPRate::findOrFail($id);
        $rate->update($request->only('amount', 'description'));

        return back()->with('success', 'Nominal berhasil diperbarui.');
    }

    public function destroy($id)
    {
        SPPRate::findOrFail($id)->delete();
        return back()->with('success', 'Data berhasil dihapus.');
    }
}
