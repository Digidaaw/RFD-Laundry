<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransaksiStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'id_pelanggan' => 'required|exists:pelanggans,id',
            'potongan' => 'nullable|numeric|min:0',
            'jumlah_bayar' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string|max:255',
        ];

        if ($this->has('items')) {
            $rules = array_merge($rules, [
                'items' => 'required|array|min:1',
                'items.*.id_layanan' => 'required|exists:layanans,id',
                'items.*.unit_satuan' => 'required|in:kg,pcs,meter',
                'items.*.qty' => 'required|numeric|min:0.1|max:999.9',
            ]);
        } else {
            $rules = array_merge($rules, [
                'id_layanan' => 'required|exists:layanans,id',
                'qty' => 'required|numeric|min:0.1|max:999.9',
            ]);
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'id_pelanggan.required' => 'Pelanggan harus dipilih.',
            'id_pelanggan.exists' => 'Pelanggan yang dipilih tidak ditemukan.',
            'items.required' => 'Minimal 1 layanan harus ditambahkan.',
            'items.min' => 'Minimal 1 layanan harus ditambahkan.',
            'items.*.id_layanan.required' => 'Layanan harus dipilih.',
            'items.*.id_layanan.exists' => 'Layanan yang dipilih tidak ditemukan.',
            'items.*.unit_satuan.required' => 'Satuan harus dipilih.',
            'items.*.unit_satuan.in' => 'Satuan hanya boleh: kg, pcs, atau meter.',
            'items.*.qty.required' => 'Qty harus diisi.',
            'items.*.qty.numeric' => 'Qty harus berupa angka.',
            'items.*.qty.min' => 'Qty minimal 0.1.',
            'items.*.qty.max' => 'Qty maksimal 999.9.',
            'id_layanan.required' => 'Layanan harus dipilih.',
            'id_layanan.exists' => 'Layanan yang dipilih tidak ditemukan.',
            'qty.required' => 'Qty harus diisi.',
            'qty.numeric' => 'Qty harus berupa angka.',
            'qty.min' => 'Qty minimal 0.1.',
            'qty.max' => 'Qty maksimal 999.9.',
            'potongan.numeric' => 'Potongan harus berupa angka.',
            'potongan.min' => 'Potongan tidak boleh negatif.',
            'jumlah_bayar.required' => 'Jumlah bayar harus diisi.',
            'jumlah_bayar.numeric' => 'Jumlah bayar harus berupa angka.',
            'jumlah_bayar.min' => 'Jumlah bayar tidak boleh negatif.',
            'deskripsi.string' => 'Deskripsi harus berupa teks.',
            'deskripsi.max' => 'Deskripsi maksimal 255 karakter.',
        ];
    }
}
