<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function student()
    {
        return view('student.dashboard.index');
    }

    public function petugas()
    {
        return view('dashboard.petugas');
    }

    public function admin()
    {
        return view('admin.dashboard.index');
    }
}
