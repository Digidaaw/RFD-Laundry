<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kasirs = User::where('role', 'kasir')->get(); // Sesuaikan nama model & role
        return view('admin.kasir', compact('kasirs')); // Sesuaikan nama view
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|min:4|max:255|unique:users',
            'password' => 'required|string|min:6|regex:/[A-Z]/|regex:/[@$!%*#?&]/',
            'role' => 'required|string',

        ], [
            'username.unique' => 'Username sudah digunakan.',
            'password.regex' => 'Password harus memiliki huruf kapital dan karakter spesial.',
        ]);

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'plain_password' => $request->password, // Simpan password asli
            'role' => $request->role ?? 'kasir',
        ]);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
    }




    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            // Pastikan username unik, tapi abaikan untuk user yang sedang diedit
            'username' => [
                'required',
                'string',
                'min:4',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            // Password bersifat opsional saat update
            'password' => 'nullable|string|min:6',
        ], [
            'username.unique' => 'Username sudah digunakan oleh user lain.',
            'password.min' => 'Password baru minimal harus 6 karakter.',
        ]);

        // Siapkan data untuk diupdate
        $updateData = [
            'name' => $request->name,
            'username' => $request->username,
        ];

        // Jika password diisi, maka hash dan update passwordnya
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
            $updateData['plain_password'] = $request->password; // Simpan juga saat update

        }

        $user->update($updateData);

        return redirect()->route('users.index')->with('success', 'Data user kasir berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        try {
            $user->delete();
            return redirect()->route('users.index')->with('success', 'User kasir berhasil dihapus.');
        } catch (\Exception $e) {
            // Menangani kemungkinan error jika user tidak bisa dihapus (misal karena relasi)
            return redirect()->route('users.index')->with('error', 'Gagal menghapus user.');
        }
    }
}
