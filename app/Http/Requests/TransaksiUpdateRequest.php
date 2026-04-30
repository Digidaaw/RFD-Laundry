<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransaksiUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id_pelanggan' => 'required|exists:pelanggans,id',
            'tanggal_order' => 'required|date',
            'deskripsi' => 'nullable|string|max:255',
            'jumlah_bayar' => 'required|numeric|min:0',
        ];
    }

    public function messages()
    {
        return [
            'id_pelanggan.required' => 'Pelanggan harus dipilih.',
            'id_pelanggan.exists' => 'Pelanggan yang dipilih tidak ditemukan.',
            'tanggal_order.required' => 'Tanggal order harus diisi.',
            'tanggal_order.date' => 'Tanggal order harus valid.',
            'deskripsi.string' => 'Deskripsi harus berupa teks.',
            'deskripsi.max' => 'Deskripsi maksimal 255 karakter.',
            'jumlah_bayar.required' => 'Jumlah bayar harus diisi.',
            'jumlah_bayar.numeric' => 'Jumlah bayar harus berupa angka.',
            'jumlah_bayar.min' => 'Jumlah bayar tidak boleh negatif.',
        ];
    }
}
