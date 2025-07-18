<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisterController extends Controller
{
    /**
     * Menampilkan halaman registrasi.
     */
    public function index()
    {
        return view('auth.register');
    }

    /**
     * Menyimpan user baru ke database.
     */
    public function store(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', 'in:admin,kasir'], // Memastikan role yang diisi valid
        ]);

        // 2. Buat user baru
        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'plain_password' => $request->password, // Menyimpan password asli jika masih diperlukan
            'role' => $request->role,
        ]);

        // 3. Login-kan user yang baru dibuat
        Auth::login($user);

        // 4. Arahkan ke halaman utama (dashboard)
        return redirect('/');
    }
}
