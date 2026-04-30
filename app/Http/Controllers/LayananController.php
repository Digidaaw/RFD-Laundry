<?php

namespace App\Http\Controllers;

use App\Http\Requests\LayananStoreRequest;
use App\Http\Requests\LayananUpdateRequest;
use App\Models\Layanan;
use App\Models\LayananUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

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

        $layanans = $query->with('units')->paginate(10)->appends(['search' => $search, 'sort' => $sort]);

        return view('admin.layanan', compact('layanans', 'search', 'sort'));
    }

    public function store(LayananStoreRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $layanan = Layanan::create([
                'name' => $request->name,
                'deskripsi' => $request->deskripsi,
                'gambar' => $this->uploadImages($request->file('gambar')),
            ]);

            $this->syncUnits($layanan, $request->input('units'));

            return redirect()->route('layanan.index')
                ->with('success', 'Layanan berhasil ditambahkan.');
        });
    }

    public function update(LayananUpdateRequest $request, Layanan $layanan)
    {
        return DB::transaction(function () use ($request, $layanan) {
            $currentImages = $layanan->gambar ?? [];

            if ($request->filled('images_to_delete')) {
                foreach ($request->input('images_to_delete') as $imageName) {
                    $path = public_path('images/layanan/' . $imageName);
                    if (File::exists($path)) {
                        File::delete($path);
                    }
                }
                $currentImages = array_values(array_diff($currentImages, $request->input('images_to_delete')));
            }

            if ($request->hasFile('gambar')) {
                $currentImages = array_merge($currentImages, $this->uploadImages($request->file('gambar')));
            }

            $layanan->update([
                'name' => $request->name,
                'deskripsi' => $request->deskripsi,
                'gambar' => array_values($currentImages),
            ]);

            $this->syncUnits($layanan, $request->input('units'));

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

    private function uploadImages(array $images): array
    {
        $fileNames = [];

        foreach ($images as $image) {
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('images/layanan'), $imageName);
            $fileNames[] = $imageName;
        }

        return $fileNames;
    }

    private function syncUnits(Layanan $layanan, array $units): void
    {
        $layanan->units()->delete();

        foreach ($units as $unit) {
            $layanan->units()->create([
                'unit_satuan' => $unit['unit_satuan'],
                'harga' => $unit['harga'],
            ]);
        }
    }
}
