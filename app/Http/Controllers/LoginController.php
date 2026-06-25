<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Menampilkan halaman login.
     */
    public function index()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    /**
     * Menangani proses autentikasi.
     */
    public function authenticate(LoginRequest $request)
    {
        $user = \App\Models\User::where('username', $request->username)->first();

        if ($user && !$user->is_active) {
            return back()->withErrors([
                'username' => 'Akun Anda dinonaktifkan. Silakan hubungi admin.',
            ])->onlyInput('username');
        }

        // Mencoba untuk login
        if (Auth::attempt($request->only(['username', 'password']))) {
            $request->session()->regenerate();

            // Jika berhasil, arahkan ke dashboard
            return redirect()->route('dashboard');
        }

        // Jika gagal, kembali ke halaman login dengan pesan error
        return back()->withErrors([
            'username' => 'Username atau password yang diberikan tidak cocok.',
        ])->onlyInput('username');
    }

    /**
     * Menangani proses sign out.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
