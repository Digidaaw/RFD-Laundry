<?php

namespace App\Http\Controllers;

use App\Models\Layanan;
use App\Models\LayananUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class LayananController extends Controller
{
    public function index()
    {
        $layanans = Layanan::with('units')->latest()->get();
        return view('admin.layanan', compact('layanans'));
    }

    public function store(Request $request)
    {
    $request->validate([
        'name' => 'required|string|max:255',
        'deskripsi' => 'nullable|string',

        // MULTI UNIT
        'units' => 'required|array|min:1',
        'units.*.unit_satuan' => 'required|in:kg,pcs,meter',
        'units.*.harga' => 'required|numeric|min:0',

        // GAMBAR
        'gambar' => 'required|array',
        'gambar.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ]);

    return DB::transaction(function () use ($request) {

        // UPLOAD GAMBAR
        $imageNames = [];
        if ($request->hasFile('gambar')) {
            foreach ($request->file('gambar') as $image) {
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('images/layanan'), $imageName);
                $imageNames[] = $imageName;
            }
        }

        // SIMPAN LAYANAN
        $layanan = Layanan::create([
            'name' => $request->name,
            'deskripsi' => $request->deskripsi,
            'gambar' => $imageNames,
        ]);

        // SIMPAN UNITS
        foreach ($request->units as $unit) {
            $layanan->units()->create([
                'unit_satuan' => $unit['unit_satuan'],
                'harga' => $unit['harga'],
            ]);
        }

        return redirect()->route('layanan.index')
            ->with('success', 'Layanan berhasil ditambahkan.');
    });
}

    public function update(Request $request, Layanan $layanan)
    {
    $request->validate([
        'name' => 'required|string|max:255',
        'deskripsi' => 'nullable|string',

        'units' => 'required|array|min:1',
        'units.*.unit_satuan' => 'required|in:kg,pcs,meter',
        'units.*.harga' => 'required|numeric|min:0',

        'gambar' => 'nullable|array',
        'gambar.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'images_to_delete' => 'nullable|array',
    ]);

    return DB::transaction(function () use ($request, $layanan) {

        $updateData = $request->only('name', 'deskripsi');
        $currentImages = $layanan->gambar ?? [];

        // HAPUS GAMBAR
        if ($request->has('images_to_delete')) {
            foreach ($request->images_to_delete as $img) {
                $path = public_path('images/layanan/' . $img);
                if (File::exists($path)) File::delete($path);
            }
            $currentImages = array_diff($currentImages, $request->images_to_delete);
        }

        // TAMBAH GAMBAR
        if ($request->hasFile('gambar')) {
            foreach ($request->file('gambar') as $image) {
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('images/layanan'), $imageName);
                $currentImages[] = $imageName;
            }
        }

        $updateData['gambar'] = array_values($currentImages);
        $layanan->update($updateData);

        // 🔥 RESET & INSERT ULANG UNITS
        $layanan->units()->delete();

        foreach ($request->units as $unit) {
            $layanan->units()->create([
                'unit_satuan' => $unit['unit_satuan'],
                'harga' => $unit['harga'],
            ]);
        }

        return redirect()->route('layanan.index')
            ->with('success', 'Layanan berhasil diperbarui.');
    });
}

    // ... (method destroy tidak berubah) ...
    public function destroy(Layanan $layanan)
    {
        try {
            // CUKUP JALANKAN INI. 
            // Jangan hapus File fisiknya agar riwayat transaksi tetap bisa melihat gambar (opsional, tapi disarankan).
            // Data di database hanya akan ditandai deleted_at = tanggal sekarang.
            $layanan->delete(); 
            
            return redirect()->route('layanan.index')->with('success', 'Layanan berhasil dihapus (disembunyikan).');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->route('layanan.index')->with('error', 'Gagal menghapus layanan.');
        }
    }

    public function show(Layanan $layanan)
    {
        return view('admin.layanan_show', compact('layanan'));
        
    }

    public function units() {
    return $this->hasMany(LayananUnit::class);
    }
}
