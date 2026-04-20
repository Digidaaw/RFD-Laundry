<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

class UserStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'min:4', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', 'string', 'in:admin,kasir'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Nama kasir harus diisi.',
            'name.string' => 'Nama kasir harus berupa teks.',
            'name.max' => 'Nama kasir maksimal 255 karakter.',
            'username.required' => 'Username harus diisi.',
            'username.string' => 'Username harus berupa teks.',
            'username.min' => 'Username minimal 4 karakter.',
            'username.max' => 'Username maksimal 255 karakter.',
            'username.unique' => 'Username sudah terdaftar.',
            'password.required' => 'Password harus diisi.',
            'password.string' => 'Password harus berupa teks.',
            'password.min' => 'Password minimal 6 karakter.',
            'role.required' => 'Role harus dipilih.',
            'role.in' => 'Role tidak valid.',
        ];
    }
}
