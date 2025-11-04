<?php

namespace App\Http\Controllers;

use App\Models\Layanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class LayananController extends Controller
{
    public function index()
    {
        $layanans = Layanan::latest()->get();
        return view('admin.layanan', compact('layanans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'gambar' => 'required|array',
            'gambar.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $imageNames = [];
        if ($request->hasFile('gambar')) {
            foreach ($request->file('gambar') as $image) {
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('images/layanan'), $imageName);
                $imageNames[] = $imageName;
            }
        }

        Layanan::create([
            'name' => $request->name,
            'harga' => $request->harga,
            'deskripsi' => $request->deskripsi,
            'gambar' => $imageNames,
        ]);

        return redirect()->route('layanan.index')->with('success', 'Layanan berhasil ditambahkan.');
    }


    public function update(Request $request, Layanan $layanan)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|array',
            'gambar.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'images_to_delete' => 'nullable|array',
            'images_to_delete.*' => 'string',
        ]);

        $updateData = $request->only('name', 'harga', 'deskripsi');
        $currentImages = $layanan->gambar ?? [];

        // 1. Hapus gambar yang ditandai untuk dihapus
        if ($request->has('images_to_delete')) {
            $imagesToDelete = $request->input('images_to_delete');
            foreach ($imagesToDelete as $imageName) {
                $imagePath = public_path('images/layanan/' . $imageName);
                if (File::exists($imagePath)) {
                    File::delete($imagePath);
                }
            }
            $currentImages = array_diff($currentImages, $imagesToDelete);
        }

        // 2. Tambahkan gambar baru jika ada
        if ($request->hasFile('gambar')) {
            foreach ($request->file('gambar') as $image) {
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('images/layanan'), $imageName);
                $currentImages[] = $imageName;
            }
        }

        $updateData['gambar'] = array_values($currentImages); // Re-index array

        $layanan->update($updateData);

        return redirect()->route('layanan.index')->with('success', 'Layanan berhasil diperbarui.');
    }

    // ... (method destroy tidak berubah) ...
    public function destroy(Layanan $layanan)
    {
        try {
            if ($layanan->gambar) {
                foreach ($layanan->gambar as $image) {
                    $imagePath = public_path('images/layanan/' . $image);
                    if (File::exists($imagePath)) {
                        File::delete($imagePath);
                    }
                }
            }
            $layanan->delete();
            return redirect()->route('layanan.index')->with('success', 'Layanan berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->route('layanan.index')->with('error', 'Gagal menghapus layanan.');
        }
    }
}
