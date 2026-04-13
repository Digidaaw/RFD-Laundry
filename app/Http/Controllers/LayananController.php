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
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sort = $request->input('sort', 'updated_latest');

        $query = Layanan::query();

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($sort === 'updated_oldest') {
            $query->orderBy('updated_at', 'asc');
        } else {
            $query->orderBy('updated_at', 'desc');
        }

        $layanans = $query->with('units')->paginate(5)->appends(['search' => $search, 'sort' => $sort]);
        return view('admin.layanan', compact('layanans', 'search', 'sort'));
    }

    public function store(Request $request)
    {
    $request->validate([
        'name' => 'required|string|max:255',
        'deskripsi' => 'required|string|min:5|max:1000',
        'units' => 'required|array|min:1',
        'units.*.unit_satuan' => 'required|in:kg,pcs,meter',
        'units.*.harga' => 'required|numeric|min:0',
        'gambar' => 'required|array',
        'gambar.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ], [
        'name.required' => 'Nama layanan harus diisi.',
        'name.string' => 'Nama layanan harus berupa teks.',
        'name.max' => 'Nama layanan maksimal 255 karakter.',
        'units.required' => 'Satuan dan harga harus ditambahkan minimal 1.',
        'units.min' => 'Satuan dan harga harus ditambahkan minimal 1.',
        'units.*.unit_satuan.required' => 'Satuan harus dipilih.',
        'units.*.unit_satuan.in' => 'Satuan hanya boleh: kg, pcs, atau meter.',
        'units.*.harga.required' => 'Harga harus diisi.',
        'units.*.harga.numeric' => 'Harga harus berupa angka.',
        'units.*.harga.min' => 'Harga tidak boleh negatif.',
        'deskripsi.required' => 'Deskripsi harus diisi.',
        'deskripsi.string' => 'Deskripsi harus berupa teks.',
        'gambar.required' => 'Minimal 1 gambar harus diunggah.',
        'gambar.array' => 'Gambar harus berformat array.',
        'gambar.*.image' => 'File harus berupa gambar.',
        'gambar.*.mimes' => 'Format gambar: jpeg, png, jpg, gif, atau svg.',
        'gambar.*.max' => 'Ukuran gambar maksimal 2MB.',
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
        'deskripsi' => 'required|string|min:5|max:1000',
        'units' => 'required|array|min:1',
        'units.*.unit_satuan' => 'required|in:kg,pcs,meter',
        'units.*.harga' => 'required|numeric|min:0',
        'gambar' => 'nullable|array',
        'gambar.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'images_to_delete' => 'nullable|array',
    ], [
        'name.required' => 'Nama layanan harus diisi.',
        'name.string' => 'Nama layanan harus berupa teks.',
        'name.max' => 'Nama layanan maksimal 255 karakter.',
        'units.required' => 'Satuan dan harga harus ditambahkan minimal 1.',
        'units.min' => 'Satuan dan harga harus ditambahkan minimal 1.',
        'units.*.unit_satuan.required' => 'Satuan harus dipilih.',
        'units.*.unit_satuan.in' => 'Satuan hanya boleh: kg, pcs, atau meter.',
        'units.*.harga.required' => 'Harga harus diisi.',
        'units.*.harga.numeric' => 'Harga harus berupa angka.',
        'units.*.harga.min' => 'Harga tidak boleh negatif.',
        'deskripsi.required' => 'Deskripsi harus diisi.',
        'deskripsi.string' => 'Deskripsi harus berupa teks.',
        'gambar.array' => 'Gambar harus berformat array.',
        'gambar.*.image' => 'File harus berupa gambar.',
        'gambar.*.mimes' => 'Format gambar: jpeg, png, jpg, gif, atau svg.',
        'gambar.*.max' => 'Ukuran gambar maksimal 2MB.',
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

    public function destroy(Layanan $layanan)
    {
        try {
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
