<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan; // Pastikan model Pelanggan di-import
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PelangganController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sort = $request->input('sort', 'updated_latest');

        $query = Pelanggan::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('kontak', 'like', "%{$search}%");
            });
        }

        if ($sort === 'updated_oldest') {
            $query->orderBy('updated_at', 'asc');
        } else {
            $query->orderBy('updated_at', 'desc');
        }

        $pelanggans = $query->paginate(5)->appends(['search' => $search, 'sort' => $sort]);

        return view('shared.pelanggan', compact('pelanggans', 'search', 'sort'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'kontak' => 'required|numeric|digits_between:10,13|unique:pelanggans',
        ], [
            'name.required' => 'Nama pelanggan harus diisi.',
            'name.string' => 'Nama pelanggan harus berupa teks.',
            'name.max' => 'Nama pelanggan maksimal 255 karakter.',
            'kontak.required' => 'Nomor kontak harus diisi.',
            'kontak.numeric' => 'Nomor kontak harus berupa angka.',
            'kontak.digits_between' => 'Nomor kontak harus antara 10 - 13 digit.',
            'kontak.unique' => 'Nomor kontak sudah terdaftar.',
        ]);

        Pelanggan::create($request->all());

        return redirect()->route('pelanggan.index')->with('success', 'Pelanggan baru berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pelanggan $pelanggan)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'kontak' => [
                'required',
                'numeric',
                'digits_between:10,13',
                Rule::unique('pelanggans')->ignore($pelanggan->id),
            ],
        ], [
            'name.required' => 'Nama pelanggan harus diisi.',
            'name.string' => 'Nama pelanggan harus berupa teks.',
            'name.max' => 'Nama pelanggan maksimal 255 karakter.',
            'kontak.required' => 'Nomor kontak harus diisi.',
            'kontak.numeric' => 'Nomor kontak harus berupa angka.',
            'kontak.digits_between' => 'Nomor kontak harus antara 10 - 13 digit.',
            'kontak.unique' => 'Nomor kontak sudah digunakan oleh pelanggan lain.',
        ]);

        $pelanggan->update($request->all());

        return redirect()->route('pelanggan.index')->with('success', 'Data pelanggan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pelanggan $pelanggan)
    {
        try {
            $pelanggan->delete();
            return redirect()->route('pelanggan.index')->with('success', 'Data pelanggan berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('pelanggan.index')->with('error', 'Gagal menghapus pelanggan.');
        }
    }
}
