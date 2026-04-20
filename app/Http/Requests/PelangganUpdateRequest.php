<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PelangganUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'kontak' => ['required', 'numeric', 'digits_between:10,13', Rule::unique('pelanggans')->ignore($this->route('pelanggan')->id)],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Nama pelanggan harus diisi.',
            'name.string' => 'Nama pelanggan harus berupa teks.',
            'name.max' => 'Nama pelanggan maksimal 255 karakter.',
            'kontak.required' => 'Nomor kontak harus diisi.',
            'kontak.numeric' => 'Nomor kontak harus berupa angka.',
            'kontak.digits_between' => 'Nomor kontak harus antara 10 - 13 digit.',
            'kontak.unique' => 'Nomor kontak sudah digunakan oleh pelanggan lain.',
        ];
    }
}
