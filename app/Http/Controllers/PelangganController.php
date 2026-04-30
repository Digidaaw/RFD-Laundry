<?php

namespace App\Http\Controllers;

use App\Http\Requests\PelangganStoreRequest;
use App\Http\Requests\PelangganUpdateRequest;
use App\Models\Pelanggan; // Pastikan model Pelanggan di-import
use Illuminate\Http\Request;

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

        $pelanggans = $query->paginate(10)->appends(['search' => $search, 'sort' => $sort]);

        return view('shared.pelanggan', compact('pelanggans', 'search', 'sort'));
    }

    public function store(PelangganStoreRequest $request)
    {
        Pelanggan::create($request->validated());

        return redirect()->route('pelanggan.index')->with('success', 'Pelanggan baru berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PelangganUpdateRequest $request, Pelanggan $pelanggan)
    {
        $pelanggan->update($request->validated());

        return redirect()->route('pelanggan.index')->with('success', 'Data pelanggan berhasil diperbarui.');
    }

}
