<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule; // <-- PERBAIKAN: TAMBAHKAN BARIS INI

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sort = $request->input('sort', 'updated_latest');

        $query = User::where('role', 'kasir');

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($sort === 'updated_oldest') {
            $query->orderBy('updated_at', 'asc');
        } else {
            $query->orderBy('updated_at', 'desc');
        }

        $kasirs = $query->paginate(10)->appends(['search' => $search, 'sort' => $sort]);
        return view('admin.kasir', compact('kasirs', 'search', 'sort'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|min:4|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|string',
        ], [
            'name.required' => 'Nama kasir harus diisi.',
            'name.string' => 'Nama kasir harus berupa teks.',
            'name.max' => 'Nama kasir maksimal 255 karakter.',
            'username.required' => 'Username harus diisi.',
            'username.string' => 'Username harus berupa teks.',
            'username.min' => 'Username minimal 4 karakter.',
            'username.max' => 'Username maksimal 255 karakter.',
            'username.unique' => 'Username sudah terdaftar.',
            'password.required' => 'Password harus diisi.',
            'password.string' => 'Password harus berupa teks.',
            'password.min' => 'Password minimal 6 karakter.',
            'role.required' => 'Role harus dipilih.',
        ]);

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'plain_password' => $request->password, // Simpan password asli
            'role' => $request->role,
        ]);

        return redirect()->route('users.index')->with('success', 'User kasir berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => [
                'required',
                'string',
                'min:4',
                'max:255',
                Rule::unique('users')->ignore($user->id), // Baris ini yang membutuhkan 'use Illuminate\Validation\Rule;'
            ],
            'password' => 'nullable|string|min:6', // Password boleh kosong saat update
        ], [
            'name.required' => 'Nama kasir harus diisi.',
            'name.string' => 'Nama kasir harus berupa teks.',
            'name.max' => 'Nama kasir maksimal 255 karakter.',
            'username.required' => 'Username harus diisi.',
            'username.string' => 'Username harus berupa teks.',
            'username.min' => 'Username minimal 4 karakter.',
            'username.max' => 'Username maksimal 255 karakter.',
            'username.unique' => 'Username sudah digunakan oleh kasir lain.',
            'password.string' => 'Password harus berupa teks.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

        // Siapkan data untuk diupdate
        $updateData = [
            'name' => $request->name,
            'username' => $request->username,
        ];

        // Hanya update password jika diisi
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
            $updateData['plain_password'] = $request->password;
        }

        $user->update($updateData);

        return redirect()->route('users.index')->with('success', 'Data kasir berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        try {
            $user->delete();
            return redirect()->route('users.index')->with('success', 'Data kasir berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('users.index')->with('error', 'Gagal menghapus kasir.');
        }
    }
}