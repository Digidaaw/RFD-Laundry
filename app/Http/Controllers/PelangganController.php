<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan; // Pastikan model Pelanggan di-import
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PelangganController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // PERBAIKAN: Ambil semua data dari model Pelanggan
        $pelanggans = Pelanggan::latest()->get();

        // PERBAIKAN: Kirim variabel $pelanggans ke view menggunakan compact()
        return view('shared.pelanggan', compact('pelanggans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'kontak' => 'required|string|max:20|unique:pelanggans',
        ], [
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
                'string',
                'max:20',
                Rule::unique('pelanggans')->ignore($pelanggan->id),
            ],
        ], [
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
