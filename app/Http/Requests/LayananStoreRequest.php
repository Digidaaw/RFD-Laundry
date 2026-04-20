<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LayananStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'deskripsi' => 'required|string|min:5|max:1000',
            'units' => 'required|array|min:1',
            'units.*.unit_satuan' => 'required|in:kg,pcs,meter',
            'units.*.harga' => 'required|numeric|min:0',
            'gambar' => 'required|array|min:1',
            'gambar.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }

    public function messages()
    {
        return [
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
        ];
    }
}
