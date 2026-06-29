<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReportPeriodeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'export' => 'nullable|in:pdf,excel',
        ];
    }

    public function messages()
    {
        return [
            'start_date.date' => 'Tanggal mulai harus berupa tanggal yang valid.',
            'end_date.date' => 'Tanggal akhir harus berupa tanggal yang valid.',
            'export.in' => 'Tipe export tidak valid.',
        ];
    }
}
