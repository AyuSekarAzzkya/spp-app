<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{
    /* =============================
     * FORM LOG IN
     * ============================= */
    public function showLogin()
    {
        return view('auth.login');
    }

    /* =============================
     * PROSES LOGIN
     * ============================= */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            // redirect sesuai role
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }

            if ($user->role === 'petugas') {
                return redirect()->route('petugas.dashboard');
            }

            if ($user->role === 'siswa') {
                return redirect()->route('student.dashboard');
            }
            
        }

        return back()->withErrors([
            'email' => 'Email atau password salah'
        ]);
    }
    /* =============================
     * LOGOUT
     * ============================= */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
