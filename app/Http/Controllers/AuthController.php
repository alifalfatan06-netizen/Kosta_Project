<?php
// ANGGOTA 1 Mahendra (fondasi)
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function form()
    {
        return view('auth.login');
    }

    public function proses(Request $request)
    {
        $data = $request->validate(['email' => 'required|email', 'password' => 'required']);

        // is_active => true: akun mantan penghuni tidak bisa login
        if (Auth::attempt($data + ['is_active' => true], $request->boolean('ingat'))) {
            $request->session()->regenerate();

            return Auth::user()->role === 'admin'
                ? redirect()->route('admin.dashboard')
                : redirect()->route('portal.index');
        }

        return back()->withErrors(['email' => 'Email/password salah atau akun nonaktif.'])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('katalog');
    }
}