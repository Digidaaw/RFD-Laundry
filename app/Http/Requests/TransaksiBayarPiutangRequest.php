<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransaksiBayarPiutangRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $transaksi = $this->route('transaksi');

        return [
            'bayar_sekarang' => [
                'required',
                'numeric',
                'min:0.01',
                'lte:' . ($transaksi ? $transaksi->sisa_bayar : '0'),
            ],
        ];
    }

    public function messages()
    {
        return [
            'bayar_sekarang.required' => 'Jumlah pembayaran harus diisi.',
            'bayar_sekarang.numeric' => 'Jumlah pembayaran harus berupa angka.',
            'bayar_sekarang.min' => 'Pembayaran minimal 0.01.',
            'bayar_sekarang.lte' => 'Pembayaran tidak boleh melebihi sisa hutang.',
        ];
    }
}
