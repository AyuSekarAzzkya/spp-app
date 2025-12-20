<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key')->toArray();

        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'school_name' => 'required',
            'school_address' => 'required',
            'school_phone' => 'required',
            'spp_default' => 'required|numeric',
            'year_active' => 'required',
            'receipt_footer' => 'nullable',
        ]);

        foreach ($request->except('_token') as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return redirect()->back()->with('success', 'Pengaturan berhasil diperbarui!');
    }
}
